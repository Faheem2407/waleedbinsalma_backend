<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use App\Traits\ApiResponse;


class GuestMiddleware
{
    /**
     * Handle an incoming request.
     */
    use ApiResponse;
    
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if ($token = JWTAuth::getToken()) {
                $user = JWTAuth::authenticate($token);
            }
        } catch (TokenBlacklistedException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Token has been blacklisted.',
                'data' => null
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Token is invalid.',
                'data' => null
            ], 401);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Token has expired.',
                'data' => null
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized or malformed token.',
                'data' => null
            ], 401);
        }

        return $next($request);
    }
}
