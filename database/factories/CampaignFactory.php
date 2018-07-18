<?php

use Faker\Generator as Faker;

$factory->define(App\Campaign::class, function (Faker $faker) {
    return [
        'name' => $faker->firstName,
        'description' => $faker->sentence,
        'start_date' => $faker->dateTimeThisCentury->format('Y-m-d'),
        'end_date' => $faker->dateTimeThisCentury->format('Y-m-d'),
    ];
});
