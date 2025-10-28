<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\PersonalAccessToken;

class StoreApiAuth
{
    /**
     * Handle an incoming request for store API access
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  $ability  Required ability/permission
     */
    public function handle(Request $request, Closure $next, ?string $ability = null): Response
    {
        // Get token from Authorization header
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'status' => false,
                'statusCode' => '401',
                'message' => 'Unauthorized. API token not provided.',
                'data' => [],
                'error' => 'TOKEN_MISSING'
            ], 401);
        }

        // Parse token (format: ID|token)
        $tokenParts = explode('|', $token, 2);

        if (count($tokenParts) !== 2) {
            return response()->json([
                'status' => false,
                'statusCode' => '401',
                'message' => 'Unauthorized. Invalid token format.',
                'data' => [],
                'error' => 'TOKEN_INVALID_FORMAT'
            ], 401);
        }

        [$tokenId, $plainToken] = $tokenParts;

        // Find token in database
        $accessToken = PersonalAccessToken::find($tokenId);

        if (!$accessToken) {
            return response()->json([
                'status' => false,
                'statusCode' => '401',
                'message' => 'Unauthorized. Token not found.',
                'data' => [],
                'error' => 'TOKEN_NOT_FOUND'
            ], 401);
        }

        // Verify token hash
        $hashedToken = hash('sha256', $plainToken);

        if (!hash_equals($accessToken->token, $hashedToken)) {
            return response()->json([
                'status' => false,
                'statusCode' => '401',
                'message' => 'Unauthorized. Invalid token.',
                'data' => [],
                'error' => 'TOKEN_INVALID'
            ], 401);
        }

        // Check if token has expired (should be null for lifetime tokens)
        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            return response()->json([
                'status' => false,
                'statusCode' => '401',
                'message' => 'Unauthorized. Token has expired.',
                'data' => [],
                'error' => 'TOKEN_EXPIRED'
            ], 401);
        }

        // Get user associated with token
        $user = $accessToken->tokenable;

        if (!$user) {
            return response()->json([
                'status' => false,
                'statusCode' => '401',
                'message' => 'Unauthorized. User not found.',
                'data' => [],
                'error' => 'USER_NOT_FOUND'
            ], 401);
        }

        // Check if user is active
        if ($user->status !== 'active') {
            return response()->json([
                'status' => false,
                'statusCode' => '403',
                'message' => 'Forbidden. User account is ' . $user->status . '.',
                'data' => [],
                'error' => 'USER_' . strtoupper($user->status)
            ], 403);
        }

        // Check token abilities if specific ability is required
        if ($ability && !$this->tokenCan($accessToken, $ability)) {
            return response()->json([
                'status' => false,
                'statusCode' => '403',
                'message' => 'Forbidden. Token does not have required permission: ' . $ability,
                'data' => [],
                'error' => 'INSUFFICIENT_PERMISSIONS'
            ], 403);
        }

        // Set user to request attributes for helper functions
        $request->attributes->set('user', $user);
        $request->attributes->set('token', $accessToken);

        // Update last_used_at timestamp
        $accessToken->forceFill(['last_used_at' => now()])->save();

        // Token is valid, continue
        return $next($request);
    }

    /**
     * Check if token has specific ability
     */
    private function tokenCan(PersonalAccessToken $token, string $ability): bool
    {
        $abilities = $token->abilities ?? [];

        // If token has '*' ability, it can do everything
        if (in_array('*', $abilities)) {
            return true;
        }

        // Check for specific ability
        if (in_array($ability, $abilities)) {
            return true;
        }

        // Check for wildcard ability (e.g., 'products:*' matches 'products:read')
        $abilityPrefix = explode(':', $ability)[0];
        if (in_array($abilityPrefix . ':*', $abilities)) {
            return true;
        }

        return false;
    }
}
