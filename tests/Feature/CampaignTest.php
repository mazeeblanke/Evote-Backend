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
use Carbon\Carbon;

class CampaignTest extends TestCase
{

    use RefreshDatabase;

    public function testCanFetchActiveCampaigns()
    {
        $user = factory(User::class)->create();

        factory(Campaign::class, 10)->create();
        factory(Campaign::class)->create([ 'active' => 1 ]);

        $response = $this
            ->actingAs($user, 'api')
            ->json('GET', 'api/campaigns?active=1');

        $this->assertSame(count($response->getOriginalContent()->items()), 1);
    }

    public function testCanFetchClosedCampaigns()
    {
        $user = factory(User::class)->create();

        // factory(Campaign::class, 10)->create();

        factory(Campaign::class)->create([ 'active' => 1, 'end_date' => Carbon::now()->subDays(23)->format('Y-m-d') ]);
        factory(Campaign::class)->create();

        $response = $this
            ->actingAs($user, 'api')
            ->json('GET', 'api/campaigns?completed=1');

        $this->assertSame(count($response->getOriginalContent()->items()), 1);
    }


    public function testACampaignWithoutACampaignpositionCannotBeSetAsActive()
    {

        $user = factory(User::class)->create();

        $campaign = factory(Campaign::class)->create();

        $response = $this
            ->actingAs($user, 'api')
            ->json('POST', 'api/campaigns/setActiveCampaign', [
                'campaignId' => $campaign->id
            ]);

        $response
            ->assertStatus(401)
            ->assertJsonFragment([
                'message' => 'At least one campaign position must be created'
            ]);
    }



    /**
     *
     *
     * @return void
     */
    public function testACampaignHavingACampaignPositionWithoutANorminationCannotBeSetAsActive()
    {

        $user = factory(User::class)->create();

        $campaign = factory(Campaign::class)->create();

        $campaignPositions = $campaign
            ->campaign_positions()
            ->saveMany(
                factory(CampaignPosition::class, 5)
                    ->create([ 'campaign_id' => $campaign->id])
                    ->each(function($campaignPosition, $i) use ($campaign) {
                        if ($i < 3) {
                            $campaignPosition
                            ->norminations()
                            ->saveMany(
                                factory(CampaignPositionNormination::class, 5)->create([
                                    'votee_id' => 1,
                                    'campaign_position_id' => $campaignPosition->id,
                                    'campaign_id' => $campaign->id
                                ])
                            );
                        }
                    })
            );

        $response = $this
            ->actingAs($user)
            ->json('POST', 'api/campaigns/setActiveCampaign', [
                'campaignId' => $campaign->id
            ]);

        $response
            ->assertStatus(401)
            ->assertJsonFragment([
                'message' => 'At least one normination has to be made for every campaign position',
                'data' => null
            ]);
    }


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
