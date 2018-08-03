<?php

use App\User;
use App\Campaign;
use App\CampaignPosition;
use Faker\Generator as Faker;

$factory->define(App\CampaignPositionNormination::class, function (Faker $faker) {
    $campaignId = factory(Campaign::class)
                        ->create()
                        ->id;

    $campaignPositionId = factory(CampaignPosition::class)
                            ->create([
                                'campaign_id' => $campaignId
                            ])
                            ->id;
    return [
        'votee_id' => function () {
            return factory(User::class)->create()->id;
        },
        'campaign_position_id' => $campaignPositionId,
        'campaign_id' => $campaignId
    ];
});
