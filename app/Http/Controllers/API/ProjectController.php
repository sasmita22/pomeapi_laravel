<?php


namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Project;
use LaravelQRCode\Facades\Qrcode;

class ProjectController extends Controller
{

    public function createProject(Request $request) {
        $this->validate($request,[
            'name' => 'required',
            'deskripsi' => 'required',
            'client' => 'required',
            'project_manager' => 'required',
            'image' => 'required',
            'start_at' => 'required',
            'deadline_at' => 'required',
            'status' => 'required',
            'price' => 'required'                
        ]);

        $name = $request->input('name');
        $deskripsi = $request->input('deskripsi');
        $client = $request->input('client');
        $project_manager = $request->input('project_manager');
        $image = $request->input('image');
        $start_at = $request->input('start_at');
        $deadline_at = $request->input('deadline_at');
        $status = $request->input('status');
        $price = $request->input('price');
    
        $project = new Project([
            'name' => $name,
            'deskripsi' => $deskripsi,
            'client' => $client,
            'project_manager' => $project_manager,
            'image' => $image,
            'start_at' => $start_at,
            'deadline_at' => $deadline_at,
            'status' => $status,
            'price' => $price,
        ]);
        $project->save();
        $project->qrcode = $project->id_project.'.png';
    
        $file = public_path('/images/qrcode/'.$project->id_project.'.png');
        QRCode::text($project->qrcode)->setOutfile($file)->png(); 

        if($project->save()){
            $message = [
                'msg' => 'project created',
            ];
            return response()->json($message,201);
        }

        $response = [
            'msg' => 'Error during creation',
        ];

        return response()->json($response,404);
    }



    public function createQrProject($id){
        // $qrcode = $id.'.png';

        $qrcode = '{ project_id : '.$id.' }';

        $file = public_path('/images/qrcode/'.$id.'.png');
        QRCode::text($qrcode)->setOutfile($file)->png();
        
        return response('berhasil');
    }


    public function getProjectDetail($id_project)
    {
        $project = Project::where('id_project',$id_project)->firstOrFail();
        $pm = DB::table("staff")->where('nip',$project->project_manager)->get();
        $project->pm = $pm[0];
        return response()->json($project,200);
    }

    public function getProjectDetailQrCode($id_project,$nip)
    {
        $project = Project::where('id_project',$id_project)->firstOrFail();
        $pm = DB::table("staff")->where('nip',$project->project_manager)->get();
        $project->pm = $pm[0];
        
        return response()->json($project,200);
    }    

}

    
