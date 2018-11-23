<?php


namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Project;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $projects = Project::all();
        foreach ($projects as $project) {
            $project->view_project = [
                'href' => 'api/v1/project/'.$project->id_project,
                'method' => 'GET'
            ];
        }

        $response = [
            'msg' => 'List of all Projects',
            'projects' => $projects
        ];

        return response()->json($response,200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'project_manager' => 'required',
            'name' => 'required',
            'start_at' => 'required',
            'ended_at' => 'required',
            'deadline_at' => 'required',
            'status' => 'required',
            'price' => 'required'
        ]);
        


       
        $project_manager = $request->input('project_manager');
        $name = $request->input('name');
        $start_at = $request->input('start_at');
        $ended_at = $request->input('ended_at');
        $deadline_at = $request->input('deadline_at');
        $status = $request->input('status');
        $price = $request->input('price');


        $project = new Project([
            
            'project_manager' => $project_manager,
            'name' => $name,
            'start_at' => $start_at,
            'ended_at' => $ended_at,
            'deadline_at' => $deadline_at,
            'status' => $status,
            'price' => $price,
        ]);

        if($project->save()){
            // $project->users()->attach($id_project);
            $project->view_project = [
                'href' => 'api/v1/project/' /*. $project->id_project*/,
                'method' => 'GET'
            ];

            $message = [
                'msg' => 'project created',
                'project' => $project
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
     * @param  int  $id_project
     * @return \Illuminate\Http\Response
     */
    public function show($id_project)
    {
        $project = Project::where('id_project',$id_project)->firstOrFail();
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

    public function getProjectByNip($nip)
    {
        $project = Project::where('project_manager',$nip)->get();
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id_project
     * @return \Illuminate\Http\Response
     */
    // public function edit($id_project)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id_project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_project)
    {
        $this->validate($request,[
            'project_manager' => 'required',
            'name' => 'required',
            'start_at' => 'required',
            'ended_at' => 'required',
            'deadline_at' => 'required',
            'status' => 'required',
            'price' => 'required'
        ]);

        $project_manager = $request->input('project_manager');
        $name = $request->input('name');
        $start_at = $request->input('start_at');
        $ended_at = $request->input('ended_at');
        $deadline_at = $request->input('deadline_at');
        $status = $request->input('status');
        $price = $request->input('price');

        $project = Project::findOrFail($id_project);

        // if(!$project->users()->where('id_project',$id_project)->first()){
        //     return response()->json(['msg' => 'user not registered for project,update not successful'],401);
        // };

        
        $project->project_manager = $project_manager;
        $project->name = $name;
        $project->start_at = $start_at;
        $project->ended_at = $ended_at;
        $project->deadline_at = $deadline_at;
        $project->status = $status;
        $project->price = $price;

        if(!$project->update()){
            return response()->json([
                'msg' => 'Error during update'
            ],404);
        }

        $project->view_project = [
            'href' => 'api/v1/project'.$project->id_project,
            'method' => 'GET'
        ];

        $response = [
            'msg' => 'Project Updated',
            'project' => $project
        ];

        return response()->json($response,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id_project
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_project)
    {
        $project = Project::findOrFail($id_project);
        // $users = $project->users;
        // $project->users()->detach();

        if(!$project->delete()){
            // foreach ($users as $user) {
            //     $project->users()->attach($user);
            // }

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
}
