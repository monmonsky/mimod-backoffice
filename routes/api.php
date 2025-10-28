<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardApiController;
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
use App\Http\Controllers\Api\Catalog\ProductAttributeApiController;
use App\Http\Controllers\Api\Catalog\ProductAttributeValueApiController;
use App\Http\Controllers\Api\SettingsApiController;
use App\Http\Controllers\Api\Orders\OrderApiController;
use App\Http\Controllers\Api\Customers\CustomerApiController;
use App\Http\Controllers\Api\Marketing\CouponApiController;
use App\Http\Controllers\Api\Payment\PaymentMethodApiController;
use App\Http\Controllers\Api\Payment\PaymentMethodConfigApiController;
use App\Http\Controllers\Api\Shipping\ShippingMethodApiController;
use App\Http\Controllers\Api\Shipping\ShippingMethodConfigApiController;
use App\Http\Controllers\Api\StoreTokenApiController;
use App\Http\Controllers\Api\AiSeoController;
use App\Http\Controllers\Api\Appearance\Navigation\MenuController as NavigationMenuController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Routes used by the frontend backoffice application.
| All routes except login require authentication.
|
*/

// ============================================
// AUTHENTICATION
// ============================================
Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware('auth.token')->prefix('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
});

// ============================================
// PROTECTED ROUTES
// ============================================
Route::middleware('auth.sanctum')->group(function () {

    // ============================================
    // DASHBOARD
    // ============================================
    Route::prefix('dashboard')->group(function () {
        Route::get('/statistics', [DashboardApiController::class, 'statistics']);
        Route::get('/sales-chart', [DashboardApiController::class, 'salesChart']);
        Route::get('/recent-orders', [DashboardApiController::class, 'recentOrders']);
        Route::get('/top-products', [DashboardApiController::class, 'topProducts']);
    });

    // ============================================
    // CATALOG - PRODUCTS
    // ============================================
    Route::prefix('catalog/products')->group(function () {
        Route::get('/', [ProductApiController::class, 'index']);
        Route::post('/', [ProductApiController::class, 'store']);
        Route::get('/category/{categoryId}', [ProductApiController::class, 'byCategory']);
        Route::get('/brand/{brandId}', [ProductApiController::class, 'byBrand']);
        Route::get('/{identifier}', [ProductApiController::class, 'show']);
        Route::put('/{id}', [ProductApiController::class, 'update']);
        Route::delete('/{id}', [ProductApiController::class, 'destroy']);
        Route::patch('/{id}/status', [ProductApiController::class, 'updateStatus']);
        Route::get('/{id}/variants', [ProductApiController::class, 'variants']);

        // Product Variants
        Route::prefix('variants')->group(function () {
            Route::get('/{id}', [ProductVariantApiController::class, 'show']);
            Route::post('/', [ProductVariantApiController::class, 'store']);
            Route::put('/{id}', [ProductVariantApiController::class, 'update']);
            Route::delete('/{id}', [ProductVariantApiController::class, 'destroy']);
            Route::post('/{id}/generate-sku-barcode', [ProductVariantApiController::class, 'generateSkuAndBarcode']);
        });
    });

    // ============================================
    // CATALOG - CATEGORIES
    // ============================================
    Route::prefix('catalog/categories')->group(function () {
        Route::get('/', [CategoryApiController::class, 'index']);
        Route::post('/', [CategoryApiController::class, 'store']);
        Route::get('/{id}', [CategoryApiController::class, 'show']);
        Route::put('/{id}', [CategoryApiController::class, 'update']);
        Route::delete('/{id}', [CategoryApiController::class, 'destroy']);
    });

    // ============================================
    // CATALOG - BRANDS
    // ============================================
    Route::prefix('catalog/brands')->group(function () {
        Route::get('/', [BrandApiController::class, 'index']);
        Route::post('/', [BrandApiController::class, 'store']);
        Route::get('/{id}', [BrandApiController::class, 'show']);
        Route::put('/{id}', [BrandApiController::class, 'update']);
        Route::delete('/{id}', [BrandApiController::class, 'destroy']);
    });

    // ============================================
    // CATALOG - ATTRIBUTES
    // ============================================
    Route::prefix('catalog')->group(function () {
        // Attributes
        Route::prefix('attributes')->group(function () {
            Route::get('/', [ProductAttributeApiController::class, 'index']);
            Route::post('/', [ProductAttributeApiController::class, 'store']);
            Route::get('/{id}', [ProductAttributeApiController::class, 'show']);
            Route::put('/{id}', [ProductAttributeApiController::class, 'update']);
            Route::delete('/{id}', [ProductAttributeApiController::class, 'destroy']);
        });

        // Attribute Values
        Route::prefix('attribute-values')->group(function () {
            Route::post('/', [ProductAttributeValueApiController::class, 'store']);
            Route::post('/bulk', [ProductAttributeValueApiController::class, 'bulkStore']);
            Route::put('/{id}', [ProductAttributeValueApiController::class, 'update']);
            Route::delete('/{id}', [ProductAttributeValueApiController::class, 'destroy']);
        });

        // Product Variant Attributes
        Route::post('/product-variant-attributes', [ProductVariantApiController::class, 'storeVariantAttribute']);
    });

    // ============================================
    // ORDERS
    // ============================================
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderApiController::class, 'index']);
        Route::post('/', [OrderApiController::class, 'store']);
        Route::get('/customer/{customerId}', [OrderApiController::class, 'byCustomer']);
        Route::get('/{id}', [OrderApiController::class, 'show']);
        Route::get('/{id}/history', [OrderApiController::class, 'getHistory']);
        Route::get('/{id}/invoice', [OrderApiController::class, 'getInvoice']);
        Route::put('/{id}', [OrderApiController::class, 'update']);
        Route::delete('/{id}', [OrderApiController::class, 'destroy']);
        Route::patch('/{id}/status', [OrderApiController::class, 'updateStatus']);
        Route::patch('/{id}/payment', [OrderApiController::class, 'updatePaymentStatus']);
        Route::post('/{id}/send-invoice', [OrderApiController::class, 'sendInvoice']);
    });

    // ============================================
    // CUSTOMERS
    // ============================================
    Route::prefix('customers')->group(function () {
        Route::get('/', [CustomerApiController::class, 'index']);
        Route::post('/', [CustomerApiController::class, 'store']);
        Route::get('/{id}', [CustomerApiController::class, 'show']);
        Route::put('/{id}', [CustomerApiController::class, 'update']);
        Route::delete('/{id}', [CustomerApiController::class, 'destroy']);
    });

    // ============================================
    // MARKETING - COUPONS
    // ============================================
    Route::prefix('marketing/coupons')->group(function () {
        Route::get('/', [CouponApiController::class, 'index']);
        Route::post('/', [CouponApiController::class, 'store']);
        Route::get('/{id}', [CouponApiController::class, 'show']);
        Route::put('/{id}', [CouponApiController::class, 'update']);
        Route::delete('/{id}', [CouponApiController::class, 'destroy']);
        Route::post('/validate', [CouponApiController::class, 'validate']);
    });

    // ============================================
    // PAYMENT METHODS
    // ============================================
     Route::prefix('payment-methods')->group(function () {
        Route::get('/', [PaymentMethodApiController::class, 'index']);
        Route::post('/', [PaymentMethodApiController::class, 'store']);
        Route::get('/{id}', [PaymentMethodApiController::class, 'show']);
        Route::put('/{id}', [PaymentMethodApiController::class, 'update']);
        Route::delete('/{id}', [PaymentMethodApiController::class, 'destroy']);
        Route::post('/{id}/toggle-active', [PaymentMethodApiController::class, 'toggleActive']);
        Route::post('/{id}/config', [PaymentMethodApiController::class, 'updateConfig']);
    });

    // ============================================
    // PAYMENT METHOD CONFIGS (Dedicated Config API)
    // ============================================

    // Global configs management (full CRUD)
    Route::get('/payment-method-configs', [PaymentMethodConfigApiController::class, 'getAllGlobalConfigs']);              // List all
    Route::post('/payment-method-configs', [PaymentMethodConfigApiController::class, 'createGlobalConfig']);              // Create
    Route::get('/payment-method-configs/{id}', [PaymentMethodConfigApiController::class, 'getGlobalConfig']);             // Get single
    Route::put('/payment-method-configs/{id}', [PaymentMethodConfigApiController::class, 'updateGlobalConfig']);          // Update
    Route::delete('/payment-method-configs/{id}', [PaymentMethodConfigApiController::class, 'deleteGlobalConfig']);       // Delete
    Route::delete('/payment-method-configs/{id}/items/{key}', [PaymentMethodConfigApiController::class, 'deleteGlobalConfigItem']); // Delete config item

    Route::prefix('payment-methods')->group(function () {
        // Get provider-level configs
        Route::get('/providers/{provider}/configs', [PaymentMethodConfigApiController::class, 'getProviderConfigs']);

        // Config management for specific method
        Route::get('/{id}/configs', [PaymentMethodConfigApiController::class, 'index']);              // Get all configs
        Route::post('/{id}/configs', [PaymentMethodConfigApiController::class, 'bulkUpdate']);        // Bulk update
        Route::get('/{id}/configs/{key}', [PaymentMethodConfigApiController::class, 'show']);         // Get single config
        Route::put('/{id}/configs/{key}', [PaymentMethodConfigApiController::class, 'update']);       // Update single config
        Route::delete('/{id}/configs/{key}', [PaymentMethodConfigApiController::class, 'destroy']);   // Delete config
    });

    // ============================================
    // SHIPPING METHODS
    // ============================================
    Route::prefix('shipping-methods')->group(function () {
        Route::get('/', [ShippingMethodApiController::class, 'index']);
        Route::post('/', [ShippingMethodApiController::class, 'store']);
        Route::get('/{id}', [ShippingMethodApiController::class, 'show']);
        Route::put('/{id}', [ShippingMethodApiController::class, 'update']);
        Route::delete('/{id}', [ShippingMethodApiController::class, 'destroy']);
        Route::post('/{id}/toggle-active', [ShippingMethodApiController::class, 'toggleActive']);
        Route::post('/{id}/config', [ShippingMethodApiController::class, 'updateConfig']);
        Route::post('/{id}/calculate-cost', [ShippingMethodApiController::class, 'calculateCost']);
    });

    // ============================================
    // SHIPPING METHOD CONFIGS (Dedicated Config API)
    // ============================================

    // Global configs management (full CRUD)
    Route::get('/shipping-method-configs', [ShippingMethodConfigApiController::class, 'getAllGlobalConfigs']);              // List all
    Route::post('/shipping-method-configs', [ShippingMethodConfigApiController::class, 'createGlobalConfig']);              // Create
    Route::get('/shipping-method-configs/{id}', [ShippingMethodConfigApiController::class, 'getGlobalConfig']);             // Get single
    Route::put('/shipping-method-configs/{id}', [ShippingMethodConfigApiController::class, 'updateGlobalConfig']);          // Update
    Route::delete('/shipping-method-configs/{id}', [ShippingMethodConfigApiController::class, 'deleteGlobalConfig']);       // Delete
    Route::delete('/shipping-method-configs/{id}/items/{key}', [ShippingMethodConfigApiController::class, 'deleteGlobalConfigItem']); // Delete config item

    Route::prefix('shipping-methods')->group(function () {
        // Get provider-level configs
        Route::get('/providers/{provider}/configs', [ShippingMethodConfigApiController::class, 'getProviderConfigs']);

        // Config management for specific method
        Route::get('/{id}/configs', [ShippingMethodConfigApiController::class, 'index']);              // Get all configs
        Route::post('/{id}/configs', [ShippingMethodConfigApiController::class, 'bulkUpdate']);        // Bulk update
        Route::get('/{id}/configs/{key}', [ShippingMethodConfigApiController::class, 'show']);         // Get single config
        Route::put('/{id}/configs/{key}', [ShippingMethodConfigApiController::class, 'update']);       // Update single config
        Route::delete('/{id}/configs/{key}', [ShippingMethodConfigApiController::class, 'destroy']);   // Delete config
    });

     // ============================================
    // RAJAONGKIR API
    // ============================================
    Route::prefix('rajaongkir')->group(function () {
        Route::get('/test-connection', [\App\Http\Controllers\Api\Shipping\RajaOngkirApiController::class, 'testConnection']);
        Route::get('/search-destination', [\App\Http\Controllers\Api\Shipping\RajaOngkirApiController::class, 'searchDestination']);
        Route::post('/calculate-cost', [\App\Http\Controllers\Api\Shipping\RajaOngkirApiController::class, 'calculateCost']);
        Route::get('/provinces', [\App\Http\Controllers\Api\Shipping\RajaOngkirApiController::class, 'getProvinces']);
        Route::get('/cities', [\App\Http\Controllers\Api\Shipping\RajaOngkirApiController::class, 'getCities']);
        Route::get('/districts', [\App\Http\Controllers\Api\Shipping\RajaOngkirApiController::class, 'getDistricts']);
        Route::get('/sub-districts', [\App\Http\Controllers\Api\Shipping\RajaOngkirApiController::class, 'getSubDistricts']);
    });


    // ============================================
    // ACCESS CONTROL - USERS
    // ============================================
    Route::prefix('access-control/users')->group(function () {
        Route::get('/', [UserApiController::class, 'index']);
        Route::post('/', [UserApiController::class, 'store']);
        Route::get('/{id}', [UserApiController::class, 'show']);
        Route::put('/{id}', [UserApiController::class, 'update']);
        Route::delete('/{id}', [UserApiController::class, 'destroy']);
    });

    // ============================================
    // ACCESS CONTROL - ROLES
    // ============================================
    Route::prefix('access-control/roles')->group(function () {
        Route::get('/', [RoleApiController::class, 'index']);
        Route::get('/{id}', [RoleApiController::class, 'show']);
        Route::put('/{id}', [RoleApiController::class, 'update']);
        Route::get('/{id}/permissions/grouped', [RolePermissionApiController::class, 'grouped']);
        Route::post('/{id}/permissions/sync', [RolePermissionApiController::class, 'sync']);
    });

    // ============================================
    // ACCESS CONTROL - PERMISSIONS
    // ============================================
    Route::prefix('access-control/permissions')->group(function () {
        Route::get('/', [PermissionApiController::class, 'index']);
        Route::get('/grouped', [PermissionApiController::class, 'grouped']);
        Route::get('/{id}', [PermissionApiController::class, 'show']);
    });

    // ============================================
    // ACCESS CONTROL - MODULES
    // ============================================
    Route::prefix('access-control/modules')->group(function () {
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

    // ============================================
    // ACCESS CONTROL - STORE TOKENS
    // ============================================
    Route::prefix('access-control/store-tokens')->group(function () {
        Route::get('/', [StoreTokenApiController::class, 'index']);
        Route::get('/stats', [StoreTokenApiController::class, 'stats']);
        Route::get('/{id}', [StoreTokenApiController::class, 'show']);
        Route::post('/generate', [StoreTokenApiController::class, 'generate']);
        Route::delete('/{id}', [StoreTokenApiController::class, 'destroy']);
    });

    // ============================================
    // ACCESS CONTROL - USER ACTIVITIES
    // ============================================
    Route::prefix('access-control/user-activities')->group(function () {
        Route::get('/', [UserActivityApiController::class, 'index']);
        Route::get('/{id}', [UserActivityApiController::class, 'show']);
    });

    // ============================================
    // UPLOADS - IMAGES
    // ============================================
    Route::prefix('upload')->group(function () {
        Route::post('/image', [UploadImageApiController::class, 'upload']);
        Route::post('/image/bulk', [UploadImageApiController::class, 'uploadBulk']);
        Route::patch('/product-image/{id}/set-primary', [UploadImageApiController::class, 'setPrimaryImage']);
        Route::delete('/product-image/{id}', [UploadImageApiController::class, 'deleteProductImage']);

        // Media (image + video)
        Route::post('/media', [UploadImageApiController::class, 'uploadMedia']);
        Route::post('/temp', [UploadImageApiController::class, 'uploadTemporary']);
        Route::post('/move', [UploadImageApiController::class, 'moveFromTemp']);
    });

    // ============================================
    // APPEARANCE - NAVIGATION (MENUS)
    // ============================================
    Route::prefix('appearance/navigation/menus')->group(function () {
        Route::get('/', [NavigationMenuController::class, 'index']);
        Route::post('/', [NavigationMenuController::class, 'store']);
        Route::get('/parents', [NavigationMenuController::class, 'getParents']);
        Route::post('/reorder', [NavigationMenuController::class, 'reorder']);
        Route::post('/bulk-create-categories', [NavigationMenuController::class, 'bulkCreateFromCategories']);
        Route::post('/bulk-create-brands', [NavigationMenuController::class, 'bulkCreateFromBrands']);
        Route::get('/{id}', [NavigationMenuController::class, 'show']);
        Route::put('/{id}', [NavigationMenuController::class, 'update']);
        Route::delete('/{id}', [NavigationMenuController::class, 'destroy']);
    });

    // Public menu location endpoint
    Route::get('/menus/location', [NavigationMenuController::class, 'getMenuByLocation']);

    // ============================================
    // SETTINGS
    // ============================================
    Route::get('/settings/{key}', [SettingsApiController::class, 'show']);
    Route::put('/settings/{key}', [SettingsApiController::class, 'update']);

    // ============================================
    // EMAIL
    // ============================================
    Route::post('/email/test-connection', [\App\Http\Controllers\Api\EmailTestApiController::class, 'testConnection']);

    // ============================================
    // AI/SEO
    // ============================================
    Route::post('/ai/generate-seo', [AiSeoController::class, 'generateSeo']);
});