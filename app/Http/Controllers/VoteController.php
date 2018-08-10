<?php

namespace App\Http\Controllers;

use App\User;
use App\Vote;
use App\Campaign;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $user->votes()->createMany($request->votes);

        $user = User::with(['votes' => function($query) use($request) {
            $query->whereHas('normination', function ($query) use ($request) {
                $query->where('campaign_id', $request->campaign_id);
            })->with(['normination' => function($query) use ($request) {
                $query->with(['votee', 'campaign_position'])->where('campaign_id', $request->campaign_id);
            }]);
        }])->whereId($user->id)->first();

        return response()->json([
            'data' =>  $user,
            'message' => 'Successfully voted!'
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function liveVote(Request $request)
    {
        $castedVotes = null;
        $loggedInUser = auth()->user();
        $campaign = Campaign::with([
            'campaign_positions' => function ($query) {
                $query->with('norminations.votee')->orderBy('id', 'desc');
            }
        ])
        ->whereActive(1)
        ->first();
        $norminationIds = collect($campaign['campaign_positions'])->map(function($p) {
            return $p['norminations']->map(function($n) {
                return $n->id;
            })->toArray();
        })->toArray();
        if ($campaign) {

            $norminationIds = array_merge(...$norminationIds);
            $loggedinHasVoted = Vote::whereVoterId($loggedInUser->id)
            ->whereIn('normination_id', $norminationIds)
            ->exists();

            if ($loggedinHasVoted)
            {
                $castedVotes = Vote::whereHas('normination', function($query) use($campaign) {
                    $query->where('campaign_id', $campaign->id);
                })->with(['normination' => function($query) use($campaign) {
                    $query->with(['votee', 'campaign_position'])->where('campaign_id', $campaign->id);
                }])->whereVoterId($loggedInUser->id)->get();

            };

        }
        return response()->json([
            'message' => 'Succesfully fetched results',
            'campaign' => $campaign,
            'votes' => $castedVotes
        ], 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
