<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CustomerAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'status' => false,
                'statusCode' => '401',
                'message' => 'Unauthorized. Token is missing.',
                'data' => []
            ], 401);
        }

        // Find token in customer_tokens table
        $tokenRecord = DB::table('customer_tokens')
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$tokenRecord) {
            return response()->json([
                'status' => false,
                'statusCode' => '401',
                'message' => 'Unauthorized. Invalid or expired token.',
                'data' => []
            ], 401);
        }

        // Get customer
        $customer = DB::table('customers')
            ->where('id', $tokenRecord->customer_id)
            ->first();

        if (!$customer) {
            return response()->json([
                'status' => false,
                'statusCode' => '401',
                'message' => 'Unauthorized. Customer not found.',
                'data' => []
            ], 401);
        }

        // Check if customer is active
        if ($customer->status !== 'active') {
            return response()->json([
                'status' => false,
                'statusCode' => '403',
                'message' => 'Your account has been ' . $customer->status . '.',
                'data' => []
            ], 403);
        }

        // Update last used timestamp
        DB::table('customer_tokens')
            ->where('id', $tokenRecord->id)
            ->update(['last_used_at' => now()]);

        // Attach customer to request
        $request->merge(['customer' => $customer]);
        $request->setUserResolver(function () use ($customer) {
            return $customer;
        });

        return $next($request);
    }
}
