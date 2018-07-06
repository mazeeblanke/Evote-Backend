<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/signup', function(Request $request) {
    return response()->json([
        'id' => 23
    ]);
});

Route::get('/w', function () {
    return response()->json([
        'w233e' => 2332
    ]);
    // dd(app());
    // return view('welcome');
});
