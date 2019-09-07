<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use TravelCompanion\Section;
use Faker\Generator as Faker;

$factory->define(Section::class, function (Faker $faker) {
    return [
        'content' => $faker->text(2500),

        'visibility' => $faker->randomElement(array_keys(config("mapping.visibility"))),
    	'published_at' => now(),
    ];
});
