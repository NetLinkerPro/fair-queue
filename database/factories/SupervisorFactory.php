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

$factory->define(\NetLinker\FairQueue\Sections\Supervisors\Models\Supervisor::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'environment' => 'local',
        'connection' => 'fair-queue',
        'balance' =>'false',
        'min_processes' => 1,
        'max_processes' => 1,
        'priority' => 0,
        'sleep' => 1,
        'active' => true,
    ];
});
