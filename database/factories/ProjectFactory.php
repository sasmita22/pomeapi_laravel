<?php

use Faker\Generator as Faker;
use Illuminate\Support\Facades\DB;


$factory->define(App\Project::class, function (Faker $faker) {
    $nip = DB::table('staff')->pluck('nip');
    $harga = [1200000,3000000,5500000,7900000];
    return [
		'id_project' => $faker->numberBetween($min = 5, $max = 30), 
		'name' => $faker->company,
		'project_manager' => $faker->randomElement($nip),
		'start_at' => $faker->date(),
		'ended_at' => $faker->date(),
		'deadline_at' => $faker->date(),
		'status' => $faker->randomDigit(),
		'price' => $faker->randomElement($harga),
    ];
});
