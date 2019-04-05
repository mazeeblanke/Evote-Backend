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
        $builder = User::query()->with(['norminations', 'roles']);
        $columns = ['email', 'username'];
        $confirmed = $request->confirmed;

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

        if (isset($confirmed))
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
        return response()->json(\Auth::user()->load(['roles']));
    }

    public function verify(Request $request)
    {
        $data = null;
        $statusCode = 422;
        $message = 'You are not authorized';

        if ($request->user()->can('verify', User::class)){

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
            $message = 'Succesfully verified user';
            $data = $user->fresh()->load('roles');
            $statusCode = 200;
        }

        return response()->json([
            'message' => $message,
            'data' => $data
        ], $statusCode);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $userId)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|required|email',
            'password' => 'sometimes|required|string',
            'username' => 'sometimes|required|string',
            'firstname' => 'sometimes|required|string',
            'lastname' => 'sometimes|required|string',
            // 'security_question' => 'json',
            'address' => 'sometimes|required|string',
            'city' => 'sometimes|required|string',
            'state' => 'sometimes|required|string',
            'country' => 'sometimes|required|string',
            'phone' => 'sometimes|required|string',
            'date_of_birth' => 'sometimes|required|date',
            'username' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255',
            'role'  => 'sometimes|Array',
            'confirmed' => 'sometimes|required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'status_code' => 422
            ], 422);
        }

        if ($user = User::whereId($userId)->first()){

            $payload = array_merge($request->except(['confirmed']), [
                'locked_profile' => $request->locked_profile ?? 1
            ]);

            $hasAdminRole = $request->user()->can('performAdminRole', User::class);

            if ($hasAdminRole){

                if ($request->has('confirmed')) {
                    $payload = array_merge($request->only('confirmed'), $payload);
                }

                if ($request->roles && count($request->roles) >= 1) {
                    $user->syncRoles($request->roles);
                }
            }

            if (!$hasAdminRole && ($request->roles || $request->confirmed)) {
                return response()->json([
                    'message' => 'You are not authorized!!'
                ], 422);
            }

            $user->update($payload);

            return response()->json([
                'message' => 'Successfully updated !',
                'data' => $user->fresh()->load('roles')
            ]);

        } else {
            return response()->json([
                'message' => 'Could not be updated ! User not found'
            ]);
        }


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
