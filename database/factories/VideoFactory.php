<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Video;
use Faker\Generator as Faker;

$factory->define(Video::class, function (Faker $faker) {
    $ratings = \App\Models\Video::ratings();
    return [
        'title' => $faker->sentence(3),
        'description' => $faker->sentence(10),
        'year_launched' => rand(1985, 2022),
        'opened' => rand(0, 1),
        'rating' => $ratings[array_rand($ratings)],
        'duration' => rand(1, 30),
    ];
});
