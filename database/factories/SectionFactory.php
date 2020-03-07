<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use TravelCompanion\Section;

$factory->define(Section::class, function (Faker $faker) {
    return [
        'content' => $faker->text(2500),

       	'image' => Str::random(80),
       	'image_caption' => $faker->text(80),

        'start_time' => $faker->time('H:i:s'),
        'end_time' => $faker->time('H:i:s'),
        'temperature' => $faker->numberBetween(-5, 50),

        'visibility' => $faker->randomElement(array_keys(config("mapping.visibility"))),
    	'published_at' => now(),
    ];
});
