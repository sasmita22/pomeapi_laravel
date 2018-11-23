<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');

Route::group(['middleware' => 'auth:api'], function(){
	Route::post('details', 'API\UserController@details');
	Route::resource('project','API\ProjectController',[
		'except' => ['create','edit']
	]);
	Route::get('project/nip/{nip}', 'API\ProjectController@getProjectByNip');
	Route::get('task/nip/{nip}', 'API\TaskController@getTaskByNip');
	Route::get('task/project_structure/{id}', 'API\TaskController@getTaskByProjectStructure');
	Route::resource('task','API\TaskController',[
		'except' => ['create','edit']
	]);
	Route::resource('invoice','API\InvoiceController',[
		'except' => ['create','edit']
	]);
	Route::resource('team','API\TeamController',[
		'except' => ['create','edit']
	]);
	Route::resource('step','API\StepController',[
		'except' => ['create','edit']
	]);
	Route::resource('payment','API\PaymentController',[
		'except' => ['create','edit']
	]);							

});


Route::get('/{id}', function ($id) {
	// $query = App\Project::join('project_structures','project_structures.id_project','=','projects.id_project')
	// ->join('project_structure_staff','project_structure_staff.id_project_structure','=','project_structures.id')
	// ->where('projects.project_manager',$id)
	// ->orWhere('project_structure_staff.staff',$id);	
	
	// $staff = App\Staff::join('projects','projects.project_manager','=','staff.nip')
    // ->join('project_structures','project_structures.id_project','=','projects.id_project')
    // ->join('tasks','tasks.project_structure','=','project_structures.id')
    // ->where('staff.nip',$id)->get(['staff.nip']);    
	
	$project = App\Staff::find($id)->projects; 
	$project_staff = App\Staff::join('project_structure_staff','project_structure_staff.staff','=','staff.nip')
	->join('project_structures','project_structures.id','=','project_structure_staff.id_project_structure')
	->where('staff.nip',$id);

	// $project = $query
	// ->get(['projects.name','projects.project_manager','projects.start_at','projects.ended_at','projects.deadline_at','projects.status','projects.price']);
	
	
	
	$response = [
		'project_manager' => $project,
		'project_staff' => $project_staff->get()
	];

    return response()->json($response,404);
});


Route::get('/s', function () {
	$staff = App\Project::join('project_structures','project_structures.id_project','=','projects.id_project')
	->join('project_structure_staff','project_structure_staff.id_project_structure','=','project_structures.id')
	->get();
    $response = $staff;
	return response()->json($response,200);
});

Route::get('/t', function () {
    $staff = App\Staff::join('projects','projects.project_manager','=','staff.nip')
    ->join('project_structures','project_structures.id_project','=','projects.id_project')
    ->join('tasks','tasks.project_structure','=','project_structures.id')
    ->where('staff.nip','1')->get();    
    
    return response($staff);
});

