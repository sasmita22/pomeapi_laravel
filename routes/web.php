<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/step', function () {
    $staff = App\Staff::join('projects','projects.project_manager','=','staff.nip')
    ->join('project_structures','project_structures.id_project','=','projects.id_project')
    ->join('steps','steps.id','=','project_structures.step')
    ->where('staff.nip','1')->get();    
    
    return response($staff);
});

Route::get('/task', function () {
    $staff = App\Staff::join('projects','projects.project_manager','=','staff.nip')
    ->join('project_structures','project_structures.id_project','=','projects.id_project')
    ->join('tasks','tasks.project_structure','=','project_structures.id')
    ->where('staff.nip','1')->get();    
    
    return response($staff);
});