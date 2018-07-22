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
    public function destroy($id)
    {
        //
    }
}
