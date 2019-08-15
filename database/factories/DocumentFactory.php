<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use TravelCompanion\Document;

$factory->define(Document::class, function (Faker $faker) {
    return [
        'name' => Str::random(30),
        'document' => $faker->slug,

        'visibility' => $faker->numberBetween(0, 255),
    	'published_at' => now(),
    ];
});
