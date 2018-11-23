<?php

use Faker\Generator as Faker;
use Illuminate\Support\Facades\DB;

$factory->define(App\ProjectStructure::class, function (Faker $faker) {
    $id_project = DB::table('projects')->pluck('id_project');
    $step = DB::table('steps')->pluck('id');
    return [
    	'id' => $faker->unique()->randomDigit,
    	'id_project' => $faker->randomElement($id_project),
		'step' => $faker->randomElement($step)
    ];
});
