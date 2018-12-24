<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Staff;


class StaffController extends Controller
{
    public function getHirarkiProject($id)
    {
        $query = DB::table('projects')
        ->join('project_structures','project_structures.id_project','=','projects.id_project')
        ->join('project_structure_staff','project_structure_staff.id_project_structure','=','project_structures.id')
        ->join('staff','staff.nip','=','project_structure_staff.staff')
        ->where('staff.nip',$id)
        ->orWhere('projects.project_manager',$id);
    
        
        
        $response = $query
        ->distinct()
        ->get(['projects.id_project','projects.name','projects.project_manager']);
        //->get(['projects.name','projects.project_manager','projects.start_at','projects.ended_at','projects.deadline_at','projects.status','projects.price']);
        
        $projects = array();
        
        
        foreach ($response as $value){
            $objProject = new \stdClass();
            $project = DB::table('projects')
            ->join('staff','staff.nip','=','projects.project_manager')
            ->where("id_project",$value->id_project)
            ->get(['id_project','projects.name AS projectname','projects.image','staff.nip AS nip_pm','staff.name AS project_manager',
            'projects.start_at','projects.deadline_at','projects.ended_at']);
            
            
    
            $objProject->id_project = $project[0]->id_project;
            $objProject->name = $project[0]->projectname;
            $objProject->image = $project[0]->image;
            $objProject->project_manager = $project[0]->project_manager;
            $objProject->start_at = $project[0]->start_at;
            $objProject->deadline_at = $project[0]->deadline_at;
            $objProject->ended_at = $project[0]->ended_at;
            $objProject->position_id = -1;
    
            if($project[0]->nip_pm == $id){
                $objProject->position = 'project manager';
                $objProject->position_id = 0;
            }
            
                
            $stepTemp = DB::table('project_structures')
            ->join('steps','steps.id','=','project_structures.step')
            ->where("project_structures.id_project",$value->id_project)
            ->get();
    
            $steps = array();
            foreach ($stepTemp as $s){
                $classStep = new \stdClass();
    
    
                $arrayStep = DB::table('steps as s')
                ->join("project_structures as ps","ps.step","s.id")
                ->where("s.id",$s->step)
                ->get();

                $arrayTeam = DB::table('staff')
                ->join('project_structure_staff', 'project_structure_staff.staff','=','staff.nip')
                ->join('project_structures','project_structures.id','=','project_structure_staff.id_project_structure')
                ->where('project_structures.step',$s->id)
                ->where('project_structures.id_project',$value->id_project)
                ->get(['staff.nip','staff.name']);

                if ($objProject->position_id == -1){
                    foreach ($arrayTeam as $ateam){
                        if ($id == $ateam->nip){
                            $objProject->position_id = $s->id;
                            $objProject->position = 'Staff '.$s->name;
                        }
                    }
                }
                
    
                $arrayTask = DB::table('tasks')
                ->where('project_structure',$s->id)
                ->get();
    
                $classStep->id = $arrayStep[0]->id;
                $classStep->name = $arrayStep[0]->name;
                $classStep->deskripsi = $arrayStep[0]->deskripsi;
                $classStep->deadline_at = $arrayStep[0]->deadline_at;
                $classStep->ended_at = $arrayStep[0]->ended_at;
                $classStep->team = $arrayTeam;
                $classStep->task = $arrayTask;
    
                array_push($steps,$classStep);
                   
            }
    
    
            $objProject->step = $steps;
    
    
            
            // $objProject->step->task = DB::table('project_structures')
            // ->join('tasks','tasks.project_structure','project_structure.step');
    
            array_push($projects,$objProject);
        }
    
    
        return response()->json($projects,200);
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
                $objTask->type = $task[0]->type;			
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
