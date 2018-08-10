<?php

namespace Tests\Feature;

use App\User;
use App\Campaign;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CampaignPositionTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Test that a campaign positionn cannot be created twice for a certain campaign
     */
    public function testACampaignPostionCannotBeCreatedTwiceForACertainCampaign()
    {
        $user = factory(User::class)->create();
        $firstCampaign = factory(Campaign::class)->create();


        $response = $this
            ->actingAs($user, 'api')
            ->json('POST', 'api/campaign-positions', [
                "campaign_id" => $firstCampaign->id,
                "positions"   => [["name" => "ertere","description" => "sdfsd"]]
            ]);
        $response->assertSuccessful();


        $anotherCampaign = factory(Campaign::class)->create();
        $response = $this
            ->actingAs($user, 'api')
            ->json('POST', 'api/campaign-positions', [
                "campaign_id" => $anotherCampaign->id,
                "positions"   => [["name" => "ertere","description" => "sdfsd"]]
            ]);
        $response->assertSuccessful();


        $response = $this
            ->actingAs($user, 'api')
            ->json('POST', 'api/campaign-positions', [
                "campaign_id" => $firstCampaign->id,
                "positions"   => [["name" => "ertere","description" => "sdfsd"]]
            ]);
        $response
            ->assertJsonFragment([
                'message' => [
                    'positions.0.name' => [
                        'This campaign position name has already been taken for this campaign.'
                    ]
                ]
            ])
            ->assertStatus(422);

    }
}
