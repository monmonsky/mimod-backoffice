<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Settings\GeneralSettingsApiController;
use App\Http\Controllers\Api\Settings\PaymentSettingsApiController;
use App\Http\Controllers\Api\Settings\ShippingSettingsApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Public routes
Route::post('auth/login', [AuthController::class, 'login']);

// Auth routes (using custom auth.token middleware)
Route::middleware('auth.token')->prefix('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('logout-all', [AuthController::class, 'logoutAll']);
    Route::get('me', [AuthController::class, 'me']);
    Route::get('sessions', [AuthController::class, 'sessions']);
});

// Settings routes (using custom Sanctum middleware)
Route::middleware('auth.sanctum')->group(function () {
    Route::prefix('settings')->group(function () {
        // General Settings
        Route::prefix('general')->group(function () {
            Route::get('/', [GeneralSettingsApiController::class, 'index']);
            Route::get('/{key}', [GeneralSettingsApiController::class, 'show']);
            Route::put('/{key}', [GeneralSettingsApiController::class, 'update']);

            // Specific endpoints
            Route::get('/store/info', [GeneralSettingsApiController::class, 'getStoreInfo']);
            Route::get('/email/settings', [GeneralSettingsApiController::class, 'getEmailSettings']);
            Route::get('/seo/settings', [GeneralSettingsApiController::class, 'getSeoSettings']);
            Route::get('/system/config', [GeneralSettingsApiController::class, 'getSystemConfig']);
        });

        // Payment Settings
        Route::prefix('payment')->group(function () {
            Route::get('/', [PaymentSettingsApiController::class, 'index']);
            Route::get('/{key}', [PaymentSettingsApiController::class, 'show']);
            Route::put('/{key}', [PaymentSettingsApiController::class, 'update']);

            // Specific endpoints
            Route::get('/tax/settings', [PaymentSettingsApiController::class, 'getTaxSettings']);
            Route::get('/midtrans/config', [PaymentSettingsApiController::class, 'getMidtransConfig']);
            Route::get('/methods/list', [PaymentSettingsApiController::class, 'getPaymentMethods']);
        });

        // Shipping Settings
        Route::prefix('shipping')->group(function () {
            Route::get('/', [ShippingSettingsApiController::class, 'index']);
            Route::get('/{key}', [ShippingSettingsApiController::class, 'show']);
            Route::put('/{key}', [ShippingSettingsApiController::class, 'update']);

            // Specific endpoints
            Route::get('/origin/address', [ShippingSettingsApiController::class, 'getOriginAddress']);
            Route::get('/rajaongkir/config', [ShippingSettingsApiController::class, 'getRajaOngkirConfig']);
            Route::get('/methods/list', [ShippingSettingsApiController::class, 'getShippingMethods']);
        });
    });
});
