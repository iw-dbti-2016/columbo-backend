<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Columbo\Location;

$factory->define(Location::class, function (Faker $faker) {
    return [
    	'coordinates' => new Point($faker->latitude(-90, 90), $faker->longitude(-180, 180)),
    	'map_zoom' => $faker->randomFloat(6, 0, 20),
    	'name' => $faker->text(100),
    	'info' => $faker->text(500),

        'visibility' => $faker->randomElement(array_keys(config("mapping.visibility"))),
    	'published_at' => now(),
    ];
});
