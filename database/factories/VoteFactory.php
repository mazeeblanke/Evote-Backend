<?php

use App\User;
use Faker\Generator as Faker;
use App\CampaignPositionNormination;

$factory->define(App\Vote::class, function (Faker $faker) {
    return [
        'normination_id' => function() {
            return factory(CampaignPositionNormination::class)->create()->id;
        },
        'voter_id' => function() {
            return factory(User::class)->create()->id;
        }
    ];
});
