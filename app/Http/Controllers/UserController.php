<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

/**
 * Controller for user
 */
class UserController extends Controller
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
        $campaignId = $request->campaignId;
        $search = $request->search;
        $available = $request->available; // 1 for available, 0 for not available
        $builder = User::query()->with('norminations');
        $columns = ['email', 'username'];

        if ($search)
        {
            $builder = $builder->where(function($query) use($columns, $search) {
                foreach($columns as $column){
                    $query = $query->orWhere($column, 'LIKE', '%' . $search . '%');
                }
            });
        }

        if ($campaignId)
        {
            $builder = $builder
            ->whereDoesntHave('norminations', function($query) use ($campaignId) {
                $query->where('campaign_id', '=', $campaignId);
            });
        }

        if ($confirmed = $request->confirmed)
        {
            $builder = $builder->whereConfirmed($confirmed);
        }

        $users = $builder->orderBy('id', 'desc')->paginate($limit, ['*'], 'page', $page);
        return response()->json($users, 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response()->json($user->load('roles'), 200);
    }

    public function me()
    {
        return response()->json(\Auth::user());
    }

    public function verify(Request $request) {
        $validator = Validator::make($request->all(), [
            'confirmed' => 'required|boolean',
            'userId' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'status_code' => 422
            ], 422);
        }

        $user = User::find($request->input('userId'));
        $user->confirmed = $request->input('confirmed');
        $user->save();

        return response()->json([
            'message' => 'Succesfully verified user',
            'data' => $user->fresh()->load('roles')
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        // $this->authorize('update', $user);
        // dd(auth());
        $validator = Validator::make($request->all(), [
            'email' => 'email',
            'password' => 'string',
            'username' => 'string',
            'firstname' => 'string',
            'lastname' => 'string',
            // 'security_question' => 'json',
            'address' => 'string',
            'city' => 'string',
            'state' => 'string',
            'country' => 'string',
            'phone' => 'string',
            'date_of_birth' => 'date',
            'username' => 'string|max:255',
            'email' => 'string|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'status_code' => 422
            ], 422);
        }

        $user->update($request->all());

        return response()->json([
            'message' => 'Successfully updated !',
            'data' => $user->fresh()->load('roles')
        ]);

    }

    public function updateRoles(Request $request) {

        $validator = Validator::make($request->all(), [
            'roleNames' => 'required|array',
            'userId' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'status_code' => 422
            ], 422);
        }

        $user = User::find($request->input('userId'));
        $roleNames = $request->input('roleNames');

        $user->syncRoles($roleNames);

        return response()->json([
            'message' => 'succesfully updated user',
            'data' => $user->fresh()->load('roles')
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
