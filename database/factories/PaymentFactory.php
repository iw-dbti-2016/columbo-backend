<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use TravelCompanion\Payment;

$factory->define(Payment::class, function (Faker $faker) {
    return [
        'name' => $faker->text(100),
        'uuid' => Str::uuid(),
        'benificiary' => $faker->text(100),

        'date' => $faker->dateTimeBetween('now', '+ 2 years')->format('Y-m-d'),
        'total_amount' => $faker->numberBetween(100, 20000),
        'currency' => $faker->currencyCode,
        'tax_percentage' => $faker->randomFloat(2, 0, 20),
        'tip_amount' => $faker->numberBetween(10, 2000),
    ];
});
