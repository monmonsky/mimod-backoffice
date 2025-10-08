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
use App\Http\Controllers\Api\Customers\CustomerSegmentApiController;
use App\Http\Controllers\Api\Customers\CustomerGroupApiController;
use App\Http\Controllers\Api\Customers\CustomerLoyaltyApiController;
use App\Http\Controllers\Api\Customers\CustomerReviewApiController;
use App\Http\Controllers\Api\Marketing\CouponApiController;
use App\Http\Controllers\Api\Marketing\FlashSaleApiController;
use App\Http\Controllers\Api\Marketing\BundleDealApiController;
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
            Route::patch('/{id}/status', [ProductApiController::class, 'updateStatus']);
            Route::delete('/{id}', [ProductApiController::class, 'destroy']);
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

        // CRUD routes
        Route::get('/', [OrderApiController::class, 'index'])
            ->middleware('permission:orders.all-orders.view');
        Route::get('/{id}', [OrderApiController::class, 'show'])
            ->middleware('permission:orders.all-orders.view');
        Route::put('/{id}', [OrderApiController::class, 'update'])
            ->middleware('permission:orders.all-orders.update');
        Route::put('/{id}/status', [OrderApiController::class, 'updateStatus'])
            ->middleware('permission:orders.all-orders.update');
        Route::delete('/{id}', [OrderApiController::class, 'destroy'])
            ->middleware('permission:orders.all-orders.delete');
    });

    // Customer Segments API routes
    Route::prefix('customer-segments')->group(function () {
        Route::get('/', [CustomerSegmentApiController::class, 'index'])
            ->middleware('permission:customers.customer-segments.view');
        Route::get('/{id}', [CustomerSegmentApiController::class, 'show'])
            ->middleware('permission:customers.customer-segments.view');
        Route::post('/', [CustomerSegmentApiController::class, 'store'])
            ->middleware('permission:customers.customer-segments.create');
        Route::put('/{id}', [CustomerSegmentApiController::class, 'update'])
            ->middleware('permission:customers.customer-segments.update');
        Route::delete('/{id}', [CustomerSegmentApiController::class, 'destroy'])
            ->middleware('permission:customers.customer-segments.delete');
    });

    // Customer Groups API routes
    Route::prefix('customer-groups')->group(function () {
        Route::get('/', [CustomerGroupApiController::class, 'index'])
            ->middleware('permission:customers.customer-groups.view');
        Route::get('/{id}', [CustomerGroupApiController::class, 'show'])
            ->middleware('permission:customers.customer-groups.view');
        Route::post('/', [CustomerGroupApiController::class, 'store'])
            ->middleware('permission:customers.customer-groups.create');
        Route::put('/{id}', [CustomerGroupApiController::class, 'update'])
            ->middleware('permission:customers.customer-groups.update');
        Route::delete('/{id}', [CustomerGroupApiController::class, 'destroy'])
            ->middleware('permission:customers.customer-groups.delete');
        Route::post('/{id}/members', [CustomerGroupApiController::class, 'addMember'])
            ->middleware('permission:customers.customer-groups.update');
        Route::delete('/{groupId}/members/{customerId}', [CustomerGroupApiController::class, 'removeMember'])
            ->middleware('permission:customers.customer-groups.update');
    });

    // Loyalty Programs API routes
    Route::prefix('loyalty-programs')->group(function () {
        Route::get('/', [CustomerLoyaltyApiController::class, 'index'])
            ->middleware('permission:customers.loyalty.view');
        Route::get('/{id}', [CustomerLoyaltyApiController::class, 'showProgram'])
            ->middleware('permission:customers.loyalty.view');
        Route::post('/', [CustomerLoyaltyApiController::class, 'storeProgram'])
            ->middleware('permission:customers.loyalty.create');
        Route::put('/{id}', [CustomerLoyaltyApiController::class, 'updateProgram'])
            ->middleware('permission:customers.loyalty.update');
        Route::delete('/{id}', [CustomerLoyaltyApiController::class, 'destroyProgram'])
            ->middleware('permission:customers.loyalty.delete');
    });

    // Loyalty Transactions API routes
    Route::prefix('loyalty-transactions')->group(function () {
        Route::post('/', [CustomerLoyaltyApiController::class, 'storeTransaction'])
            ->middleware('permission:customers.loyalty.create');
        Route::get('/customer/{customerId}/balance', [CustomerLoyaltyApiController::class, 'getCustomerBalance'])
            ->middleware('permission:customers.loyalty.view');
    });

    // Product Reviews API routes
    Route::prefix('product-reviews')->group(function () {
        Route::get('/', [CustomerReviewApiController::class, 'index'])
            ->middleware('permission:customers.reviews.view');
        Route::get('/{id}', [CustomerReviewApiController::class, 'show'])
            ->middleware('permission:customers.reviews.view');
        Route::post('/{id}/approve', [CustomerReviewApiController::class, 'approve'])
            ->middleware('permission:customers.reviews.approve');
        Route::post('/{id}/respond', [CustomerReviewApiController::class, 'respond'])
            ->middleware('permission:customers.reviews.update');
        Route::delete('/{id}', [CustomerReviewApiController::class, 'destroy'])
            ->middleware('permission:customers.reviews.delete');
    });

    // Marketing routes
    Route::prefix('marketing')->group(function () {
        // Coupons API routes
        Route::prefix('coupons')->group(function () {
            Route::get('/', [CouponApiController::class, 'index'])
                ->middleware('permission:marketing.coupons.view');
            Route::get('/{id}', [CouponApiController::class, 'show'])
                ->middleware('permission:marketing.coupons.view');
            Route::post('/', [CouponApiController::class, 'store'])
                ->middleware('permission:marketing.coupons.create');
            Route::put('/{id}', [CouponApiController::class, 'update'])
                ->middleware('permission:marketing.coupons.update');
            Route::delete('/{id}', [CouponApiController::class, 'destroy'])
                ->middleware('permission:marketing.coupons.delete');
            Route::post('/validate', [CouponApiController::class, 'validate'])
                ->middleware('permission:marketing.coupons.view');
        });

        // Flash Sales API routes
        Route::prefix('flash-sales')->group(function () {
            Route::get('/', [FlashSaleApiController::class, 'index'])
                ->middleware('permission:marketing.flash-sales.view');
            Route::get('/{id}', [FlashSaleApiController::class, 'show'])
                ->middleware('permission:marketing.flash-sales.view');
            Route::post('/', [FlashSaleApiController::class, 'store'])
                ->middleware('permission:marketing.flash-sales.create');
            Route::put('/{id}', [FlashSaleApiController::class, 'update'])
                ->middleware('permission:marketing.flash-sales.update');
            Route::delete('/{id}', [FlashSaleApiController::class, 'destroy'])
                ->middleware('permission:marketing.flash-sales.delete');
            Route::post('/{id}/products', [FlashSaleApiController::class, 'addProduct'])
                ->middleware('permission:marketing.flash-sales.update');
            Route::delete('/{id}/products', [FlashSaleApiController::class, 'removeProduct'])
                ->middleware('permission:marketing.flash-sales.update');
        });

        // Bundle Deals API routes
        Route::prefix('bundle-deals')->group(function () {
            Route::get('/', [BundleDealApiController::class, 'index'])
                ->middleware('permission:marketing.bundle-deals.view');
            Route::get('/{id}', [BundleDealApiController::class, 'show'])
                ->middleware('permission:marketing.bundle-deals.view');
            Route::post('/', [BundleDealApiController::class, 'store'])
                ->middleware('permission:marketing.bundle-deals.create');
            Route::put('/{id}', [BundleDealApiController::class, 'update'])
                ->middleware('permission:marketing.bundle-deals.update');
            Route::delete('/{id}', [BundleDealApiController::class, 'destroy'])
                ->middleware('permission:marketing.bundle-deals.delete');
            Route::post('/{id}/items', [BundleDealApiController::class, 'addItem'])
                ->middleware('permission:marketing.bundle-deals.update');
            Route::delete('/{id}/items', [BundleDealApiController::class, 'removeItem'])
                ->middleware('permission:marketing.bundle-deals.update');
        });
    });
});
