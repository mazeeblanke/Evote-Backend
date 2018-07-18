<?php

namespace App\Http\Controllers;

use App\CampaignPosition;
use Illuminate\Http\Request;
use App\CampaignPositionNormination;
use Illuminate\Support\Facades\Validator;

class CampaignPositionNorminationController extends Controller
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
        $campaign_position_id = $request->input('campaign_position_id');
        $campaign_id = $request->input('campaign_id');
        $builder = CampaignPositionNormination::with(['campaign', 'campaign_position', 'votee']);

        if ($campaign_id) {
            $builder = $builder->where('campaign_id', $campaign_id);
        }

        if ($campaign_position_id) {
            $builder = $builder->where('campaign_position_id', $campaign_position_id);
        }

        $results = $builder->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'message' => 'successfully fetched results',
            'data' => $results
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @todo also verify that each of the ids exist
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'norminations' => 'required',
            'campaign_position_id' => 'required|integer',
            'norminations.*.votee_id' => 'required|integer',
            'norminations.*.campaign_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'status_code' => 422
            ], 422);
        }

        $campaignPosition = CampaignPosition::whereId($request->campaign_position_id)->first();

        $campaignPositionNormination = $campaignPosition
                                        ->norminations()
                                        ->createMany($request->norminations)
                                        ->load(['votee','campaign_position', 'campaign']);


        return response()->json([
            'message' => 'Successfully Created',
            'data' => $campaignPositionNormination
        ], 202);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CampaignPositionNormination  $campaignPositionNormination
     * @return \Illuminate\Http\Response
     */
    public function show(CampaignPositionNormination $campaignPositionNormination)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CampaignPositionNormination  $campaignPositionNormination
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CampaignPositionNormination $campaignPositionNormination)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CampaignPositionNormination  $campaignPositionNormination
     * @return \Illuminate\Http\Response
     */
    public function destroy(CampaignPositionNormination $campaignPositionNormination)
    {
        //
    }
}
