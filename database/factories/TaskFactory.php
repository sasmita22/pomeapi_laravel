<?php

use Faker\Generator as Faker;
use Illuminate\Support\Facades\DB;

$factory->define(App\Task::class, function (Faker $faker) {
    $type = DB::table('types')->pluck('id');
    $project_structure = DB::table('project_structures')->pluck('id');
    $handled_by = DB::table('staff')->pluck('nip');        
    return [
    	'id' => $faker->unique()->randomDigit,
    	'name' => $faker->unique()->jobTitle,
		'status' => $faker->randomDigit(),
    	'type' => $faker->randomElement($type),
    	'project_structure' => $faker->randomElement($project_structure),
		'deadline_at' => $faker->date(),
    	'finished_at' => $faker->date(),
    	'handled_by' => $faker->randomElement($handled_by),				
    ];
});
