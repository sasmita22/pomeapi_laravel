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


//==========================Terbaru Update
Route::delete('delete/member/{project}/{id_project}','API\TaskController@deleteMember');
Route::get('projects/{id}','API\StaffController@getHirarkiProject');
Route::post('step/create','API\StepController@createStep');//create step
Route::post('task/create','API\TaskController@createTask');//create task
Route::get('staff/getStaffForLeaderOrTeam/{id}','API\TaskController@getStaffForLeaderOrTeam');
Route::post('addTeam/porject/{project}/step/{step}','API\TaskController@setAddTeam'); //addteam
Route::get('getTeam/porject/{project}/step/{step}','API\TaskController@getAddTeam'); //getTeam
Route::get('task/penanggungJawabTask/project/{id_project}/step/{id_step}','API\TaskController@getPenanggungJawabTask'); //getHandledBy
Route::patch('project/setLeaderProjectStructure/{project}/{step}','API\TaskController@setLeaderProjectStructure'); //set Leader
Route::patch('task/setHandledBy/{id}','API\TaskController@setHandledBy');//set handledby
Route::get('task/{id}','API\TaskController@getTask');
Route::get('dashboard/projectManager/{id}','API\TaskController@getProjectManagerDashboard');
Route::get('dashboard/leader/{id}','API\TaskController@getLeaderDashboard');
Route::get('dashboard/staff/{id}','API\TaskController@getStaffDashboard');


Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');


Route::group(['middleware' => 'auth:api'], function(){
	Route::post('details', 'API\UserController@details');
	Route::get('project/{id_project}', 'API\ProjectController@getProjectDetail');

	Route::get('staff/{nip}', 'API\StaffController@getStaff');
	Route::get('step/project/{id_project}/step/{id_step}', 'API\StepController@getDetailStep');
	Route::get('tasks/project/{id_project}/step/{id_step}', 'API\TaskController@getTasks');
	//Route::get('task/{id}','API\TaskController@getTask');
	Route::get('dashboard/staff/{nip}','API\TaskController@getDashboardStaff');
	Route::get('dashboard/leader/{nip}','API\TaskController@getDashboardLeader');
	Route::get('dashboard/manager/{nip}','API\TaskController@getDashboardManager');
	
	
	Route::patch('task/statusDone/{id}','API\TaskController@statusDone');
	Route::patch('task/statusUndone/{id}','API\TaskController@statusUndone');
	
	
	// Route::get('projects/{id}','API\StaffController@getHirarkiProject');
	Route::get('staff/chooseLeader/{id}','API\StaffController@chooseLeader');
	Route::get('staff/getStaffOnStep/{id_project}/{id_staff}','API\StaffController@getStaffOnStep');
	Route::get('project/getHirarkiStep/{id_project}','API\StaffController@getHirarkiStep');
	
	//--------------------------------Baru--------------------------------------
	// Route::post('task/setAddTeam/{project}/{step}','API\TaskController@setAddTeam');
	// Route::get('task/penanggungJawabTask/project/{id_project}/step/{id_step}','API\TaskController@getPenanggungJawabTask');
	// Route::patch('project/setLeaderProjectStructure/{project}/{step}','API\TaskController@setLeaderProjectStructure');
	// Route::patch('task/setPenanggungJawabTask/{id}/','API\TaskController@setPenanggungJawabTask');
	Route::get('task/getStaffForTeam/{project}/{step}/','API\TaskController@getStaffForTeam');
	Route::get('task/progressStep/{project}/{step}','API\TaskController@progressStep');
	Route::get('task/progressProject/{project}','API\TaskController@progressProject');
	// Route::get('staff/getStaffForLeaderOrTeam/{id}','API\TaskController@getStaffForLeaderOrTeam');

});

