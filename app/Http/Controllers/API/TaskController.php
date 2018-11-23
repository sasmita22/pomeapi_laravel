<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Task;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::all();
        foreach ($tasks as $task) {
            $task->view_project = [
                'href' => 'api/task/'.$task->id,
                'method' => 'GET'
            ];
        }

        $response = [
            'msg' => 'List of all tasks',
            'tasks' => $tasks
        ];

        return response()->json($response,200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'required',
            'status' => 'required',
            'type' => 'required',
            'project_structure' => 'required',
            'deadline_at' => 'required',
            'finished_at' => 'required',
            'handled_by' => 'required'
        ]);
        


        $name = $request->input('name');
        $status = $request->input('status');
        $type = $request->input('type');
        $project_structure = $request->input('project_structure');
        $deadline_at = $request->input('deadline_at');
        $finished_at = $request->input('finished_at');
        $handled_by = $request->input('handled_by');


        $task = new Task([
            'name' => $name,
            'status' => $status,
            'type' => $type,
            'project_structure' => $project_structure,
            'deadline_at' => $deadline_at,
            'finished_at' => $finished_at,
            'handled_by' => $handled_by,
        ]);

        if($task->save()){
            // $project->users()->attach($id_project);
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = Task::where('id',$id)->firstOrFail();
        $task->task = [
            'href' => 'api/v1/project',
            'method' => 'GET'
        ];

        $response = [
            'msg' => 'Project information',
            'task' => $task
        ];
        return response()->json($response,200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'name' => 'required',
            'status' => 'required',
            'type' => 'required',
            'project_structure' => 'required',
            'deadline_at' => 'required',
            'finished_at' => 'required',
            'handled_by' => 'required'
        ]);


        $name = $request->input('name');
        $status = $request->input('status');
        $type = $request->input('type');
        $project_structure = $request->input('project_structure');
        $deadline_at = $request->input('deadline_at');
        $finished_at = $request->input('finished_at');
        $handled_by = $request->input('handled_by');

        $task = Task::findOrFail($id);


        $task->name = $name;
        $task->status = $status;
        $task->type = $type;
        $task->project_structure = $project_structure;
        $task->deadline_at = $deadline_at;
        $task->finished_at = $finished_at;
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);

        if(!$task->delete()){

            return response()->json([
                'msg' => 'Deletion Failed'
            ],404);
        }

        $response = [
            'msg' => 'Project deleted',
            'create' => [
                'href' => 'api/v1/project',
                'method' => 'POST',
                'params' => 'title, description,time'
            ]
        ];

        return response()->json($response,200);
    }

    public function getTaskByNip($nip)
    {
        $project = Task::where('handled_by',$nip)->get();
        $project->view_project = [
            'href' => 'api/v1/project',
            'method' => 'GET'
        ];

        $response = [
            'msg' => 'Project information',
            'project' => $project
        ];
        return response()->json($response,200);
    }


    public function getTaskByProjectStructure($id)
    {
        $project = Task::where('project_structure',$id)->get();
        $project->view_project = [
            'href' => 'api/v1/project',
            'method' => 'GET'
        ];

        $response = [
            'msg' => 'Project information',
            'project' => $project
        ];
        return response()->json($response,200);
    }    
}
