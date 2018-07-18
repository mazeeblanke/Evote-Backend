<?php

use Faker\Generator as Faker;

$factory->define(App\CampaignPostion::class, function (Faker $faker) {
    return [
        'name' => $faker->firstName,
        'description' => $faker->sentence,
        'campaign_id' => 1
    ];
});
