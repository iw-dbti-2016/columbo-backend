<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use TravelCompanion\Currency;

$factory->define(Currency::class, function (Faker $faker) {
    return [
		'id' => $faker->unique()->currencyCode,

		'name' => Str::random(25),
		'symbol' => $faker->asciify('*'),

		'decimals' => 2,
		'decimal_spacer' => ',',
		'thousand_spacer' => ' ',
    ];
});
