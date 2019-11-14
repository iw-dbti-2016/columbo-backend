<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use TravelCompanion\Section;

$factory->define(Section::class, function (Faker $faker) {
    return [
        'content' => $faker->text(2500),

       	'image' => Str::random(80),
        'time' => $faker->time('H:i:s'),
        'duration_minutes' => $faker->numberBetween(1, 180),

        'visibility' => $faker->randomElement(array_keys(config("mapping.visibility"))),
    	'published_at' => now(),
    ];
});
