<?php

use App\Http\Controllers\Api\Catalog\ProductApiController;
use App\Http\Controllers\Api\Catalog\CategoryApiController;
use App\Http\Controllers\Api\Catalog\BrandApiController;
use App\Http\Controllers\Api\SettingsApiController;
use App\Http\Controllers\Api\Appearance\Navigation\MenuController as NavigationMenuController;
use App\Http\Controllers\Store\StoreOrderController;
use App\Http\Controllers\Store\StorePaymentMethodController;
use App\Http\Controllers\Store\StoreShippingMethodController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Store API Routes (Frontend E-commerce)
|--------------------------------------------------------------------------
|
| These routes are for the store frontend (e-commerce website).
| Uses store.api middleware with lifetime session tokens.
| Read-only access to catalog data and settings.
|
*/

// All routes use store.api middleware (lifetime session tokens)
Route::middleware('store.api')->prefix('store')->group(function () {

    // ============================================
    // PRODUCTS
    // ============================================
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductApiController::class, 'index'])
            ->middleware('store.api:products:read');
        Route::get('/{id}', [ProductApiController::class, 'show'])
            ->middleware('store.api:products:read');
    });

    // ============================================
    // CATEGORIES
    // ============================================
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryApiController::class, 'index'])
            ->middleware('store.api:categories:read');
        Route::get('/tree', [CategoryApiController::class, 'tree'])
            ->middleware('store.api:categories:read');
        Route::get('/parents', [CategoryApiController::class, 'parents'])
            ->middleware('store.api:categories:read');
        Route::get('/{id}', [CategoryApiController::class, 'show'])
            ->middleware('store.api:categories:read');
        Route::get('/{parentId}/children', [CategoryApiController::class, 'children'])
            ->middleware('store.api:categories:read');
    });

    // ============================================
    // BRANDS
    // ============================================
    Route::prefix('brands')->group(function () {
        Route::get('/', [BrandApiController::class, 'index'])
            ->middleware('store.api:brands:read');
        Route::get('/{id}', [BrandApiController::class, 'show'])
            ->middleware('store.api:brands:read');
    });

    // ============================================
    // SETTINGS
    // ============================================
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsApiController::class, 'index'])
            ->middleware('store.api:settings:read');
        Route::get('/{key}', [SettingsApiController::class, 'show'])
            ->middleware('store.api:settings:read');
    });

    // ============================================
    // NAVIGATION MENUS
    // ============================================
    // Public menu endpoint for store frontend (header, footer, etc.)
    Route::get('/menus', [NavigationMenuController::class, 'getMenuByLocation']);

    // ============================================
    // PAYMENT METHODS
    // ============================================
    Route::prefix('payment-methods')->group(function () {
        Route::get('/', [StorePaymentMethodController::class, 'index']);
        Route::get('/{id}', [StorePaymentMethodController::class, 'show']);
    });

    // ============================================
    // SHIPPING METHODS
    // ============================================
    Route::prefix('shipping-methods')->group(function () {
        Route::get('/', [StoreShippingMethodController::class, 'index']);
        Route::post('/{id}/calculate-cost', [StoreShippingMethodController::class, 'calculateCost']);
    });

    // ============================================
    // ORDERS (WhatsApp Checkout)
    // ============================================
    Route::prefix('orders')->group(function () {
        // Create order and save to database
        Route::post('/', [StoreOrderController::class, 'createOrder']);
    });
});
