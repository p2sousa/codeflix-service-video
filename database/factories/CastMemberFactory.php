<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Models\CastMember::class, function (Faker $faker) {
    $types = \App\Models\CastMember::typeMembers();
    return [
        'name' => $faker->firstName,
        'type' => $types[array_rand($types)]
    ];
});
