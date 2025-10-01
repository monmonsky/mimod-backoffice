<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission Permission required to access the route
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = currentUser();

        // If no user, redirect to login
        if (!$user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.'
                ], 401);
            }

            return redirect('/login')->with('error', 'Please login first.');
        }

        // Check if user has permission
        if (!hasPermission($permission)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to access this resource.',
                    'required_permission' => $permission
                ], 403);
            }

            // For web requests, redirect to dashboard with error (avoid infinite loop)
            // If current request is dashboard and user doesn't have permission, show 403 page
            if ($request->is('dashboard')) {
                abort(403, 'You do not have permission to access dashboard.');
            }

            return redirect('/dashboard')->with('error', 'You do not have permission to access that page.');
        }

        return $next($request);
    }
}
