<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Models\CastMember::class, function (Faker $faker) {
    return [
        'name' => $faker->firstName,
        'type' => rand(1,2)
    ];
});
