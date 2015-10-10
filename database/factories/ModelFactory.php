<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/
use App\User;

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Question::class, function (Faker\Generator $faker) {

    $user_count = User::count();

    return [
        'text' => $faker->text,
        'user_id' => $faker->numberBetween(1, $user_count),
        'location' => $faker->streetAddress,
        //TODO make some true later.
        'current_status' => false,
    ];
});