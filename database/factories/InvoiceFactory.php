<?php

use Faker\Generator as Faker;
use Illuminate\Support\Facades\DB;

$factory->define(App\Invoice::class, function (Faker $faker) {
    $project = DB::table('projects')->pluck('id_project');
    $total = [1200000,3000000,5500000,7900000];   
    return [
		
		'invoice_id' => $faker->unique()->randomDigit, 
		'notes' => $faker->sentence($nbWords = 6, $variableNbWords = true),
		'total' => $faker->randomElement($total),
		'status' => $faker->randomDigit(),
		'due_date' => $faker->date(),
		'project' => $faker->randomElement($project),
    ];
});
