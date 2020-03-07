<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Columbo\Trip;

$factory->define(Trip::class, function (Faker $faker) {
    return [
        'name' => Str::random(30),
        'synopsis' => $faker->text(100),
        'description' => $faker->text(500),
        'start_date' => $faker->dateTimeBetween('now', '+ 1 year')->format('Y-m-d'),
        'end_date' => $faker->dateTimeBetween('+1 year', '+ 2 years')->format('Y-m-d'),

    	'visibility' => $faker->randomElement(array_keys(config("mapping.visibility"))),
    	'published_at' => now(),
    ];
});
