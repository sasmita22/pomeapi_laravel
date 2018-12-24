<?php


namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Project;

class ProjectController extends Controller
{


    public function getProjectDetail($id_project)
    {
        $project = Project::where('id_project',$id_project)->firstOrFail();
        $pm = DB::table("staff")->where('nip',$project->project_manager)->get();
        $project->pm = $pm[0];
        return response()->json($project,200);
    }

    
}
