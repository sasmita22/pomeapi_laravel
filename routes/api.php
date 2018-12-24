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
	Route::get('project/{id_project}', 'API\ProjectController@getProjectDetail');

	Route::get('staff/{nip}', 'API\StaffController@getStaff');
	Route::get('step/project/{id_project}/step/{id_step}', 'API\StepController@getDetailStep');
	Route::get('tasks/project/{id_project}/step/{id_step}', 'API\TaskController@getTasks');
	Route::get('task/{id}','API\TaskController@getTask');
	
	Route::patch('task/statusDone/{id}','API\TaskController@statusDone');
	Route::patch('task/statusUndone/{id}','API\TaskController@statusUndone');
	
	
	Route::get('projects/{id}','API\StaffController@getHirarkiProject');
	Route::get('staff/chooseLeader/{id}','API\StaffController@chooseLeader');
	Route::get('staff/getStaffOnStep/{id_project}/{id_staff}','API\StaffController@getStaffOnStep');
	Route::get('project/getHirarkiStep/{id_project}','API\StaffController@getHirarkiStep');	

});

