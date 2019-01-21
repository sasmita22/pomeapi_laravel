<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Task;
use App\Staff;
use App\ProjectStructure;
use App\ProjectStructureStaff;
use carbon\Carbon;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getNotification($id){
        $notif = DB::select(DB::raw("
            select n.id , p.name as project, st.name as step, t.name as task, n.tgl_review from projects as p
            join project_structures as ps
            on ps.id_project = p.id_project
            join steps as st
            on st.id = ps.step
            join tasks as t
            on t.project_structure = ps.id
            join notifications as n
            on n.task = t.id
            where n.leader = '$id' or n.project_manager = '$id'
            order by n.tgl_review desc         
        "));



        return response()->json($notif,201);
    }

    public function createTask(Request $request,$project,$step)
    {

        $ps = DB::table('project_structures')
            ->where('id_project',$project)
            ->where('step',$step)
            ->get();

        $project_structure = $ps[0]->id;

        $staff = $request->input('staff');
        
        
        
        $this->validate($request,[
            'name' => 'required',
            'deskripsi' => 'required',
            'deadline_at' => 'required',
            // 'finished_at' => 'required',
            // 'handled_by' => 'required'
        ]);
        


        $name = $request->input('name');
        $deskripsi = $request->input('deskripsi');
        $project_structure = $project_structure;
        $deadline_at = $request->input('deadline_at');
        // $finished_at = $request->input('finished_at');
        // $handled_by = $request->input('handled_by');


        $task = new Task([
            'name' => $name,
            'deskripsi' => $deskripsi,
            'status' => 0,
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

    public function getManagerDashboard($nip){ 
        $query = DB::table('projects as p')
        ->select(DB::raw('t.id , t.name, t.deadline_at , p.name as project, s.name as step'))
        ->join('project_structures as ps','ps.id_project','p.id_project')
        ->join('steps as s','s.id','ps.step')
        ->join('tasks as t','t.project_structure','ps.id')
        ->where('p.project_manager',$nip)
        ->where('t.status',0)
        ->orderBy('t.deadline_at')
        ->get();
        for ($i=0; $i < count($query) ; $i++) { 
            $query[$i]->deadline_at = Carbon::parse($query[$i]->deadline_at)->format('d F Y');
        }        

        return response()->json($query,200); 
    }
        
    public function getLeaderDashboard($nip){
        $query = DB::table('projects as p')
        ->select(DB::raw('t.id , t.name, t.deadline_at , p.name as project, s.name as step'))
        ->join('project_structures as ps','ps.id_project','p.id_project')
        ->join('steps as s','s.id','ps.step')
        ->join('tasks as t','t.project_structure','ps.id')
        ->where('ps.leader',$nip)
        ->where('t.status',0)
        ->orderBy('t.deadline_at')
        ->get();
        
        for ($i=0; $i < count($query) ; $i++) { 
            $query[$i]->deadline_at = Carbon::parse($query[$i]->deadline_at)->format('d F Y');
        }

        return response()->json($query,200); 
    } 
    
    public function getStaffDashboard($nip){
        $query = DB::table('projects as p')
        ->select(DB::raw('t.id ,t.name, t.deadline_at , p.name as project, s.name as step'))
        ->join('project_structures as ps','ps.id_project','p.id_project')
        ->join('steps as s','s.id','ps.step')
        ->join('tasks as t','t.project_structure','ps.id')
        ->where('t.handled_by',$nip)
        ->where('t.status',0)
        ->orderBy('t.deadline_at')
        ->get();

        for ($i=0; $i < count($query) ; $i++) { 
            $query[$i]->deadline_at = Carbon::parse($query[$i]->deadline_at)->format('d F Y');
        }

        
        return response()->json($query,200); 
    }       

     
    public function getTask($id){

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
                $task[0]->staff = $staff[0];
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

    public function statusPreview($id)
    {
        $task = Task::findOrFail($id);
        $task->status = 2;
        if(!$task->update()){
            return response()->json([
                'msg' => 'Error during update'
            ],404);
        }


        $response = [
            'msg' => 'Task status Preview',
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

// ---------------------------------------------------------------TERBARU----------------------------------------------------------------------------    

    public function getPenanggungJawabTask($id_project,$id_step,$task){
        // $tasks = DB::table('project_structures as p')
        // ->join('project_structure_staff as ps','ps.id_project_structure','p.id')
        // ->join('staff as s','s.nip','ps.staff')
        // ->where('p.id_project',$id_project)
        // ->where('p.step',$id_step)
        // ->get(); 
        // ->get(['s.nip','s.name','s.image','s.jabatan']); 

        $result = DB::select(DB::raw("
            SELECT s.nip,s.name,s.image,s.jabatan
            FROM project_structures AS ps
            JOIN project_structure_staff AS pss on ps.id = pss.id_project_structure
            JOIN staff as s on s.nip = pss.staff
            where ps.id_project = '$id_project' AND ps.step = '$id_step' and pss.staff not in (select coalesce(handled_by,0) from tasks where id='$task')            
        "));
        return response()->json($result,200);
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

        return response()->json($response,201);
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

        // $ps = DB::table('project_structures')
        //     ->join()
        //     ->where('id_project',$project)
        //     ->where('step',$step)
        //     ->get(['id']);

        $ps = DB::select(DB::raw("
            select staff.nip, staff.name, staff.jabatan, staff.image from staff
            WHERE staff.nip in (select project_structure_staff.staff
            from project_structures
            join project_structure_staff on project_structures.id = project_structure_staff.id_project_structure
            where project_structures.id_project = '$project' AND project_structures.step = '$step')        
        "));

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
            return response()->json($message,200);
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
        ->whereNotNull('leader')
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
        ->get(['nip','name','image','jabatan']);

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
    
    
    public function deleteMember($project,$step,$staff){

        $ps = DB::table('project_structures')
            ->where('id_project',$project)
            ->where('step',$step)
            ->get();

        $temp = $ps[0]->id;

        $pss = DB::table('project_structure_staff')
        ->where('id_project_structure',$temp)
        ->where('staff',$staff);
        

        if($pss->delete()){
            $msg= 'member has been deleted';   
            return response()->json($msg[0],201);
        }

        
    }

}
