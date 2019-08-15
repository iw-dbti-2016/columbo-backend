<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use TravelCompanion\Action;
use Faker\Generator as Faker;

$factory->define(Action::class, function (Faker $faker) {
    return [
        'action' => $faker->numberBetween(0, 255),
    ];
});
