<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\CampaignPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CampaignPositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = $request->input('page') ?: 1;
        $limit = $request->input('limit') ?: 10;
        $campaign_id = $request->input('campaign_id');
        if ($campaign_id) {
            $campaignPosition = CampaignPosition::where('campaign_id', $campaign_id)->paginate($limit, ['*'], 'page', $page);
        } else {
            $campaignPosition = CampaignPosition::paginate($limit, ['*'], 'page', $page);
        }
        return response()->json($campaignPosition, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'positions' => 'required|array',
            'campaign_id' => 'required|integer',
            'positions.*.name' => 'required|string|max:255|unique:campaign_positions',
            'positions.*.description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'status_code' => 422
            ], 422);
        }

        $campaign = Campaign::whereId($request->campaign_id)->first();

        $campaignPosition = $campaign
                            ->campaign_positions()
                            ->createMany($request->positions)
                            ->load(['campaign', 'norminations.votee']);

        return response()->json([
            'message' => 'Successfully Created',
            'data' => $campaignPosition
        ], 202);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(CampaignPosition $campaignPosition)
    {
        $campaignPosition = $campaignPosition->load(['campaign', 'norminations.votee']);
        return response()->json([
            'data' => $campaignPosition
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CampaignPosition $campaignPosition
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CampaignPosition $campaignPosition)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:campaigns',
            'description' => 'required|string',
            'campaign_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'status_code' => 422
            ], 422);
        }

        $campaignPosition->update($request->all());

        return response()->json([
            'message' => 'Successfully updated !',
            'data' => $campaignPosition->fresh()->load(['norminations.votee'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(CampaignPosition $campaignPosition)
    {
        if ($campaignPosition->delete())
        {
            return response()->json([
                'message' => 'Successfully deleted'
            ], 202);
        }
    }
}
