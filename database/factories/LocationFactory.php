<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Columbo\Location;

$factory->define(Location::class, function (Faker $faker) {
    return [
    	'coordinates' => [
			'latitude' => $faker->latitude(-90, 90),
			'longitude' => $faker->longitude(-180, 180),
		],
    	'map_zoom' => $faker->randomFloat(6, 0, 20),
    	'name' => $faker->text(100),
    	'info' => $faker->text(500),

        'visibility' => $faker->randomElement(array_keys(config("mapping.visibility"))),
    	'published_at' => now(),
    ];
});
