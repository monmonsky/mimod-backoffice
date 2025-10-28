<?php

use App\Http\Controllers\Customer\CustomerAuthController;
use App\Http\Controllers\Customer\CustomerProfileController;
use App\Http\Controllers\Customer\CustomerAddressController;
use App\Http\Controllers\Customer\CustomerOrderController;
use App\Http\Controllers\Customer\CustomerDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Customer API Routes
|--------------------------------------------------------------------------
|
| These routes are for customer-facing APIs (e-commerce frontend).
| Customers can register, login, manage profile, and place orders.
|
*/

// Public routes (no authentication required)
Route::prefix('auth')->group(function () {
    // Registration & Login
    Route::post('/register', [CustomerAuthController::class, 'register']);
    Route::post('/login', [CustomerAuthController::class, 'login']);

    // Password Reset
    Route::post('/forgot-password', [CustomerAuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [CustomerAuthController::class, 'resetPassword']);

    // Email Verification
    Route::post('/verify-email', [CustomerAuthController::class, 'verifyEmail']);
    Route::post('/resend-verification', [CustomerAuthController::class, 'resendVerification']);

    // OTP
    Route::post('/send-otp', [CustomerAuthController::class, 'sendOtp']);
    Route::post('/verify-otp', [CustomerAuthController::class, 'verifyOtp']);

    // Social Login (optional)
    Route::post('/social/google', [CustomerAuthController::class, 'loginWithGoogle']);
    Route::post('/social/facebook', [CustomerAuthController::class, 'loginWithFacebook']);
});

// Protected routes (authentication required)
Route::middleware('customer.auth')->group(function () {

    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [CustomerAuthController::class, 'logout']);
        Route::post('/logout-all', [CustomerAuthController::class, 'logoutAll']);
        Route::get('/me', [CustomerAuthController::class, 'me']);
        Route::get('/sessions', [CustomerAuthController::class, 'sessions']);
        Route::post('/refresh', [CustomerAuthController::class, 'refreshToken']);
    });

    // Profile Management
    Route::prefix('profile')->group(function () {
        Route::get('/', [CustomerProfileController::class, 'show']);
        Route::put('/', [CustomerProfileController::class, 'update']);
        Route::put('/change-password', [CustomerProfileController::class, 'changePassword']);
        Route::post('/upload-avatar', [CustomerProfileController::class, 'uploadAvatar']);
        Route::delete('/avatar', [CustomerProfileController::class, 'deleteAvatar']);
        Route::delete('/account', [CustomerProfileController::class, 'deleteAccount']);
    });

    // Address Management
    Route::prefix('addresses')->group(function () {
        Route::get('/', [CustomerAddressController::class, 'index']);
        Route::post('/', [CustomerAddressController::class, 'store']);
        Route::get('/{id}', [CustomerAddressController::class, 'show']);
        Route::put('/{id}', [CustomerAddressController::class, 'update']);
        Route::delete('/{id}', [CustomerAddressController::class, 'destroy']);
        Route::post('/{id}/set-default', [CustomerAddressController::class, 'setDefault']);
    });

    // Loyalty Points
    Route::prefix('loyalty')->group(function () {
        Route::get('/points', [CustomerProfileController::class, 'getLoyaltyPoints']);
        Route::get('/history', [CustomerProfileController::class, 'getLoyaltyHistory']);
    });

    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [CustomerDashboardController::class, 'index']);
        Route::get('/order-stats', [CustomerDashboardController::class, 'orderStats']);
        Route::get('/activities', [CustomerDashboardController::class, 'activities']);
        Route::get('/notifications', [CustomerDashboardController::class, 'notifications']);
    });

    // Orders
    Route::prefix('orders')->group(function () {
        Route::get('/', [CustomerOrderController::class, 'index']);
        Route::post('/', [CustomerOrderController::class, 'store']);
        Route::get('/{id}', [CustomerOrderController::class, 'show']);
        Route::post('/{id}/cancel', [CustomerOrderController::class, 'cancel']);
        Route::get('/track/{orderNumber}', [CustomerOrderController::class, 'track']);
    });
});
