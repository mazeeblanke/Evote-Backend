<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
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
        //
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
        $validator = Validator::make($request->all(), [
            'email' => 'email',
            'password' => 'string', 
            'username' => 'string',
            'firstname' => 'string',
            'lastname' => 'string',
            'security_question' => 'json',
            'address' => 'string',
            'city' => 'string',
            'state' => 'string',
            'country' => 'string',
            'phone' => 'string',
            'date_of_birth' => 'date',
            'confirmed' => 'boolean',
            'username' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'status_code' => 422
            ], 422);
        }

        $user = $user->update($request->all());

        return response()->json([
            'message' => 'Successfully updated !',
            'data' => $user
        ]);

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
