<?php

use Faker\Generator as Faker;

$factory->define(App\Staff::class, function (Faker $faker) {
    return [
        	'nip' => substr($this->faker->unique()->creditCardNumber,6),
        	'name' => $this->faker->name(10),
        	'email' => $this->faker->unique()->safeEmail,
        	'password' => str_random(10),
    ];
});
