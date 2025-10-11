<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanctumAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get token from Authorization header
        $token = $request->bearerToken();

        // Check if token exists
        if (!$token) {
            return response()->json([
                'status' => false,
                'statusCode' => '401',
                'message' => 'Unauthorized. API token not provided.',
                'data' => [],
                'error' => 'TOKEN_MISSING'
            ], 401);
        }

        // Authenticate using Sanctum
        $user = $request->user('sanctum');

        if (!$user) {
            return response()->json([
                'status' => false,
                'statusCode' => '401',
                'message' => 'Unauthorized. Invalid or expired API token.',
                'data' => [],
                'error' => 'TOKEN_INVALID'
            ], 401);
        }

        // Check if user is active
        if (isset($user->status) && $user->status !== 'active') {
            return response()->json([
                'status' => false,
                'statusCode' => '403',
                'message' => 'Forbidden. Your account has been ' . $user->status . '.',
                'data' => [],
                'error' => 'ACCOUNT_' . strtoupper($user->status)
            ], 403);
        }

        // Set user to request for helper functions (userId(), currentUser(), etc.)
        $request->attributes->set('user', $user);

        // Token is valid, continue
        return $next($request);
    }
}
