<?php

use App\Campaign;
use Faker\Generator as Faker;

$factory->define(App\CampaignPosition::class, function (Faker $faker) {
    return [
        'name' => $faker->firstName,
        'description' => $faker->sentence,
        'campaign_id' => function () {
            return factory(Campaign::class)->create()->id;
        }
    ];
});
