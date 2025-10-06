<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AccessControl\UserApiController;
use App\Http\Controllers\Api\AccessControl\RoleApiController;
use App\Http\Controllers\Api\AccessControl\PermissionApiController;
use App\Http\Controllers\Api\AccessControl\ModuleApiController;
use App\Http\Controllers\Api\Catalog\ProductApiController;
use App\Http\Controllers\Api\Catalog\CategoryApiController;
use App\Http\Controllers\Api\Catalog\BrandApiController;
use App\Http\Controllers\Api\Settings\GeneralSettingsApiController;
use App\Http\Controllers\Api\Settings\PaymentSettingsApiController;
use App\Http\Controllers\Api\Settings\ShippingSettingsApiController;
use App\Http\Controllers\Api\Orders\OrderApiController;
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

// Protected routes (using custom auth.token middleware)
Route::middleware('auth.token')->group(function () {

    // Access Control routes
    Route::prefix('access-control')->group(function () {
        // Users
        Route::prefix('users')->group(function () {
            Route::get('/', [UserApiController::class, 'index'])->middleware('permission:access-control.users.view');
            Route::get('/{id}', [UserApiController::class, 'show'])->middleware('permission:access-control.users.view');
            Route::post('/', [UserApiController::class, 'store'])->middleware('permission:access-control.users.create');
            Route::put('/{id}', [UserApiController::class, 'update'])->middleware('permission:access-control.users.update');
            Route::delete('/{id}', [UserApiController::class, 'destroy'])->middleware('permission:access-control.users.delete');
            Route::post('/{id}/toggle-active', [UserApiController::class, 'toggleActive'])->middleware('permission:access-control.users.update');
        });

        // Roles
        Route::prefix('roles')->group(function () {
            Route::get('/', [RoleApiController::class, 'index'])->middleware('permission:access-control.roles.view');
            Route::get('/{id}', [RoleApiController::class, 'show'])->middleware('permission:access-control.roles.view');
            Route::post('/', [RoleApiController::class, 'store'])->middleware('permission:access-control.roles.create');
            Route::put('/{id}', [RoleApiController::class, 'update'])->middleware('permission:access-control.roles.update');
            Route::delete('/{id}', [RoleApiController::class, 'destroy'])->middleware('permission:access-control.roles.delete');
            Route::post('/{id}/toggle-active', [RoleApiController::class, 'toggleActive'])->middleware('permission:access-control.roles.update');
        });

        // Permissions
        Route::prefix('permissions')->group(function () {
            Route::get('/', [PermissionApiController::class, 'index'])->middleware('permission:access-control.permissions.view');
            Route::get('/grouped', [PermissionApiController::class, 'grouped'])->middleware('permission:access-control.permissions.view');
            Route::get('/{id}', [PermissionApiController::class, 'show'])->middleware('permission:access-control.permissions.view');
        });

        // Modules
        Route::prefix('modules')->group(function () {
            Route::get('/', [ModuleApiController::class, 'index'])->middleware('permission:access-control.modules.view');
            Route::get('/tree', [ModuleApiController::class, 'tree'])->middleware('permission:access-control.modules.view');
            Route::get('/{id}', [ModuleApiController::class, 'show'])->middleware('permission:access-control.modules.view');
            Route::post('/', [ModuleApiController::class, 'store'])->middleware('permission:access-control.modules.create');
            Route::put('/{id}', [ModuleApiController::class, 'update'])->middleware('permission:access-control.modules.update');
            Route::delete('/{id}', [ModuleApiController::class, 'destroy'])->middleware('permission:access-control.modules.delete');
            Route::post('/{id}/toggle-visible', [ModuleApiController::class, 'toggleVisible'])->middleware('permission:access-control.modules.update');
            Route::post('/{id}/toggle-active', [ModuleApiController::class, 'toggleActive'])->middleware('permission:access-control.modules.update');
        });
    });

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

    // Catalog routes
    Route::prefix('catalog')->group(function () {
        // Products
        Route::prefix('products')->group(function () {
            Route::get('/', [ProductApiController::class, 'index']);
            Route::get('/featured', [ProductApiController::class, 'featured']);
            Route::get('/{identifier}', [ProductApiController::class, 'show']);
            Route::get('/{id}/variants', [ProductApiController::class, 'variants']);
            Route::get('/{id}/images', [ProductApiController::class, 'images']);
            Route::get('/category/{categoryId}', [ProductApiController::class, 'byCategory']);
            Route::get('/brand/{brandId}', [ProductApiController::class, 'byBrand']);
        });

        // Categories
        Route::prefix('categories')->group(function () {
            Route::get('/', [CategoryApiController::class, 'index']);
            Route::get('/tree', [CategoryApiController::class, 'tree']);
            Route::get('/parents', [CategoryApiController::class, 'parents']);
            Route::get('/{id}', [CategoryApiController::class, 'show']);
            Route::get('/{parentId}/children', [CategoryApiController::class, 'children']);
        });

        // Brands
        Route::prefix('brands')->group(function () {
            Route::get('/', [BrandApiController::class, 'index']);
            Route::get('/{id}', [BrandApiController::class, 'show']);
        });
    });

    // Orders routes
    Route::prefix('orders')->group(function () {
        Route::get('/pending/count', [OrderApiController::class, 'pendingCount']);
        Route::get('/pending/recent', [OrderApiController::class, 'recentPending']);
    });

    // Customer Segments API routes
    Route::prefix('customer-segments')->group(function () {
        Route::get('/{id}', 'App\Http\Controllers\Customers\CustomerSegmentsController@show')
            ->middleware('permission:customers.customer-segments.view');
        Route::post('/', 'App\Http\Controllers\Customers\CustomerSegmentsController@store')
            ->middleware('permission:customers.customer-segments.create');
        Route::put('/{id}', 'App\Http\Controllers\Customers\CustomerSegmentsController@update')
            ->middleware('permission:customers.customer-segments.update');
        Route::delete('/{id}', 'App\Http\Controllers\Customers\CustomerSegmentsController@destroy')
            ->middleware('permission:customers.customer-segments.delete');
    });
});
