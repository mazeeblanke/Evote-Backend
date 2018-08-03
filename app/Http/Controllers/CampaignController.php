<?php

namespace App\Http\Controllers;

use App\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = $request->page ?: 1;
        $limit = $request->limit ?: 10;
        $builder = Campaign::query();

        if ($search = $request->search) {
            $columns = ['name', 'description'];
            foreach($columns as $column){
                $builder = $builder->orWhere($column, 'LIKE', '%' . $search . '%');
            }
        }

        $campaigns = $builder->orderBy('id', 'desc')->paginate($limit, ['*'], 'page', $page);
        return response()->json($campaigns, 200);
    }

    /**
     *  Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:campaigns',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'status_code' => 422
            ], 422);
        }


        $campaign = Campaign::create($request->all());

        return response()->json([
            'message' => 'Successfully Created',
            'data' => $campaign
        ], 202);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Campaign $campaign
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $campaign = Campaign::with([
            'campaign_positions' => function ($query) {
                $query->with('norminations.votee')->orderBy('id', 'desc');
            }
        ])
        ->where('id', $id)
        ->first();
        return response()->json([
            'message' => 'Succesfully fetched results',
            'data' => $campaign
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Campaign $campaign)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'string',
            'start_date' => 'date',
            'end_date' => 'date'
        ]);

        //if no normination and at least one norminee/votee and active is true throw validation error

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'status_code' => 422
            ], 422);
        }

        $campaign->update($request->all());

        return response()->json([
            'message' => 'Successfully updated !',
            'data' => $campaign->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Campaign $campaign)
    {
        if ($campaign->delete())
        {
            return response()->json([
                'message' => 'Successfully deleted'
            ], 202);
        }

    }


    public function disableActiveCampaign (Request $request) {
        if (Campaign::whereActive(1)->update(['active' => 0]))
        {
            return response()->json([
                'message' => 'Successfully disabled active campaign'
            ], 202);
        }

        return response()->json([
            'message' => 'Unable to disable active campaign'
        ], 400);

    }

    /**
     *
     * Set an active campaign
     *
     */
    public function setActiveCampaign(Request $request)
    {
       $builder = Campaign::whereId($request->campaignId);
       $data = null;

       $campaign = $builder
        ->has('campaign_positions')
        ->withCount('campaign_positions')
        ->with(['campaign_positions' => function ($query) {
            $query->whereHas('norminations');
        }])->first();

        if (!$campaign) {
            $status = 401;
            $message = 'At least one campaign position must be created';
        } else if (
           (int) $campaign->campaign_positions_count !==
           $campaign->campaign_positions->count()
        ) {
            $status = 401;
            $message = 'At least one normination has to be made for every campaign position';
        } else if ($campaign->update(['active' => 1])) {
            $campaign->where('id', '!=', $request->campaignId)->update(['active' => 0]);
            $status = 201;
            $message = 'Campaign successfully set as active';
            $data = $campaign->fresh();
        };

        return response()->json([
            'message' => $message,
            'data'    => $data
        ], $status);
    }

}
