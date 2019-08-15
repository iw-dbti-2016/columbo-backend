<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use TravelCompanion\Section;
use Faker\Generator as Faker;

$factory->define(Section::class, function (Faker $faker) {
    return [
        'content' => $faker->text(2500),

        'visibility' => $faker->numberBetween(0, 255),
    	'published_at' => now(),
    ];
});
