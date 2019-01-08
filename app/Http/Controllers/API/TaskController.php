<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Task;
use App\Staff;
use App\ProjectStructure;
use App\ProjectStructureStaff;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function createTask(Request $request)
    {
        $this->validate($request,[
            'name' => 'required',
            'deskripsi' => 'required',
            'status' => 'required',
            'type' => 'required',
            'project_structure' => 'required',
            'deadline_at' => 'required',
            // 'finished_at' => 'required',
            // 'handled_by' => 'required'
        ]);
        


        $name = $request->input('name');
        $deskripsi = $request->input('deskripsi');
        $status = $request->input('status');
        $type = $request->input('type');
        $project_structure = $request->input('project_structure');
        $deadline_at = $request->input('deadline_at');
        // $finished_at = $request->input('finished_at');
        // $handled_by = $request->input('handled_by');


        $task = new Task([
            'name' => $name,
            'deskripsi' => $deskripsi,
            'status' => $status,
            'type' => $type,
            'project_structure' => $project_structure,
            'deadline_at' => $deadline_at,
            // 'finished_at' => $finished_at,
            // 'handled_by' => $handled_by,
        ]);

        if($task->save()){
            $task->task = [
                'href' => 'api/task/'.$task->id,
                'method' => 'GET'
            ];

            $message = [
                'msg' => 'project created',
                'task' => $task
            ];
            return response()->json($message,201);
        }

        $response = [
            'msg' => 'Error during creation',
        ];

        return response()->json($response,404);
    }

    public function getProjectManagerDashboard($nip){ 
        $query = DB::table('projects as p')
        ->select(DB::raw('t.id , t.deadline_at , p.name as project, s.name as step'))
        ->join('project_structures as ps','ps.id_project','p.id_project')
        ->join('steps as s','s.id','ps.step')
        ->join('tasks as t','t.project_structure','ps.id')
        ->where('p.project_manager',$nip)
        ->orderBy('t.deadline_at')
        ->get();

        return response()->json($query,200); 
    }
        
    public function getLeaderDashboard($nip){
        $query = DB::table('projects as p')
        ->select(DB::raw('t.id , t.deadline_at , p.name as project, s.name as step'))
        ->join('project_structures as ps','ps.id_project','p.id_project')
        ->join('steps as s','s.id','ps.step')
        ->join('tasks as t','t.project_structure','ps.id')
        ->where('ps.leader',$nip)
        ->orderBy('t.deadline_at')
        ->get();

        return response()->json($query,200); 
    } 
    
    public function getStaffDashboard($nip){
        $query = DB::table('projects as p')
        ->select(DB::raw('t.id , t.deadline_at , p.name as project, s.name as step'))
        ->join('project_structures as ps','ps.id_project','p.id_project')
        ->join('steps as s','s.id','ps.step')
        ->join('tasks as t','t.project_structure','ps.id')
        ->where('t.handled_by',$nip)
        ->orderBy('t.deadline_at')
        ->get();

        return response()->json($query,200); 
    }       

     
    public function getTask($id){
        // $task = Task::where('id',$id)->firstOrFail();

        // $staff = Staff::where('nip',$task->handled_by)
        //     ->firstOrFail(['nip','name','jabatan','image']);


        // $task->staff = $staff;

        $task = DB::table('tasks')
        ->where('id',$id)
        ->get();

        if(count($task)>0){
            $staff = DB::table('staff')
            ->where('nip',$task[0]->handled_by)
            ->get();
            
            if(count($staff)==0){
                $task[0]->staff = null;
            }else{
                $task[0]->staff = $staff;
            }
            
        }

        $task = $task[0];

        return response()->json($task,200);
    } 

    public function statusDone($id)
    {
        $task = Task::findOrFail($id);
        $task->status = 1;
        if(!$task->update()){
            return response()->json([
                'msg' => 'Error during update'
            ],404);
        }

        $response = [
            'msg' => 'Task status Done',
            'task' => $task
        ];

        return response()->json($response,200);
    }

    public function statusUndone($id)
    {
        $task = Task::findOrFail($id);
        $task->status = 0;
        if(!$task->update()){
            return response()->json([
                'msg' => 'Error during update'
            ],404);
        }


        $response = [
            'msg' => 'Task status Undone',
            'task' => $task
        ];

        return response()->json($response,200);
    }    

    public function getTasks($id_project,$id_step){
        $tasks = DB::table('tasks as t')
            ->where('project_structure',
                (DB::table('project_structures as ps')
                ->where('ps.id_project',$id_project)
                ->where('ps.step',$id_step)
                ->get(['ps.id']))[0]->id
            )
            ->get();


        return response()->json($tasks,200);
    }

    public function getDashboardStaff($nip){
        $task = DB::table('tasks as t')
            ->join('staff as st','t.handled_by','st.nip')
            ->join('project_structures as ps','t.project_structure','ps.id')
            ->join('steps as s','s.id','ps.step')
            ->join('projects as p','ps.id_project','p.id_project')
            ->where('st.nip',$nip)
            ->orderBy('t.deadline_at','asc')
            ->get(['t.id as id_task','t.name as task','t.deadline_at','s.id as id_itep','p.id_project as id_project']);

        return response()->json($task,200);
    }

    public function getDashboardLeader($nip){
        $task = DB::table('tasks as t')
            ->join('project_structures as ps','t.project_structure','ps.id')
            ->join('staff as st','ps.leader','st.nip')
            ->join('steps as s','s.id','ps.step')
            ->join('projects as p','ps.id_project','p.id_project')
            ->where('st.nip',$nip)
            ->orderBy('t.deadline_at','asc')
            ->get(['t.id as id_task','t.name as task','t.deadline_at','s.id as id_itep','p.id_project as id_project']);

        return response()->json($task,200);
    }

    public function getDashboardManager($nip){
        $task = DB::table('tasks as t')
            ->join('project_structures as ps','t.project_structure','ps.id')
            ->join('steps as s','s.id','ps.step')
            ->join('projects as p','ps.id_project','p.id_project')
            ->join('staff as st','p.project_manager','st.nip')
            ->where('st.nip',$nip)
            ->orderBy('t.deadline_at','asc')
            ->get(['t.id as id_task','t.name as task','t.deadline_at','s.id as id_itep','p.id_project as id_project']);

        return response()->json($task,200);
    }

// ---------------------------------------------------------------TERBARU----------------------------------------------------------------------------    

    public function getPenanggungJawabTask($id_project,$id_step){
        $tasks = DB::table('project_structures as p')
        ->join('project_structure_staff as ps','ps.id_project_structure','p.id')
        ->join('staff as s','s.nip','ps.staff')
        ->where('p.id_project',$id_project)
        ->where('p.step',$id_step)
        ->get(['s.nip','s.name','s.image','s.jabatan']); 

        return response()->json($tasks,200);
    }

    public function setPenanggungJawabTask(Request $request, $id){
        
        $this->validate($request,[
            'handled_by' => 'required'
        ]);

        

        $handled_by = $request->input('handled_by');
        $task = Task::findOrFail($id);
        $task->handled_by = $handled_by;     

        if(!$task->update()){
            return response()->json([
                'msg' => 'Error during update'
            ],404);
        }

        $task->view_project = [
            'href' => 'api/v1/task'.$task->id,
            'method' => 'GET'
        ];

        $response = [
            'msg' => 'Project Updated',
            'project' => $task
        ];

        return response()->json($response,200);
    }    


    public function setLeaderProjectStructure(Request $request, $project,$step){
        $id = DB::table('project_structures')
        ->where('id_project',$project)
        ->where('step',$step)
        ->get(['id']);

        
        $id_task = $id[0]->id;
        
        $this->validate($request,[
            'leader' => 'required'
        ]);
        

        $leader = $request->input('leader');
        
        
        $ps = ProjectStructure::findOrFail($id_task);
        
        $ps->leader = $leader;     
        
        if(!$ps->update()){
            return response()->json([
                'msg' => 'Error during update'
            ],404);
        }

        $response = [
            'msg' => 'Project Structure Updated'
        ];

        
        return response()->json($response,200);
    }

    public function getAddTeam($project,$step){

        $ps = DB::table('project_structures')
            ->where('id_project',$project)
            ->where('step',$step)
            ->get();

        return response()->json($ps,200);
    }  

    public function setAddTeam(Request $request, $project,$step){
        $this->validate($request,[
            'staff' => 'required',
        ]);

        $ps = DB::table('project_structures')
            ->where('id_project',$project)
            ->where('step',$step)
            ->get();

        $temp = $ps[0]->id;

        $staff = $request->input('staff');
        
        

        $pss = new ProjectStructureStaff([
            'staff' => $staff,
            'id_project_structure' => $temp,
        ]);

        

        if($pss->save()){

            $message = [
                'msg' => 'Team has been created',
            ];
            return response()->json($message,201);
        }

        

        $response = [
            'msg' => 'Error during creation',
        ];

        return response()->json($response,404);
    }

    

    public function progressProject($id_project){
            
        $project = DB::table('project_structures')
        ->where('id_project',$id_project)
        ->where('status',1)
        ->get();
        
        $project2 = DB::table('project_structures')
        ->where('id_project',$id_project)
        ->get();
        
        $beres_project = count($project);
        $total_project = count($project2);
        
        $result = floor($beres_project/$total_project * 100);
        return response($result);
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
        return response($result);
    }

    public function getStaffForLeaderOrTeam($id){
            
        $pm = DB::table('projects')
        ->where('id_project',$id)
        ->pluck('project_manager')
        ->toArray();

        $leader = DB::table('project_structures')
        ->where('id_project',$id)
        ->pluck('leader')
        ->toArray();
        
        $staff = DB::table('project_structures as ps')
        ->join('project_structure_staff as pss','ps.id','pss.id_project_structure')
        ->where('ps.id_project',$id)
        ->pluck('pss.staff')
        ->toArray();         

        $bekerja = $pm;
        for ($i=0; $i <count($leader) ; $i++) { 
            array_push($bekerja,$leader[$i]);            
        }
        for ($i=0; $i <count($staff) ; $i++) { 
            array_push($bekerja,$staff[$i]);            
        }        
        
                
        $result = DB::table('staff')
        ->whereNotin('nip',$bekerja)
        ->get();

        return response()->json($result,200); 
    }   


    public function getstafforteam($id){
        $pm = DB::table('projects')
        ->where('id_project',$id)
        ->pluck('project_manager')
        ->toArray();

        $leader = DB::table('project_structures')
        ->where('id_project',$id)
        ->pluck('leader')
        ->toArray();
        
        $staff = DB::table('project_structures as ps')
        ->join('project_structure_staff as pss','ps.id','pss.id_project_structure')
        ->where('ps.id_project',$id)
        ->pluck('pss.staff')
        ->toArray();         

        $bekerja = $pm;
        for ($i=0; $i <count($leader) ; $i++) { 
            array_push($bekerja,$leader[$i]);            
        }
        for ($i=0; $i <count($staff) ; $i++) { 
            array_push($bekerja,$staff[$i]);            
        }        
        
        
        
        
        $result = DB::table('staff')
        ->whereNotin('nip',$bekerja)
        ->get();
        
        return response()->json($result,200); 
    }   
    
    
    public function deleteMember(Request $request,$project,$step){
        
        $this->validate($request,[
            'staff' => 'required',
        ]);

        $ps = DB::table('project_structures')
            ->where('id_project',$project)
            ->where('step',$step)
            ->get();

        $temp = $ps[0]->id;

        $staff = $request->input('staff');
        
        

        // $pss = new ProjectStructureStaff([
        //     'staff' => $staff,
        //     'id_project_structure' => $temp,
        // ]);

        $pss = DB::table('project_structure_staff')
        ->where('id_project_structure',$temp)
        ->where('staff',$staff);
        

        if($pss->delete()){
            $message = 'member has been deleted';
        }else{
            $message = 'gagal wow';
        }

            
        return response()->json($message,201);
    }

}
