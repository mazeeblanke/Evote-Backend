<?php

namespace Tests\Feature;

use App\User;
use App\Campaign;
use Tests\TestCase;
use App\CampaignPosition;
use App\CampaignPositionNormination;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use phpDocumentor\Reflection\Types\Boolean;

class CampaignTest extends TestCase
{

    use RefreshDatabase;


    // /**
    //  *
    //  *
    //  * @return void
    //  */
    // public function testACampaignWithoutACampaignpositionCannotBeSetAsActive()
    // {

    //     $user = factory(User::class)->create();

    //     $campaign = factory(Campaign::class)->create();

    //     $response = $this
    //         ->actingAs($user)
    //         ->json('POST', 'api/campaigns/setActiveCampaign', [
    //             'campaignId' => $campaign->id
    //         ]);

    //     $response
    //         ->assertStatus(401)
    //         ->assertJsonFragment([
    //             'message' => 'At least one campaign position must be created'
    //         ]);
    // }



    // /**
    //  *
    //  *
    //  * @return void
    //  */
    // public function testACampaignHavingACampaignPositionWithoutANorminationCannotBeSetAsActive()
    // {

    //     $user = factory(User::class)->create();

    //     $campaign = factory(Campaign::class)->create();

    //     $campaignPositions = $campaign
    //         ->campaign_positions()
    //         ->saveMany(
    //             factory(CampaignPosition::class, 5)
    //                 ->create([ 'campaign_id' => $campaign->id])
    //                 ->each(function($campaignPosition, $i) use ($campaign) {
    //                     if ($i < 3) {
    //                         $campaignPosition
    //                         ->norminations()
    //                         ->saveMany(
    //                             factory(CampaignPositionNormination::class, 5)->create([
    //                                 'votee_id' => 1,
    //                                 'campaign_position_id' => $campaignPosition->id,
    //                                 'campaign_id' => $campaign->id
    //                             ])
    //                         );
    //                     }
    //                 })
    //         );

    //     $response = $this
    //         ->actingAs($user)
    //         ->json('POST', 'api/campaigns/setActiveCampaign', [
    //             'campaignId' => $campaign->id
    //         ]);

    //     $response
    //         ->assertStatus(401)
    //         ->assertJsonFragment([
    //             'message' => 'At least one normainations has to be made for every campaign position'
    //         ]);
    // }


    /**
     *
     *
     * @return void
     */
    public function testACampaignWithCampaignPositionsHavingAtLeastOneNorminationCanBeSetAsActive()
    {

        $user = factory(User::class)->create();

        $campaign = factory(Campaign::class)->create();

        $campaignPositions = $campaign
            ->campaign_positions()
            ->saveMany(
                factory(CampaignPosition::class, 5)
                    ->create([ 'campaign_id' => $campaign->id])
                    ->each(function($campaignPosition, $i) use ($campaign) {
                        $campaignPosition
                            ->norminations()
                            ->saveMany(
                                factory(CampaignPositionNormination::class, 5)->create([
                                    'votee_id' => 1,
                                    'campaign_position_id' => $campaignPosition->id,
                                    'campaign_id' => $campaign->id
                                ])
                            );
                    })
            );

        $response = $this
            ->actingAs($user)
            ->json('POST', 'api/campaigns/setActiveCampaign', [
                'campaignId' => $campaign->id
            ]);

        $this->assertTrue(
            (int) $campaign->fresh()->active === 1
        );

        $response
            ->assertStatus(201)
            ->assertSuccessful()
            ->assertJsonFragment([
                'message' => 'Campaign successfully set as active'
            ]);
    }
}
