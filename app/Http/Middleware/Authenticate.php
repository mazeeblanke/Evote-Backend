<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

// use Tymon\JWTAuth\Facades\JWTAuth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // dd('sdnsd');
        try {

            if (!$user = \JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (TokenExpiredException $e) {

            return response()->json(['message' => 'token_expired'], 401);

        } catch (TokenInvalidException $e) {

            return response()->json(['message' => 'token_invalid'], 401);

        } catch (JWTException $e) {

            return response()->json([
                'message' => 'token_absent',
                'code' => 450
            ], 422);

        }

        // dd($user);
        // dd(\Auth::user());

        // the token is valid and we have found the user via the sub claim
        // return response()->json(compact('user'));
        return $next($request);
    }
}
