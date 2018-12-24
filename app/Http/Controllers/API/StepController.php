<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Step;

class StepController extends Controller
{

    public function getDetailStep($id_project,$id_step){
        $step = DB::table('project_structures as ps')
            ->join('steps as st','st.id','ps.step')
            ->where('ps.id_project',$id_project)
            ->where('ps.step',$id_step)
            ->get(['ps.id as id_project_structure', 'ps.step as id', 'st.name', 'ps.leader','ps.deskripsi', 'ps.leader','ps.deadline_at','ps.ended_at']);

        $step = $step[0];

        $leader = DB::table('staff as s')
            ->where('s.nip',$step->leader)
            ->get(['nip','name','jabatan','image']);

        $staff = DB::table('project_structures as ps') 
        ->join('project_structure_staff as pss','pss.id_project_structure','ps.id')
        ->join('staff as s','s.nip','pss.staff')
        ->where('ps.id',$step->id_project_structure)
        ->get(['s.nip','s.name','s.jabatan', 's.email','image']);

        $step->leader = $leader[0];
        $step->team = $staff;


        return response()->json($step,200);
    }
}
