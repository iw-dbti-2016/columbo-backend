<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use TravelCompanion\Plan;

$factory->define(Plan::class, function (Faker $faker) {
    return [
        'date' => $faker->dateTimeBetween('now', '+ 2 years')->format('Y-m-d'),
        'program' => $faker->text(500),

        'driving_distance_km' => $faker->randomFloat(3, 0, 1000),
        'wifi_available' => $faker->boolean,
        'sleeping_location' => Str::random(30),
        'estimated_price' => $faker->numberBetween(100, 20000),

        'status_sleep' => $faker->numberBetween(0, 255),
        'status_activities' => $faker->numberBetween(0, 255),

        'visibility' => $faker->numberBetween(0, 255),
        'published_at' => now(),
    ];
});
