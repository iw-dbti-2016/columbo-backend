<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Faker\Generator as Faker;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Support\Str;
use TravelCompanion\User;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'middle_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'username' => $faker->unique()->userName,
        'email' => $faker->unique()->safeEmail,

        'telephone' => $faker->phoneNumber,
        'image' => $faker->slug,
        'home_location' => new Point($faker->latitude(-90,90), $faker->longitude(-180,180)),
        'birth_date' => $faker->date(),
        'description' => $faker->text(500),

        'language' => $faker->locale,

        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
    ];
});
