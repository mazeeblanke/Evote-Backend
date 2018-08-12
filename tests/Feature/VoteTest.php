<?php

namespace Tests\Feature;

use App\User;
use App\Vote;
use App\Campaign;
use Tests\TestCase;
use App\CampaignPosition;
use App\CampaignPositionNormination;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VoteTest extends TestCase
{

    use RefreshDatabase;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAnyUserCanViewVoteResults()
    {
        //given we have an admin
        $admin = factory(User::class)->create();

        //and 2 contestant
        $contestant1 = factory(User::class)->create();
        $contestant2 = factory(User::class)->create();

        //and a created active campaign
        $electionCampaign = factory(Campaign::class)
                                ->create(['active' => true]);

        // create campaign position
        $presidentPosition = factory(CampaignPosition::class)
                                ->create([
                                    'campaign_id' => $electionCampaign->id,
                                    'name' => 'president'
                                ]);

        //norminate the contestants
        foreach([$contestant1, $contestant2] as $key => $contestant) {
            $k = $key + 1;
            ${"normination{$k}"} = factory(CampaignPositionNormination::class)->create([
                'votee_id' => $contestant->id,
                'campaign_position_id' => $presidentPosition->id,
                'campaign_id' => $electionCampaign->id
            ]);
        }

        //and 3 users who voted
        $voter1 = factory(User::class)->create();
        $voter2 = factory(User::class)->create();
        $voter3 = factory(User::class)->create();


        //voter1 and voter2 voted for contestant 1
        factory(Vote::class)->create([
            'normination_id' => $normination1,
            'voter_id' => $voter1
        ]);
        factory(Vote::class)->create([
            'normination_id' => $normination1,
            'voter_id' => $voter2
        ]);


        //voter3 voted for contestant 2
        factory(Vote::class)->create([
            'normination_id' => $normination2,
            'voter_id' => $voter3
        ]);


        //when we visit the results endpoint, we should the exact vote numbers by the users agsint the campaign positions

       $response = $this
                    ->actingAs($admin, 'api')
                    ->get('/api/votes/voteResults');

        $response
            ->assertSuccessful()
            ->assertJsonFragment(
                [
                    $presidentPosition->name => [
                        'votes_count' => ['2', '1'],
                        'contestants' => [$contestant1->username, $contestant2->username]
                    ],
                    'campaignName' => $electionCampaign->name
                ]
            )
            ->assertJsonStructure([
                'message',
                'data' => [
                    $presidentPosition->name => [
                        'votes_count',
                        'contestants'
                    ],
                    'campaignName'
                ]
            ]);

    }
}
