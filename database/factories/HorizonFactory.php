<?php

use Faker\Generator as Faker;

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
/** @var \Illuminate\Database\Eloquent\Factory $factory */

$factory->define(\NetLinker\FairQueue\Sections\Horizons\Models\Horizon::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'memory_limit' => 1024,
        'trim_recent' => 2880,
        'trim_recent_failed' =>2880,
        'trim_failed' => 10080,
        'trim_monitored' => 2880,
        'active' => true,
    ];
});


