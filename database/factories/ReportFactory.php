<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Columbo\Report;
use Faker\Generator as Faker;

$factory->define(Report::class, function (Faker $faker) {
    return [
        'title' => $faker->text(100),
        'date' => $faker->dateTimeBetween('now', '+2 years')->format('Y-m-d'),
        'description' => $faker->text(500),

        'visibility' => $faker->randomElement(array_keys(config("mapping.visibility"))),
        'published_at' => now(),
    ];
});
