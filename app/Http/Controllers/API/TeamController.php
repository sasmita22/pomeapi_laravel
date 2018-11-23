<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Team;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teams = Team::all();
        foreach ($teams as $team) {
            $team->view_team = [
                'href' => 'api/v1/team/'.$team->id_team,
                'method' => 'GET'
            ];
        }

        $response = [
            'msg' => 'List of all Teams',
            'teams' => $teams
        ];

        return response()->json($response,200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'staff' => 'required',
        ]);
        


        $id_team = $request->input('id_team');
        $staff = $request->input('staff');



        $team = new Team([
            'staff' => $staff,
        ]);

        if($team->save()){
            // $team->users()->attach($id_team);
            $team->view_team = [
                'href' => 'api/v1/team/' /*. $team->id_team*/,
                'method' => 'GET'
            ];

            $message = [
                'msg' => 'team created',
                'team' => $team
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
        $team = Team::where('id_team',$id)->firstOrFail();
        $team->view_team = [
            'href' => 'api/v1/team',
            'method' => 'GET'
        ];

        $response = [
            'msg' => 'Team information',
            'team' => $team
        ];
        return response()->json($response,200);
    }


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
            'staff' => 'required',
        ]);

        $id_team = $request->input('id_team');
        $staff = $request->input('staff');

        $team = Team::findOrFail($id_team);

        // if(!$team->users()->where('id_team',$id_team)->first()){
        //     return response()->json(['msg' => 'user not registered for team,update not successful'],401);
        // };

        
        $staff = $staff;        

        if(!$team->update()){
            return response()->json([
                'msg' => 'Error during update'
            ],404);
        }

        $team->view_team = [
            'href' => 'api/team'.$team->id_team,
            'method' => 'GET'
        ];

        $response = [
            'msg' => 'Team Updated',
            'team' => $team
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
        $team = Team::findOrFail($id);

        if(!$team->delete()){

            return response()->json([
                'msg' => 'Deletion Failed'
            ],404);
        }

        $response = [
            'msg' => 'Team deleted',
            'create' => [
                'href' => 'api/v1/team',
                'method' => 'POST',
                'params' => 'title, description,time'
            ]
        ];

        return response()->json($response,200);
    }
}
