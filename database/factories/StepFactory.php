<?php

use Faker\Generator as Faker;

$factory->define(App\Step::class, function (Faker $faker) {
    return [
    	'id' => $faker->unique()->randomDigit,
    	'name' => $faker->unique()->jobTitle,
    	'deadline_at' => $faker->date(),
		'ended_at' => $faker->date()
    ];
});
