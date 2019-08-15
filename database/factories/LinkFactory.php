<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use TravelCompanion\Links;
use Faker\Generator as Faker;

$factory->define(Links::class, function (Faker $faker) {
    return [
        'name' => $faker->text(100),
        'url' => $faker->slug,

        'visibility' => $faker->numberBetween(0, 255),
    	'published_at' => now(),
    ];
});
