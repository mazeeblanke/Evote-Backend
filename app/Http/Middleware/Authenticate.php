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

        try {

            if (!$user = $request->user() ?: \JWTAuth::parseToken()->authenticate() ) {
                return response()->json(['message' => 'you are not logged in!'], 401);
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

        return $next($request);
    }
}
