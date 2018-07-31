<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/signup', 'SignupController@create');
Route::post('/login', 'LoginController@create');
Route::get('/votes/liveVote', 'VoteController@liveVote');
Route::patch('/updateUserRoles', 'UserController@updateRoles'); //to be protected by middleware
Route::middleware('authenticate')->patch('/verifyUser', 'UserController@verify'); //to be protected by middleware
Route::middleware('authenticate')->get('/me', 'UserController@me'); //to be protected by middleware

Route::apiResources([
    'users' => 'UserController',
    'campaigns' => 'CampaignController',
    'campaign-positions' => 'CampaignPositionController',
    'campaign-position-norminations' => 'CampaignPositionNorminationController',
    'votes' => 'VoteController'
]);