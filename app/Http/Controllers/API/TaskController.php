<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Task;
use App\Staff;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getTask($id){
        $task = Task::where('id',$id)->firstOrFail();

        $staff = Staff::where('nip',$task->handled_by)
            ->firstOrFail(['nip','name','jabatan','image']);

        $task->staff = $staff;

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

}
