<?php

use Faker\Generator as Faker;
use Illuminate\Support\Facades\DB;

$factory->define(App\Payment::class, function (Faker $faker) {
    $invoice = DB::table('invoices')->pluck('invoice_id');
    $total = [1200000,3000000,5500000,7900000];     
    return [
		'id' => $faker->unique()->randomDigit, 
		'desc' => $faker->sentence($nbWords = 6, $variableNbWords = true),
		'paid_at' => $faker->date(),
		'total' => $faker->randomElement($total),
		'invoice' => $faker->randomElement($invoice),
    ];
});
