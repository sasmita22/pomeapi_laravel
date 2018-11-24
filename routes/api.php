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
	$query = DB::table('projects')
	->join('project_structures','project_structures.id_project','=','projects.id_project')
	->join('project_structure_staff','project_structure_staff.id_project_structure','=','project_structures.id')
	->join('staff','staff.nip','=','project_structure_staff.staff')
	->where('staff.nip',$id)
	->orWhere('projects.project_manager',$id);	

	// $query = App\Project::join('project_structures','project_structures.id_project','=','projects.id_project')
	// ->join('project_structure_staff','project_structure_staff.id_project_structure','=','project_structures.id')
	// ->join('staff','staff.nip','=','project_structure_staff.staff')
	// ->where('staff.nip',$id)
	// ->orWhere('projects.project_manager',$id);	
	
	// $staff = App\Staff::join('projects','projects.project_manager','=','staff.nip')
    // ->join('project_structures','project_structures.id_project','=','projects.id_project')
    // ->join('tasks','tasks.project_structure','=','project_structures.id')
    // ->where('staff.nip',$id)->get(['staff.nip']);    
	
	// $project = App\Staff::find($id)->projects; 
	// $project_staff = App\Staff::join('project_structure_staff','project_structure_staff.staff','=','staff.nip')
	// ->join('project_structures','project_structures.id','=','project_structure_staff.id_project_structure')
	// ->where('staff.nip',$id);

	
	
	$response = $query
	->distinct()
	->get(['projects.id_project','projects.name','projects.project_manager']);
	//->get(['projects.name','projects.project_manager','projects.start_at','projects.ended_at','projects.deadline_at','projects.status','projects.price']);
	$projects = array();
	
	foreach ($response as $value){
		$objProject = new stdClass();
		$project = DB::table('projects')
		->where("id_project",$value->id_project)
		->get();
		
		

		$objProject->id_project = $project[0]->id_project;
		$objProject->name = $project[0]->name;
		$objProject->project_manager = $project[0]->project_manager;
		

		if($project[0]->project_manager == $id){
			$objProject->position = 'project manager';
		}else{
			$objProject->position = 'staff';
		}
		
			
		$stepTemp = DB::table('project_structures')
		->join('steps','steps.id','project_structures.step')
		->where("project_structures.id_project",$value->id_project)
		->get();

		$steps = array();
		foreach ($stepTemp as $s){
			$classStep = new stdClass();


			$arrayStep = DB::table('steps')
			->where("id",$s->step)
			->get();

			$arrayTask = DB::table('tasks')
			->where('project_structure',$s->id)
			->get();

			$classStep->id = $arrayStep[0]->id;
			$classStep->name = $arrayStep[0]->name;
			$classStep->deadline_at = $arrayStep[0]->deadline_at;
			$classStep->ended_at = $arrayStep[0]->ended_at;

			$classStep->task = $arrayTask;



			array_push($steps,$classStep);
			
			
			
		
		}


		$objProject->step = $steps;


		
		// $objProject->step->task = DB::table('project_structures')
		// ->join('tasks','tasks.project_structure','project_structure.step');

		array_push($projects,$objProject);
	}


    return response()->json($projects,404);
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

class Project{
	public $step;
}

class Step{
	public $task;
}
