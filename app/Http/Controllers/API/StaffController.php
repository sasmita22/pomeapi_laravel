<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StaffController extends Controller
{
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
}
