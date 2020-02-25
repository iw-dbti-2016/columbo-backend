<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use TravelCompanion\Role;
use Faker\Generator as Faker;

$factory->define(Role::class, function (Faker $faker) {
    return [
        'name' => $faker->text(50),
        'label' => $faker->text(50),
        'description' => $faker->text(500),
    ];
});
