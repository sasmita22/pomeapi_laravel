<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Staff;
use carbon\Carbon;


class StaffController extends Controller
{

    public function qrShowJob($id,$id_project)
    {
        
        $project = DB::table('projects')
        ->select(
            DB::raw(
            'id_project,
            name,
            deskripsi,
            client,
            project_manager,
            image,
            start_at,
            ended_at,
            deadline_at,
            status,"Project Manager" as jabatan,0 AS "position_id",0 AS "step_work_on"')
        )        
        ->where('project_manager',$id)
        ->where('id_project',$id_project);


        

        $step = DB::table('projects as p')
        ->select(
            DB::raw('
            p.id_project,
            p.name,
            p.deskripsi,
            p.client,
            p.project_manager,
            p.image,
            p.start_at,
            p.ended_at,
            p.deadline_at,
            p.status,CONCAT(s.name," Leader") AS "jabatan",1 AS "position_id",ps.step AS "step_work_on"')
        )        
        ->join('project_structures as ps','ps.id_project','p.id_project')
        ->join('steps as s','s.id','ps.step')
        ->where('ps.leader',($id))
        ->where('p.id_project',$id_project);
        
        

        $task = DB::table('projects as p')
        ->select(
            DB::raw('
            p.id_project,
            p.name,
            p.deskripsi,
            p.client,
            p.project_manager,
            p.image,
            p.start_at,
            p.ended_at,
            p.deadline_at,
            p.status,CONCAT(s.name," Staff") AS "jabatan",2 AS "position_id",ps.step AS "step_work_on"')
        )           
        ->join('project_structures as ps','ps.id_project','p.id_project')
        ->join('steps as s','s.id','ps.step')
        ->join('project_structure_staff as pss','pss.id_project_structure','ps.id')
        ->where('pss.staff',($id))
        ->where('ps.id_project',$id_project)      
        ->union($project)
        ->union($step)
        ->get();       
        
        
        

        $ntask = count($task);
        for ($i=0; $i < $ntask ; $i++) { 
            $task[$i]->progress = $this->progressProject($task[$i]->id_project);
            $task[$i]->start_at = Carbon::parse($task[$i]->start_at)->format('d-m-Y');
            $task[$i]->deadline_at = Carbon::parse($task[$i]->deadline_at)->format('d-m-Y');
            $pm_name = Staff::find($task[$i]->project_manager);
            $task[$i]->project_manager = $pm_name->name;
            
        }

        $result = $task;
        if(count($result)==0){
            $result = DB::table('projects')
            ->where('id_project',$id_project)
            ->get([
                'id_project',
                'name',
                'deskripsi',
                'client',
                'project_manager',
                'image',
                'start_at',
                'ended_at',
                'deadline_at',
                'status',
            ]);
            $result[0]->jabatan = "Tidak Ada";
            $result[0]->position_id = -1;
            $result[0]->step_work_on = 0;
            $result[0]->start_at = Carbon::parse($result[0]->start_at)->format('d-m-Y');
            $result[0]->deadline_at = Carbon::parse($result[0]->deadline_at)->format('d-m-Y');            
            $result[0]->progress = $this->progressProject($id_project);
            return response()->json($result[0],200);
        }
        return response()->json($result[0],200);
    }    


    public function progressProject($id_project){
            
        $project1 = DB::table('project_structures as p')
        ->join('tasks as t','t.project_structure','p.id')
        ->where('p.id_project',$id_project)
        ->where('t.status',1)
        ->get();
        
        

        $project2 = DB::table('project_structures as p')
        ->join('tasks as t','t.project_structure','p.id')
        ->where('p.id_project',$id_project)
        ->get();

        $beres_project = count($project1);
        $total_project = count($project2);

        if($total_project == 0){
            $result = 0;
        }else{
            $result = floor($beres_project/$total_project * 100);
        }
        
        return $result;
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
        
        $result = floor($beres_project/$total_project * 100);
        return $result;
    }    
    
    public function getHirarkiProject($id)
    {
        $project = DB::table('projects')
        ->select(
            DB::raw(
            'id_project,
            name,
            deskripsi,
            client,
            project_manager,
            image,
            start_at,
            ended_at,
            deadline_at,
            status,"Project Manager" as jabatan,0 AS "position_id",0 AS "step_work_on"')
        )        
        ->where('project_manager',$id);

        $step = DB::table('projects as p')
        ->select(
            DB::raw('
            p.id_project,
            p.name,
            p.deskripsi,
            p.client,
            p.project_manager,
            p.image,
            p.start_at,
            p.ended_at,
            p.deadline_at,
            p.status,CONCAT(s.name," Leader") AS "jabatan",1 AS "position_id",ps.step AS "step_work_on"')
        )        
        ->join('project_structures as ps','ps.id_project','p.id_project')
        ->join('steps as s','s.id','ps.step')
        ->where('ps.leader',($id));

        $task = DB::table('projects as p')
        ->select(
            DB::raw('
            p.id_project,
            p.name,
            p.deskripsi,
            p.client,
            p.project_manager,
            p.image,
            p.start_at,
            p.ended_at,
            p.deadline_at,
            p.status,CONCAT(s.name," Staff") AS "jabatan",2 AS "position_id",ps.step AS "step_work_on"')
        )           
        ->join('project_structures as ps','ps.id_project','p.id_project')
        ->join('steps as s','s.id','ps.step')
        ->join('project_structure_staff as pss','pss.id_project_structure','ps.id')
        ->where('pss.staff',($id))
        ->union($project)
        ->union($step)
        ->get();       
        
        
        

        $ntask = count($task);
        for ($i=0; $i < $ntask ; $i++) { 
            $task[$i]->progress = $this->progressProject($task[$i]->id_project);
            $task[$i]->start_at = Carbon::parse($task[$i]->start_at)->format('d-m-Y');
            $task[$i]->deadline_at = Carbon::parse($task[$i]->deadline_at)->format('d-m-Y');
            $pm_name = Staff::find($task[$i]->project_manager);
            $task[$i]->project_manager = $pm_name->name;
            
        }

        $result = $task;
        return response()->json($result,200);
    }


    public function changeDateFormat($date){
        if ($date != null){
            return date("d-m-Y", strtotime($date));
        }
        return null;
    }

    public function chooseLeader($id){
	        
        $staff = DB::table('projects as p')
        ->join('project_structures as ps','ps.id_project','=','p.id_project')
        ->join('project_structure_staff as pss','pss.id_project_structure','=','ps.id')
        ->join('staff as s','s.nip','=','pss.staff')
        ->where('p.id_project',$id)
        ->pluck('s.nip');
    
        $pm = DB::table('projects')
        ->where('id_project',$id)
        ->get();
    
    
        $query = DB::table('staff')
        ->whereNotin('nip',$staff)
        ->get();
    
        return response()->json($query,200);
    }

    public function getStaffOnStep($id_project,$id_step){
        $query = DB::table('projects as p')
        ->join('project_structures as ps','ps.id_project','p.id_project')
        ->join('project_structure_staff as pss','pss.id_project_structure','ps.id')
        ->join('staff as s','s.nip','pss.staff')
        ->where('p.id_project',$id_project)
        ->where('ps.step',$id_step)
        ->get();       

        return response()->json($query,200);
    }

    public function getHirarkiStep($id_project){
        $query = DB::table('project_structures as ps')
        ->where('ps.id_project',$id_project)
        ->get(['ps.id']);
        
        $steps = array();
        foreach ($query as $value) {
            $objStep = new \stdClass;
            $step = DB::table('project_structures as ps')
            ->join('steps as st','st.id','ps.step')
            ->where('ps.id',$value->id)
            ->get(['st.id','st.name','ps.deskripsi','ps.deadline_at','ps.ended_at']);
            
            
            $objStep->id = $step[0]->id;
            $objStep->name = $step[0]->name;
            $objStep->deskripsi = $step[0]->deskripsi;
            $objStep->deadline_at = date("d-m-Y", strtotime($step[0]->deadline_at));
            $objStep->ended_at = date("d-m-Y", strtotime($step[0]->ended_at));
    
    
            $queryTask = DB::table('project_structures as ps')
            ->join('tasks as t','t.project_structure','ps.id')
            ->where('ps.id',$value->id)
            ->get();
    
    
        
            $tasks = array();
            
    
            
            
            foreach ($queryTask as $key => $s) {
                
                $objTask = new \stdClass;
                $task = DB::table('tasks')
                ->where('id',$s->id)
                ->get();
    
                    
                $objTask->id 	= $task[0]->id;
                $objTask->name = $task[0]->name;
                $objTask->deskripsi = $task[0]->deskripsi;
                $objTask->status = $task[0]->status;		
                $objTask->project_structure = $task[0]->project_structure;
                $objTask->deadline_at = $task[0]->deadline_at;
                $objTask->finished_at = $task[0]->finished_at;
                $objTask->handled_by = $task[0]->handled_by;
    
                
                array_push($tasks,$objTask);
    
                
                
            }
            $objStep->tasks = $tasks;
            array_push($steps,$objStep);
        }
    
        
        return response()->json($steps,200);
        //return response()->json($query,200);
    }

    public function getStaff($nip)
    {
        $staff = Staff::where('nip',$nip)->firstOrFail();
        
        return response()->json($staff,200);
    }
    
        
}
