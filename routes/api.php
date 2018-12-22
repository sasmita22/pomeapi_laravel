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

	Route::get('staff/{nip}', 'API\StaffController@show');
	
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
	
	Route::patch('task/statusDone/{id}','API\TaskController@statusDone');
	Route::patch('task/statusUndone/{id}','API\TaskController@statusUndone');
	
	
	Route::get('projects/{id}','API\StaffController@getHirarkiProject');
	Route::get('staff/chooseLeader/{id}','API\StaffController@chooseLeader');
	Route::get('staff/getStaffOnStep/{id_project}/{id_staff}','API\StaffController@getStaffOnStep');
	Route::get('project/getHirarkiStep/{id_project}','API\StaffController@getHirarkiStep');	

});




// Route::get('staff/getHirarkiStep/{id_project}',function($id_project){
// 	$query = DB::table('projects as p')
// 	->join('project_structures as ps','ps.id_project','p.id_project')
// 	->join('tasks as t','t.project_structure','ps.id')
// 	->join('steps as st','st.id','ps.step')
// 	->where('p.id_project',$id_project)
// 	->distinct()
// 	->get(['ps.id']);
	
// 	$steps = array();
// 	foreach ($query as $key => $value) {
// 		$objStep = new \stdClass;
// 		$step = DB::table('project_structures as ps')
// 		->join('steps as st','st.id','ps.step')
// 		->where('ps.id',$value->id)
// 		->get();
		
		
// 		$objStep->id = $step[0]->id;
// 		$objStep->name = $step[0]->name;
// 		$objStep->deadline_at = $step[0]->deadline_at;
// 		$objStep->ended_at = $step[0]->ended_at;


// 		$queryTask = DB::table('project_structures as ps')
// 		->join('tasks as t','t.project_structure','ps.id')
// 		->where('ps.id',$value->id)
// 		->get();


	
// 		$tasks = array();
		

		
		
// 		foreach ($queryTask as $key => $s) {
			
// 			$objTask = new \stdClass;
// 			$task = DB::table('tasks')
// 			->where('id',$s->id)
// 			->get();

				
// 			$objTask->id 	= $task[0]->id;
// 			$objTask->name = $task[0]->name;
// 			$objTask->status = $task[0]->status;
// 			$objTask->type = $task[0]->type;			
// 			$objTask->project_structure = $task[0]->project_structure;
// 			$objTask->deadline_at = $task[0]->deadline_at;
// 			$objTask->finished_at = $task[0]->finished_at;
// 			$objTask->handled_by = $task[0]->handled_by;

			
// 			array_push($tasks,$objTask);

			
			
// 		}
// 		$objStep->tasks = $tasks;
// 		array_push($steps,$objStep);
// 	}

	
// 	return response()->json($steps,404);

// });

// Route::get('/{id}', function ($id) {
// 	$query = DB::table('projects')
// 	->join('project_structures','project_structures.id_project','=','projects.id_project')
// 	->join('project_structure_staff','project_structure_staff.id_project_structure','=','project_structures.id')
// 	->join('staff','staff.nip','=','project_structure_staff.staff')
// 	->where('staff.nip',$id)
// 	->orWhere('projects.project_manager',$id);	

// 	// $query = App\Project::join('project_structures','project_structures.id_project','=','projects.id_project')
// 	// ->join('project_structure_staff','project_structure_staff.id_project_structure','=','project_structures.id')
// 	// ->join('staff','staff.nip','=','project_structure_staff.staff')
// 	// ->where('staff.nip',$id)
// 	// ->orWhere('projects.project_manager',$id);	
	
// 	// $staff = App\Staff::join('projects','projects.project_manager','=','staff.nip')
//     // ->join('project_structures','project_structures.id_project','=','projects.id_project')
//     // ->join('tasks','tasks.project_structure','=','project_structures.id')
//     // ->where('staff.nip',$id)->get(['staff.nip']);    
	
// 	// $project = App\Staff::find($id)->projects; 
// 	// $project_staff = App\Staff::join('project_structure_staff','project_structure_staff.staff','=','staff.nip')
// 	// ->join('project_structures','project_structures.id','=','project_structure_staff.id_project_structure')
// 	// ->where('staff.nip',$id);

	
	
// 	$response = $query
// 	->distinct()
// 	->get(['projects.id_project','projects.name','projects.project_manager']);
// 	//->get(['projects.name','projects.project_manager','projects.start_at','projects.ended_at','projects.deadline_at','projects.status','projects.price']);
// 	$projects = array();
	
// 	foreach ($response as $value){
// 		$objProject = new stdClass();
// 		$project = DB::table('projects')
// 		->where("id_project",$value->id_project)
// 		->get();
		
		

// 		$objProject->id_project = $project[0]->id_project;
// 		$objProject->name = $project[0]->name;
// 		$objProject->project_manager = $project[0]->project_manager;
		

// 		if($project[0]->project_manager == $id){
// 			$objProject->position = 'project manager';
// 		}else{
// 			$objProject->position = 'staff';
// 		}
		
			
// 		$stepTemp = DB::table('project_structures')
// 		->join('steps','steps.id','project_structures.step')
// 		->where("project_structures.id_project",$value->id_project)
// 		->get();

// 		$steps = array();
// 		foreach ($stepTemp as $s){
// 			$classStep = new stdClass();


// 			$arrayStep = DB::table('steps')
// 			->where("id",$s->step)
// 			->get();

// 			$arrayTask = DB::table('tasks')
// 			->where('project_structure',$s->id)
// 			->get();

// 			$classStep->id = $arrayStep[0]->id;
// 			$classStep->name = $arrayStep[0]->name;
// 			$classStep->deadline_at = $arrayStep[0]->deadline_at;
// 			$classStep->ended_at = $arrayStep[0]->ended_at;

// 			$classStep->task = $arrayTask;



// 			array_push($steps,$classStep);
			
			
			
		
// 		}


// 		$objProject->step = $steps;


		
// 		// $objProject->step->task = DB::table('project_structures')
// 		// ->join('tasks','tasks.project_structure','project_structure.step');

// 		array_push($projects,$objProject);
// 	}

//     return response()->json($projects,404);
// });


class Project{
	public $step;
}

class Step{
	public $task;
}
