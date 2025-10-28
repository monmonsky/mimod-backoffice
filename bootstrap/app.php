<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Customer API routes
            Route::prefix('api/customer')
                ->middleware('api')
                ->group(base_path('routes/customer-api.php'));

            // Store API routes (for frontend e-commerce)
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/store-api.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.token' => \App\Http\Middleware\AuthenticateToken::class,
            'auth.sanctum' => \App\Http\Middleware\SanctumAuth::class,
            'customer.auth' => \App\Http\Middleware\CustomerAuth::class,
            'store.api' => \App\Http\Middleware\StoreApiAuth::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'pulse.code' => \App\Http\Middleware\PulseAccessCode::class,
        ]);

        // Disable CSRF for API routes
        $middleware->validateCsrfTokens(except: [
            'api/*'
        ]);

        $middleware->encryptCookies(except: [
            'auth_token',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle authentication exceptions for API
        $exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson() || str_starts_with($request->path(), 'api/')) {
                return response()->json([
                    'status' => false,
                    'statusCode' => '401',
                    'message' => 'Unauthenticated.'
                ], 401);
            }
        });
    })
    ->withSchedule(function ($schedule) {
        // Cleanup expired tokens daily at 3 AM
        $schedule->command('tokens:cleanup')
            ->daily()
            ->at('03:00')
            ->withoutOverlapping();
    })
    ->withEvents(discover: true)
    ->create();
