<?php

namespace App\Http\Middleware;

use App\Repositories\Contracts\UserRepositoryInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateToken
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        // If no bearer token, try to get from cookie (for Web)
        if (!$token) {
            $token = $request->cookie('auth_token');
        }

        // Check if token exists
        if (!$token) {
            if ($request->expectsJson() || str_starts_with($request->path(), 'api/')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated. Token not provided.',
                    'code' => 'TOKEN_MISSING'
                ], 401);
            }
        }

        // Find user by token
        $user = $this->userRepository->findByToken($token);

        if (!$user) {
            if ($request->expectsJson() || str_starts_with($request->path(), 'api/')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated. Invalid or expired token.',
                    'code' => 'TOKEN_INVALID'
                ], 401);
            }
        }

        // Check account status
        if ($user->status !== 'active') {
            // Revoke token
            $this->userRepository->revokeToken($token);

            if ($request->expectsJson() || str_starts_with($request->path(), 'api/')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your account has been ' . $user->status . '.',
                    'code' => 'ACCOUNT_' . strtoupper($user->status)
                ], 403);
            }
        }

        // Convert user object to array properly (including dynamic properties)
        $userData = (array) $user;

        // Ensure role_id is included (important for permission checks)
        if (isset($user->role_id)) {
            $userData['role_id'] = $user->role_id;
        }

        // Ensure role and permissions are included
        if (isset($user->role)) {
            $userData['role'] = $user->role;
        }
        if (isset($user->role_display)) {
            $userData['role_display'] = $user->role_display;
        }
        if (isset($user->role_priority)) {
            $userData['role_priority'] = $user->role_priority;
        }
        if (isset($user->permissions)) {
            $userData['permissions'] = $user->permissions;
        }

        // Attach user to request
        $request->merge(['user' => $userData]);

        return $next($request);
    }
}
