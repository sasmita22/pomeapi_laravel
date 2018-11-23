<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Step;

class StepController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $steps = Step::all();
        foreach ($steps as $step) {
            $step->view_step = [
                'href' => 'api/step/'.$step->id,
                'method' => 'GET'
            ];
        }

        $response = [
            'msg' => 'List of all step',
            'step' => $steps
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
            'deadline_at' => 'required',
            'ended_at' => 'required',

        ]);
        


        $name = $request->input('name');
        $deadline_at = $request->input('deadline_at');
        $ended_at = $request->input('ended_at');


        $step = new Step([
            'name' => $name,
            'deadline_at' => $deadline_at,
            'ended_at' => $ended_at,

        ]);

        if($step->save()){
            // $step->users()->attach($id_step);
            $step->step = [
                'href' => 'api/step/'.$step->id,
                'method' => 'GET'
            ];

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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $step = Step::where('id',$id)->firstOrFail();
        $step->step = [
            'href' => 'api/v1/step',
            'method' => 'GET'
        ];

        $response = [
            'msg' => 'Project information',
            'step' => $step
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
            'deadline_at' => 'required',
            'ended_at' => 'required',

        ]);
        


        $name = $request->input('name');
        $deadline_at = $request->input('deadline_at');
        $ended_at = $request->input('ended_at');

        $step = Step::findOrFail($id);

        // if(!$step->users()->where('id_step',$id_step)->first()){
        //     return response()->json(['msg' => 'user not registered for step,update not successful'],401);
        // };


        $step->name = $name;
        $step->deadline_at = $deadline_at;
        $step->ended_at = $ended_at;     

        

        if(!$step->update()){
            return response()->json([
                'msg' => 'Error during update'
            ],404);
        }

        $step->view_step = [
            'href' => 'api/v1/step'.$step->id,
            'method' => 'GET'
        ];

        $response = [
            'msg' => 'Project Updated',
            'step' => $step
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
        $step = Step::findOrFail($id);
        // $users = $step->users;
        // $step->users()->detach();

        if(!$step->delete()){
            // foreach ($users as $user) {
            //     $step->users()->attach($user);
            // }

            return response()->json([
                'msg' => 'Deletion Failed'
            ],404);
        }

        $response = [
            'msg' => 'Project deleted',
            'create' => [
                'href' => 'api/v1/step',
                'method' => 'POST',
                'params' => 'title, description,time'
            ]
        ];

        return response()->json($response,200);
    }
}
