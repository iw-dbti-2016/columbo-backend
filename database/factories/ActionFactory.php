<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Columbo\Action;

$factory->define(Action::class, function (Faker $faker) {
    return [
    	'uuid' => Str::uuid(),
        'action' => $faker->randomElement(array_keys(config("mapping.action"))),
        'executed_at' => $faker->datetime(),
    ];
});
