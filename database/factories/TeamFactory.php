<?php

use Faker\Generator as Faker;
use Illuminate\Support\Facades\DB;

$factory->define(App\Team::class, function (Faker $faker) {
    $staff = DB::table('staff')->pluck('nip');
    return [
		'id_team' => $faker->unique()->randomDigit,
		'staff' => $faker->randomElement($staff),
    ];
});
