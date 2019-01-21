<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Step;
use App\ProjectStructure;
class StepController extends Controller
{
    public function createStep(Request $request,$project)
    {
        $this->validate($request,[
            'name' => 'required',
            'deskripsi' => 'required',
            'deadline_at' => 'required',
        ]);
        

        $name = $request->input('name');
        $deskripsi = $request->input('deskripsi');
        $deadline_at = $request->input('deadline_at');

        $step = new Step([
            'name' => $name,
            'deskripsi' => $deskripsi,
        ]);

        $step->save();

        $last_step = DB::table('steps')
        ->where('id',$step->id)
        ->get();
        

        $project_structure = new ProjectStructure([
            'id_project' => $project,
            'step' => $last_step[0]->id,
            'status' => 0,
            'deadline_at' => $deadline_at,
            'deskripsi' => $deskripsi,            
        ]);

        if($project_structure->save()){
            $message = [
                'msg' => 'step created',
                'step' => $step
            ];
            return response()->json($message,201);
        }

        $response = [
            'msg' => 'Error during creation',
        ];

        return response()->json($response,404);
    }


    public function progressStep($project,$step){
        $ps = DB::table('project_structures')
            ->where('id_project',$project)
            ->where('step',$step)
            ->get();
        
        $temp = $ps[0]->id;        
        
        $project = DB::table('project_structures as ps')
        ->join('tasks as t','t.project_structure','ps.id')
        ->where('t.project_structure',$temp)
        ->where('t.status',1)
        ->get();



        $project2 = DB::table('project_structures as ps')
        ->join('tasks as t','t.project_structure','ps.id')
        ->where('t.project_structure',$temp)
        ->get();

        $beres_project = count($project);
        $total_project = count($project2);
        if($total_project != 0){
            $result = floor($beres_project/$total_project * 100);
        }else{
            $result = 0;
        }
        
        return $result;
    }    

    public function getDetailStep($id_project,$id_step){
        $step = DB::table('project_structures as ps')
            ->join('steps as st','st.id','ps.step')
            ->where('ps.id_project',$id_project)
            ->where('ps.step',$id_step)
            ->get(['ps.id as id_project_structure', 'ps.step as id', 'st.name', 'ps.leader','ps.deskripsi', 'ps.leader','ps.deadline_at','ps.ended_at']);

        $step = $step[0];

        if ($step->leader != null){
            $leader = DB::table('staff as s')
            ->where('s.nip',$step->leader)
            ->get(['nip','name','jabatan','image']);
            $step->leader = $leader[0];
        }

        $staff = DB::table('project_structures as ps') 
        ->join('project_structure_staff as pss','pss.id_project_structure','ps.id')
        ->join('staff as s','s.nip','pss.staff')
        ->where('ps.id',$step->id_project_structure)
        ->get(['s.nip','s.name','s.jabatan', 's.email','image']);

        
        $step->team = $staff;
        $step->progress = $this->progressStep($id_project,$id_step);


        return response()->json($step,200);
    }
}       