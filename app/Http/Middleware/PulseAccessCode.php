<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PulseAccessCode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get access code from query parameter or session
        $code = $request->query('code') ?? $request->session()->get('pulse_access_code');

        // Check if code matches
        $validCode = config('pulse.access_code', 'secret123');

        if ($code === $validCode) {
            // Store in session for next requests
            $request->session()->put('pulse_access_code', $code);
            return $next($request);
        }

        // Show simple form if no/invalid code
        return response()->view('pulse-auth', [
            'error' => $request->query('code') ? 'Invalid access code' : null
        ], 401);
    }
}
