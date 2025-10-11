<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UploadImageApiController;
use App\Http\Controllers\Api\AccessControl\UserApiController;
use App\Http\Controllers\Api\AccessControl\RoleApiController;
use App\Http\Controllers\Api\AccessControl\RolePermissionApiController;
use App\Http\Controllers\Api\AccessControl\PermissionApiController;
use App\Http\Controllers\Api\AccessControl\ModuleApiController;
use App\Http\Controllers\Api\AccessControl\UserActivityApiController;
use App\Http\Controllers\Api\Catalog\ProductApiController;
use App\Http\Controllers\Api\Catalog\ProductVariantApiController;
use App\Http\Controllers\Api\Catalog\CategoryApiController;
use App\Http\Controllers\Api\Catalog\BrandApiController;
use App\Http\Controllers\Api\SettingsApiController;
use App\Http\Controllers\Api\Orders\OrderApiController;
use App\Http\Controllers\Api\Customers\CustomerApiController;
use App\Http\Controllers\Api\Customers\CustomerSegmentApiController;
use App\Http\Controllers\Api\Customers\CustomerGroupApiController;
use App\Http\Controllers\Api\Customers\CustomerLoyaltyApiController;
use App\Http\Controllers\Api\Customers\CustomerReviewApiController;
use App\Http\Controllers\Api\Marketing\CouponApiController;
use App\Http\Controllers\Api\Marketing\FlashSaleApiController;
use App\Http\Controllers\Api\Marketing\BundleDealApiController;
use App\Http\Controllers\Api\StoreTokenApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Public routes
Route::post('auth/login', [AuthController::class, 'login']);

// Store API routes (read-only access for frontend)
Route::middleware('store.api')->prefix('store')->group(function () {
    // Products
    Route::get('/products', [ProductApiController::class, 'index'])->middleware('store.api:products:read');
    Route::get('/products/{id}', [ProductApiController::class, 'show'])->middleware('store.api:products:read');

    // Categories
    Route::get('/categories', [CategoryApiController::class, 'index'])->middleware('store.api:categories:read');
    Route::get('/categories/tree', [CategoryApiController::class, 'tree'])->middleware('store.api:categories:read');
    Route::get('/categories/parents', [CategoryApiController::class, 'parents'])->middleware('store.api:categories:read');
    Route::get('/categories/{id}', [CategoryApiController::class, 'show'])->middleware('store.api:categories:read');
    Route::get('/categories/{parentId}/children', [CategoryApiController::class, 'children'])->middleware('store.api:categories:read');

    // Brands
    Route::get('/brands', [BrandApiController::class, 'index'])->middleware('store.api:brands:read');
    Route::get('/brands/{id}', [BrandApiController::class, 'show'])->middleware('store.api:brands:read');

    // Settings
    Route::get('/settings', [SettingsApiController::class, 'index'])->middleware('store.api:settings:read');
    Route::get('/settings/{key}', [SettingsApiController::class, 'show'])->middleware('store.api:settings:read');
});

// Auth routes (using custom auth.token middleware)
Route::middleware('auth.token')->prefix('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('logout-all', [AuthController::class, 'logoutAll']);
    Route::get('me', [AuthController::class, 'me']);
    Route::get('sessions', [AuthController::class, 'sessions']);
});

// Protected routes (using custom auth.token middleware)
Route::middleware('auth:sanctum')->group(function () {

    // Upload Image routes
    Route::prefix('upload')->group(function () {
        Route::post('/image', [UploadImageApiController::class, 'upload']);
        Route::post('/images', [UploadImageApiController::class, 'uploadMultiple']);
        Route::post('/bulk', [UploadImageApiController::class, 'uploadBulk']);
        Route::post('/temp', [UploadImageApiController::class, 'uploadTemporary']);
        Route::post('/move', [UploadImageApiController::class, 'moveFromTemp']);
        Route::delete('/image', [UploadImageApiController::class, 'delete']);
        Route::delete('/product-image/{id}', [UploadImageApiController::class, 'deleteProductImage']);
        Route::patch('/product-image/{id}/set-primary', [UploadImageApiController::class, 'setPrimaryImage']);
    });

    // Access Control routes
    Route::prefix('access-control')->group(function () {
        // Users
        Route::prefix('users')->group(function () {
            Route::get('/', [UserApiController::class, 'index']);
            Route::get('/{id}', [UserApiController::class, 'show']);
            Route::post('/', [UserApiController::class, 'store']);
            Route::put('/{id}', [UserApiController::class, 'update']);
            Route::delete('/{id}', [UserApiController::class, 'destroy']);
        });

        // Roles
        Route::prefix('roles')->group(function () {
            Route::get('/', [RoleApiController::class, 'index']);
            Route::get('/{id}', [RoleApiController::class, 'show']);
            Route::post('/', [RoleApiController::class, 'store']);
            Route::put('/{id}', [RoleApiController::class, 'update']);
            Route::delete('/{id}', [RoleApiController::class, 'destroy']);
            Route::post('/{id}/toggle-active', [RoleApiController::class, 'toggleActive']);

            // Role Permissions
            Route::get('/{roleId}/permissions', [RolePermissionApiController::class, 'index']);
            Route::get('/{roleId}/permissions/grouped', [RolePermissionApiController::class, 'grouped']);
            Route::post('/{roleId}/permissions/sync', [RolePermissionApiController::class, 'sync']);
            Route::post('/{roleId}/permissions/attach', [RolePermissionApiController::class, 'attach']);
            Route::delete('/{roleId}/permissions/{permissionId}', [RolePermissionApiController::class, 'detach']);
        });

        // Permissions
        Route::prefix('permissions')->group(function () {
            Route::get('/', [PermissionApiController::class, 'index']);
            Route::get('/grouped', [PermissionApiController::class, 'grouped']);
            Route::get('/{id}', [PermissionApiController::class, 'show']);
        });

        // Modules
        Route::prefix('modules')->group(function () {
            Route::get('/', [ModuleApiController::class, 'index']);
            Route::get('/tree', [ModuleApiController::class, 'tree']);
            Route::get('/grouped', [ModuleApiController::class, 'grouped']);
            Route::get('/group/{groupName}', [ModuleApiController::class, 'byGroup']);
            Route::post('/reorder', [ModuleApiController::class, 'reorder']);
            Route::get('/{id}', [ModuleApiController::class, 'show']);
            Route::post('/', [ModuleApiController::class, 'store']);
            Route::put('/{id}', [ModuleApiController::class, 'update']);
            Route::delete('/{id}', [ModuleApiController::class, 'destroy']);
            Route::post('/{id}/toggle-visible', [ModuleApiController::class, 'toggleVisible']);
            Route::post('/{id}/toggle-active', [ModuleApiController::class, 'toggleActive']);
        });

        // User Activities
        Route::prefix('user-activities')->group(function () {
            Route::get('/', [UserActivityApiController::class, 'index']);
            Route::get('/statistics', [UserActivityApiController::class, 'statistics']);
            Route::get('/my-activities', [UserActivityApiController::class, 'myActivities']);
            Route::get('/export', [UserActivityApiController::class, 'export']);
            Route::delete('/clear', [UserActivityApiController::class, 'clear']);
            Route::get('/{id}', [UserActivityApiController::class, 'show']);
        });

        // Store Token Management
        Route::prefix('store-tokens')->group(function () {
            Route::get('/', [StoreTokenApiController::class, 'index']);
            Route::get('/stats', [StoreTokenApiController::class, 'stats']);
            Route::post('/generate', [StoreTokenApiController::class, 'generate']);
            Route::get('/{id}', [StoreTokenApiController::class, 'show']);
            Route::delete('/{id}', [StoreTokenApiController::class, 'destroy']);
        });
    });

    // Settings API
    Route::get('/settings', [SettingsApiController::class, 'index']);
    Route::get('/settings/{prefix}', [SettingsApiController::class, 'show']);
    Route::put('/settings/{key}', [SettingsApiController::class, 'update']);
    Route::post('/settings/bulk-update', [SettingsApiController::class, 'updateBulk']);

    // Email Test API
    Route::post('/email/test-connection', [\App\Http\Controllers\Api\EmailTestApiController::class, 'testConnection']);
    Route::get('/email/config', [\App\Http\Controllers\Api\EmailTestApiController::class, 'getConfig']);

    // Catalog routes
    Route::prefix('catalog')->group(function () {
        // Products
        Route::prefix('products')->group(function () {
            Route::get('/', [ProductApiController::class, 'index']);
            Route::post('/', [ProductApiController::class, 'store']);
            Route::get('/featured', [ProductApiController::class, 'featured']);
            Route::get('/{identifier}', [ProductApiController::class, 'show']);
            Route::put('/{id}', [ProductApiController::class, 'update']);
            Route::get('/{id}/variants', [ProductApiController::class, 'variants']);
            Route::get('/{id}/images', [ProductApiController::class, 'images']);
            Route::post('/{id}/images', [ProductApiController::class, 'uploadImages']);
            Route::delete('/{id}/images/{imageId}', [ProductApiController::class, 'deleteImage']);
            Route::post('/{id}/images/{imageId}/primary', [ProductApiController::class, 'setPrimaryImage']);
            Route::get('/category/{categoryId}', [ProductApiController::class, 'byCategory']);
            Route::get('/brand/{brandId}', [ProductApiController::class, 'byBrand']);
            Route::patch('/{id}/status', [ProductApiController::class, 'updateStatus']);
            Route::post('/{id}/set-active', [ProductApiController::class, 'setActive']);
            Route::post('/{id}/set-inactive', [ProductApiController::class, 'setInactive']);
            Route::post('/{id}/categories', [ProductApiController::class, 'updateCategories']);
            Route::delete('/{id}', [ProductApiController::class, 'destroy']);

            // Product Variants
            Route::prefix('variants')->group(function () {
                Route::get('/{id}', [ProductVariantApiController::class, 'show']);
                Route::post('/', [ProductVariantApiController::class, 'store']);
                Route::put('/{id}', [ProductVariantApiController::class, 'update']);
                Route::delete('/{id}', [ProductVariantApiController::class, 'destroy']);
            });
        });

        // Categories
        Route::prefix('categories')->group(function () {
            Route::get('/', [CategoryApiController::class, 'index']);
            Route::post('/', [CategoryApiController::class, 'store']);
            Route::get('/tree', [CategoryApiController::class, 'tree']);
            Route::get('/parents', [CategoryApiController::class, 'parents']);
            Route::get('/{id}', [CategoryApiController::class, 'show']);
            Route::put('/{id}', [CategoryApiController::class, 'update']);
            Route::get('/{parentId}/children', [CategoryApiController::class, 'children']);
            Route::patch('/{id}/status', [CategoryApiController::class, 'updateStatus']);
            Route::delete('/{id}', [CategoryApiController::class, 'destroy']);
        });

        // Brands
        Route::prefix('brands')->group(function () {
            Route::get('/', [BrandApiController::class, 'index']);
            Route::post('/', [BrandApiController::class, 'store']);
            Route::get('/{id}', [BrandApiController::class, 'show']);
            Route::put('/{id}', [BrandApiController::class, 'update']);
            Route::patch('/{id}/status', [BrandApiController::class, 'updateStatus']);
            Route::delete('/{id}', [BrandApiController::class, 'destroy']);
        });
    });

    // Orders routes
    Route::prefix('orders')->group(function () {
        Route::get('/pending/count', [OrderApiController::class, 'pendingCount']);
        Route::get('/pending/recent', [OrderApiController::class, 'recentPending']);

        // CRUD routes
        Route::get('/', [OrderApiController::class, 'index']);
        Route::get('/customer/{customerId}', [OrderApiController::class, 'byCustomer']);
        Route::get('/{id}', [OrderApiController::class, 'show']);
        Route::put('/{id}', [OrderApiController::class, 'update']);
        Route::put('/{id}/status', [OrderApiController::class, 'updateStatus']);
        Route::delete('/{id}', [OrderApiController::class, 'destroy']);
    });

    // Customers API routes
    Route::prefix('customers')->group(function () {
        Route::get('/', [CustomerApiController::class, 'index']);
        Route::post('/', [CustomerApiController::class, 'store']);
        Route::get('/{id}', [CustomerApiController::class, 'show']);
        Route::put('/{id}', [CustomerApiController::class, 'update']);
        Route::patch('/{id}/status', [CustomerApiController::class, 'updateStatus']);
        Route::delete('/{id}', [CustomerApiController::class, 'destroy']);
        Route::get('/{id}/orders', [CustomerApiController::class, 'orders']);
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
