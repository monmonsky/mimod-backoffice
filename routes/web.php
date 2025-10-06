<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Catalog\AddProductsController;
use App\Http\Controllers\Catalog\AllProductsController;
use App\Http\Controllers\Catalog\BrandsController;
use App\Http\Controllers\Catalog\CategoriesController;
use App\Http\Controllers\Catalog\VariantsController;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {

    Route::get('/', function () {
        return redirect('/login');
    });

    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
    });
});

Route::middleware('auth.token')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard')->middleware('permission:dashboard.view');

    Route::controller(LoginController::class)->group(function () {
        // Logout routes
        Route::post('/logout', 'logout')->name('logout');
    });


    // Users
    Route::group(['prefix' => 'user'], function () {
        Route::get('/', 'App\Http\Controllers\AccessControl\UserController@index')
            ->name('user.index')
            ->middleware('permission:access-control.users.view');

        Route::get('/create', 'App\Http\Controllers\AccessControl\UserController@create')
            ->name('user.create')
            ->middleware('permission:access-control.users.create');

        Route::post('/store', 'App\Http\Controllers\AccessControl\UserController@store')
            ->name('user.store')
            ->middleware('permission:access-control.users.create');

        Route::get('/{id}/edit', 'App\Http\Controllers\AccessControl\UserController@edit')
            ->name('user.edit')
            ->middleware('permission:access-control.users.update');

        Route::put('/{id}', 'App\Http\Controllers\AccessControl\UserController@update')
            ->name('user.update')
            ->middleware('permission:access-control.users.update');

        Route::delete('/{id}', 'App\Http\Controllers\AccessControl\UserController@destroy')
            ->name('user.destroy')
            ->middleware('permission:access-control.users.delete');

        Route::post('/{id}/toggle-active', 'App\Http\Controllers\AccessControl\UserController@toggleActive')
            ->name('user.toggle-active')
            ->middleware('permission:access-control.users.update');
    });

    // Access Control
    Route::group(['prefix' => 'access-control'], function () {
        // Modules
        Route::group(['prefix' => 'modules'], function () {
            Route::get('/', 'App\Http\Controllers\AccessControl\ModuleController@index')->name('modules.index')->middleware('permission:access-control.modules.view');
            Route::get('/create', 'App\Http\Controllers\AccessControl\ModuleController@create')->name('modules.create')->middleware('permission:access-control.modules.create');
            Route::get('/{id}/edit', 'App\Http\Controllers\AccessControl\ModuleController@edit')->name('modules.edit')->middleware('permission:access-control.modules.update');
            Route::get('/all', 'App\Http\Controllers\AccessControl\ModuleController@getAll')->name('modules.all')->middleware('permission:access-control.modules.view');
            Route::post('/store', 'App\Http\Controllers\AccessControl\ModuleController@store')->name('modules.store')->middleware('permission:access-control.modules.create');
            Route::put('/{id}', 'App\Http\Controllers\AccessControl\ModuleController@update')->name('modules.update')->middleware('permission:access-control.modules.update');
            Route::delete('/{id}', 'App\Http\Controllers\AccessControl\ModuleController@destroy')->name('modules.destroy')->middleware('permission:access-control.modules.delete');
            Route::post('/{id}/toggle-active', 'App\Http\Controllers\AccessControl\ModuleController@toggleActive')->name('modules.toggle-active')->middleware('permission:access-control.modules.update');
            Route::post('/{id}/toggle-visible', 'App\Http\Controllers\AccessControl\ModuleController@toggleVisible')->name('modules.toggle-visible')->middleware('permission:access-control.modules.update');
            Route::post('/update-order', 'App\Http\Controllers\AccessControl\ModuleController@updateOrder')->name('modules.update-order')->middleware('permission:access-control.modules.update');
            Route::post('/update-group-order', 'App\Http\Controllers\AccessControl\ModuleController@updateGroupOrder')->name('modules.update-group-order')->middleware('permission:access-control.modules.update');
        });

        // Roles
        Route::group(['prefix' => 'role'], function () {
            Route::get('/', 'App\Http\Controllers\AccessControl\RoleController@index')->name('role.index')->middleware('permission:access-control.roles.view');
            Route::get('/create', 'App\Http\Controllers\AccessControl\RoleController@create')->name('role.create')->middleware('permission:access-control.roles.create');
            Route::post('/store', 'App\Http\Controllers\AccessControl\RoleController@store')->name('role.store')->middleware('permission:access-control.roles.create');
            Route::get('/{id}/edit', 'App\Http\Controllers\AccessControl\RoleController@edit')->name('role.edit')->middleware('permission:access-control.roles.update');
            Route::get('/{id}/detail', 'App\Http\Controllers\AccessControl\RoleController@detail')->name('role.detail')->middleware('permission:access-control.roles.view');
            Route::put('/{id}', 'App\Http\Controllers\AccessControl\RoleController@update')->name('role.update')->middleware('permission:access-control.roles.update');
            Route::delete('/{id}', 'App\Http\Controllers\AccessControl\RoleController@destroy')->name('role.destroy')->middleware('permission:access-control.roles.delete');
            Route::post('/{id}/toggle-active', 'App\Http\Controllers\AccessControl\RoleController@toggleActive')->name('role.toggle-active')->middleware('permission:access-control.roles.update');
        });

        // Permissions
        Route::group(['prefix' => 'permission'], function () {
            Route::get('/', 'App\Http\Controllers\AccessControl\PermissionController@index')->name('permission.index')->middleware('permission:access-control.permissions.view');
            Route::get('/create', 'App\Http\Controllers\AccessControl\PermissionController@create')->name('permission.create')->middleware('permission:access-control.permissions.create');
            Route::post('/store', 'App\Http\Controllers\AccessControl\PermissionController@store')->name('permission.store')->middleware('permission:access-control.permissions.create');
            Route::get('/{id}/edit', 'App\Http\Controllers\AccessControl\PermissionController@edit')->name('permission.edit')->middleware('permission:access-control.permissions.update');
            Route::put('/{id}', 'App\Http\Controllers\AccessControl\PermissionController@update')->name('permission.update')->middleware('permission:access-control.permissions.update');
            Route::delete('/{id}', 'App\Http\Controllers\AccessControl\PermissionController@destroy')->name('permission.destroy')->middleware('permission:access-control.permissions.delete');
        });

        // Permission Groups
        Route::group(['prefix' => 'permission-group'], function () {
            Route::get('/', 'App\Http\Controllers\AccessControl\PermissionGroupController@index')->name('permission-group.index')->middleware('permission:access-control.permissions.view');
            Route::get('/create', 'App\Http\Controllers\AccessControl\PermissionGroupController@create')->name('permission-group.create')->middleware('permission:access-control.permissions.create');
            Route::post('/store', 'App\Http\Controllers\AccessControl\PermissionGroupController@store')->name('permission-group.store')->middleware('permission:access-control.permissions.create');
            Route::get('/{id}/edit', 'App\Http\Controllers\AccessControl\PermissionGroupController@edit')->name('permission-group.edit')->middleware('permission:access-control.permissions.update');
            Route::put('/{id}', 'App\Http\Controllers\AccessControl\PermissionGroupController@update')->name('permission-group.update')->middleware('permission:access-control.permissions.update');
            Route::delete('/{id}', 'App\Http\Controllers\AccessControl\PermissionGroupController@destroy')->name('permission-group.destroy')->middleware('permission:access-control.permissions.delete');
        });

        // User Activities
        Route::group(['prefix' => 'user-activities'], function () {
            Route::get('/', 'App\Http\Controllers\AccessControl\UserActivityController@index')->name('access-control.user-activities.index')->middleware('permission:access-control.user-activities.view');
            Route::get('/{id}', 'App\Http\Controllers\AccessControl\UserActivityController@show')->name('access-control.user-activities.show')->middleware('permission:access-control.user-activities.view');
            Route::delete('/clear', 'App\Http\Controllers\AccessControl\UserActivityController@clear')->name('access-control.user-activities.clear')->middleware('permission:access-control.user-activities.clear');
            Route::get('/export/csv', 'App\Http\Controllers\AccessControl\UserActivityController@export')->name('access-control.user-activities.export')->middleware('permission:access-control.user-activities.export');
        });

        // Session (optional, might not have permission yet)
        Route::group(['prefix' => 'session'], function () {
            Route::get('/', function () {
                return view('pages.session.index');
            })->name('session.index');
        });
    });


    // Reports
    Route::group(['prefix' => 'reports'], function () {
        // Sales Report
        Route::get('/sales', 'App\Http\Controllers\Reports\SalesReportController@index')
            ->name('reports.sales')
            ->middleware('permission:reports.sales.view');
        Route::post('/sales/export', 'App\Http\Controllers\Reports\SalesReportController@export')
            ->name('reports.sales.export')
            ->middleware('permission:reports.sales.export');

        // Revenue Report
        Route::get('/revenue', 'App\Http\Controllers\Reports\RevenueReportController@index')
            ->name('reports.revenue')
            ->middleware('permission:reports.revenue.view');
        Route::post('/revenue/export', 'App\Http\Controllers\Reports\RevenueReportController@export')
            ->name('reports.revenue.export')
            ->middleware('permission:reports.revenue.export');

        // Product Performance
        Route::get('/product-performance', 'App\Http\Controllers\Reports\ProductPerformanceController@index')
            ->name('reports.product-performance')
            ->middleware('permission:reports.product-performance.view');
        Route::post('/product-performance/export', 'App\Http\Controllers\Reports\ProductPerformanceController@export')
            ->name('reports.product-performance.export')
            ->middleware('permission:reports.product-performance.export');

        // Inventory Report
        Route::get('/inventory', 'App\Http\Controllers\Reports\InventoryReportController@index')
            ->name('reports.inventory')
            ->middleware('permission:reports.inventory.view');
        Route::post('/inventory/export', 'App\Http\Controllers\Reports\InventoryReportController@export')
            ->name('reports.inventory.export')
            ->middleware('permission:reports.inventory.export');
    });

    // Orders Management
    Route::group(['prefix' => 'orders'], function () {
        // All Orders
        Route::get('/all-orders', 'App\Http\Controllers\Orders\AllOrdersController@index')
            ->name('orders.all-orders.index')
            ->middleware('permission:orders.all-orders.view');
        Route::get('/all-orders/{id}', 'App\Http\Controllers\Orders\AllOrdersController@show')
            ->name('orders.all-orders.show')
            ->middleware('permission:orders.all-orders.view');
        Route::put('/all-orders/{id}', 'App\Http\Controllers\Orders\AllOrdersController@update')
            ->name('orders.all-orders.update')
            ->middleware('permission:orders.all-orders.update');
        Route::delete('/all-orders/{id}', 'App\Http\Controllers\Orders\AllOrdersController@destroy')
            ->name('orders.all-orders.destroy')
            ->middleware('permission:orders.all-orders.delete');
        Route::post('/all-orders/{id}/status', 'App\Http\Controllers\Orders\AllOrdersController@updateStatus')
            ->name('orders.all-orders.update-status')
            ->middleware('permission:orders.all-orders.update');
        Route::post('/all-orders/export', 'App\Http\Controllers\Orders\AllOrdersController@export')
            ->name('orders.all-orders.export')
            ->middleware('permission:orders.all-orders.export');

        // Pending Orders
        Route::get('/pending-orders', 'App\Http\Controllers\Orders\PendingOrdersController@index')
            ->name('orders.pending-orders.index')
            ->middleware('permission:orders.pending-orders.view');
        Route::post('/pending-orders/{id}/confirm', 'App\Http\Controllers\Orders\PendingOrdersController@confirm')
            ->name('orders.pending-orders.confirm')
            ->middleware('permission:orders.pending-orders.confirm');
        Route::post('/pending-orders/{id}/cancel', 'App\Http\Controllers\Orders\PendingOrdersController@cancel')
            ->name('orders.pending-orders.cancel')
            ->middleware('permission:orders.pending-orders.cancel');

        // Processing Orders
        Route::get('/processing-orders', 'App\Http\Controllers\Orders\ProcessingOrdersController@index')
            ->name('orders.processing-orders.index')
            ->middleware('permission:orders.processing-orders.view');
        Route::post('/processing-orders/{id}/ship', 'App\Http\Controllers\Orders\ProcessingOrdersController@ship')
            ->name('orders.processing-orders.ship')
            ->middleware('permission:orders.processing-orders.ship');

        // Shipped Orders
        Route::get('/shipped-orders', 'App\Http\Controllers\Orders\ShippedOrdersController@index')
            ->name('orders.shipped-orders.index')
            ->middleware('permission:orders.shipped-orders.view');
        Route::post('/shipped-orders/{id}/complete', 'App\Http\Controllers\Orders\ShippedOrdersController@complete')
            ->name('orders.shipped-orders.complete')
            ->middleware('permission:orders.shipped-orders.complete');

        // Completed Orders
        Route::get('/completed-orders', 'App\Http\Controllers\Orders\CompletedOrdersController@index')
            ->name('orders.completed-orders.index')
            ->middleware('permission:orders.completed-orders.view');

        // Cancelled Orders
        Route::get('/cancelled-orders', 'App\Http\Controllers\Orders\CancelledOrdersController@index')
            ->name('orders.cancelled-orders.index')
            ->middleware('permission:orders.cancelled-orders.view');
    });

    Route::group(['prefix' => 'promotions'], function () {
        Route::get('/coupons', function () {
            return view('pages.promotions.coupons');
        })->name('promotions.coupons');

        Route::get('/coupon-usage', function () {
            return view('pages.promotions.coupon-usage');
        })->name('promotions.coupon-usage');

        Route::get('/campaigns', function () {
            return view('pages.promotions.campaigns');
        })->name('promotions.campaigns');

        Route::get('/email-campaigns', function () {
            return view('pages.promotions.email-campaigns');
        })->name('promotions.email-campaigns');

        Route::get('/email-campaigns/create', function () {
            return view('pages.promotions.email-campaigns-create');
        })->name('promotions.email-campaigns.create');

        Route::get('/email-campaigns/{id}', function () {
            return view('pages.promotions.email-campaigns-view');
        })->name('promotions.email-campaigns.view');

        Route::get('/email-templates', function () {
            return view('pages.promotions.email-templates');
        })->name('promotions.email-templates');

        Route::get('/email-templates/create', function () {
            return view('pages.promotions.email-templates-create');
        })->name('promotions.email-templates.create');

        Route::get('/email-templates/{id}/edit', function () {
            return view('pages.promotions.email-templates-edit');
        })->name('promotions.email-templates.edit');
    });

    // Settings
    Route::group(['prefix' => 'settings'], function () {
        // General Settings Routes
        Route::prefix('generals')->group(function () {
            Route::get('/store', 'App\Http\Controllers\Settings\GeneralController@storeInfo')->name('settings.generals.store')->middleware('permission:settings.generals.store.view');
            Route::post('/store', 'App\Http\Controllers\Settings\GeneralController@updateStoreInfo')->name('settings.generals.store.update')->middleware('permission:settings.generals.store.update');
            Route::post('/store/upload-logo', 'App\Http\Controllers\Settings\GeneralController@uploadStoreLogo')->name('settings.generals.store.upload-logo')->middleware('permission:settings.generals.store.update');
            Route::delete('/store/delete-logo', 'App\Http\Controllers\Settings\GeneralController@deleteStoreLogo')->name('settings.generals.store.delete-logo')->middleware('permission:settings.generals.store.update');

            Route::get('/email', 'App\Http\Controllers\Settings\GeneralController@emailSettings')->name('settings.generals.email')->middleware('permission:settings.generals.email.view');
            Route::post('/email', 'App\Http\Controllers\Settings\GeneralController@updateEmailSettings')->name('settings.generals.email.update')->middleware('permission:settings.generals.email.update');
            Route::post('/email/test', 'App\Http\Controllers\Settings\GeneralController@testEmailConnection')->name('settings.generals.email.test')->middleware('permission:settings.generals.email.update');

            Route::get('/seo', 'App\Http\Controllers\Settings\GeneralController@seoMeta')->name('settings.generals.seo')->middleware('permission:settings.generals.seo.view');
            Route::post('/seo', 'App\Http\Controllers\Settings\GeneralController@updateSeoMeta')->name('settings.generals.seo.update')->middleware('permission:settings.generals.seo.update');

            Route::get('/system', 'App\Http\Controllers\Settings\GeneralController@systemConfig')->name('settings.generals.system')->middleware('permission:settings.generals.system.view');
            Route::post('/system', 'App\Http\Controllers\Settings\GeneralController@updateSystemConfig')->name('settings.generals.system.update')->middleware('permission:settings.generals.system.update');

            Route::get('/api-tokens', 'App\Http\Controllers\Settings\ApiTokenController@index')->name('settings.generals.api-tokens')->middleware('permission:settings.generals.api-tokens.view');
            Route::post('/api-tokens/generate', 'App\Http\Controllers\Settings\ApiTokenController@generate')->name('settings.generals.api-tokens.generate')->middleware('permission:settings.generals.api-tokens.generate');
            Route::delete('/api-tokens/{tokenId}', 'App\Http\Controllers\Settings\ApiTokenController@revoke')->name('settings.generals.api-tokens.revoke')->middleware('permission:settings.generals.api-tokens.revoke');
            Route::delete('/api-tokens', 'App\Http\Controllers\Settings\ApiTokenController@revokeAll')->name('settings.generals.api-tokens.revoke-all')->middleware('permission:settings.generals.api-tokens.revoke');
            Route::get('/api-tokens/{tokenId}/show', 'App\Http\Controllers\Settings\ApiTokenController@show')->name('settings.generals.api-tokens.show')->middleware('permission:settings.generals.api-tokens.view');
        });

        // Payment Settings Routes
        Route::prefix('payments')->group(function () {
            Route::get('/methods', 'App\Http\Controllers\Settings\PaymentController@paymentMethods')->name('settings.payments.methods')->middleware('permission:settings.payments.methods.view');
            Route::post('/methods/{method}/toggle', 'App\Http\Controllers\Settings\PaymentController@togglePaymentMethod')->name('settings.payments.methods.toggle')->middleware('permission:settings.payments.methods.update');

            // Bank Account Routes
            Route::post('/methods/banks/store', 'App\Http\Controllers\Settings\PaymentController@storeBankAccount')->name('settings.payments.methods.banks.store')->middleware('permission:settings.payments.methods.update');
            Route::get('/methods/banks/{bankId}', 'App\Http\Controllers\Settings\PaymentController@getBankAccount')->name('settings.payments.methods.banks.show')->middleware('permission:settings.payments.methods.view');
            Route::delete('/methods/banks/{bankId}', 'App\Http\Controllers\Settings\PaymentController@deleteBankAccount')->name('settings.payments.methods.banks.delete')->middleware('permission:settings.payments.methods.update');
            Route::post('/methods/banks/{bankId}/toggle', 'App\Http\Controllers\Settings\PaymentController@toggleBankActive')->name('settings.payments.methods.banks.toggle')->middleware('permission:settings.payments.methods.update');

            Route::get('/midtrans-config', 'App\Http\Controllers\Settings\PaymentController@midtransConfig')->name('settings.payments.midtrans-config')->middleware('permission:settings.payments.midtrans.view');
            Route::post('/midtrans-config/api', 'App\Http\Controllers\Settings\PaymentController@updateMidtransApi')->name('settings.payments.midtrans-config.api.update')->middleware('permission:settings.payments.midtrans.update');
            Route::post('/midtrans-config/methods', 'App\Http\Controllers\Settings\PaymentController@updateMidtransPaymentMethods')->name('settings.payments.midtrans-config.methods.update')->middleware('permission:settings.payments.midtrans.update');
            Route::post('/midtrans-config/transaction', 'App\Http\Controllers\Settings\PaymentController@updateMidtransTransaction')->name('settings.payments.midtrans-config.transaction.update')->middleware('permission:settings.payments.midtrans.update');
            Route::post('/midtrans-config/test', 'App\Http\Controllers\Settings\PaymentController@testMidtransConnection')->name('settings.payments.midtrans-config.test')->middleware('permission:settings.payments.midtrans.update');
            Route::post('/midtrans-config/sync', 'App\Http\Controllers\Settings\PaymentController@syncMidtransPaymentMethods')->name('settings.payments.midtrans-config.sync')->middleware('permission:settings.payments.midtrans.update');

            Route::get('/tax-settings', 'App\Http\Controllers\Settings\PaymentController@taxSettings')->name('settings.payments.tax-settings')->middleware('permission:settings.payments.tax.view');
            Route::post('/tax-settings', 'App\Http\Controllers\Settings\PaymentController@updateTaxSettings')->name('settings.payments.tax-settings.update')->middleware('permission:settings.payments.tax.update');
        });

        // Shipping Settings Routes
        Route::prefix('shippings')->group(function () {
            Route::get('/methods', 'App\Http\Controllers\Settings\ShippingController@shippingMethods')->name('settings.shippings.methods')->middleware('permission:settings.shippings.methods.view');

            Route::get('/rajaongkir-config', 'App\Http\Controllers\Settings\ShippingController@rajaongkirConfig')->name('settings.shippings.rajaongkir-config')->middleware('permission:settings.shippings.rajaongkir.view');
            Route::post('/rajaongkir-config', 'App\Http\Controllers\Settings\ShippingController@updateRajaongkirConfig')->name('settings.shippings.rajaongkir-config.update')->middleware('permission:settings.shippings.rajaongkir.update');
            Route::post('/rajaongkir-config/test', 'App\Http\Controllers\Settings\ShippingController@testRajaongkirConnection')->name('settings.shippings.rajaongkir-config.test')->middleware('permission:settings.shippings.rajaongkir.update');
            Route::post('/rajaongkir-config/sync-locations', 'App\Http\Controllers\Settings\ShippingController@syncLocations')->name('settings.shippings.rajaongkir-config.sync-locations')->middleware('permission:settings.shippings.rajaongkir.update');

            Route::get('/origin-address', 'App\Http\Controllers\Settings\ShippingController@originAddress')->name('settings.shippings.origin-address')->middleware('permission:settings.shippings.origin.view');
            Route::post('/origin-address', 'App\Http\Controllers\Settings\ShippingController@updateOriginAddress')->name('settings.shippings.origin-address.update')->middleware('permission:settings.shippings.origin.update');

            // Shipping API Routes (untuk dropdown & calculations) - need view permission
            Route::get('/api/provinces', 'App\Http\Controllers\Settings\ShippingController@getProvinces')->name('settings.shippings.api.provinces')->middleware('permission:settings.shippings.methods.view');
            Route::get('/api/cities', 'App\Http\Controllers\Settings\ShippingController@getAllCities')->name('settings.shippings.api.all-cities')->middleware('permission:settings.shippings.methods.view');
            Route::get('/api/cities/{provinceId}', 'App\Http\Controllers\Settings\ShippingController@getCities')->name('settings.shippings.api.cities')->middleware('permission:settings.shippings.methods.view');
            Route::get('/api/districts/{cityId}', 'App\Http\Controllers\Settings\ShippingController@getDistricts')->name('settings.shippings.api.districts')->middleware('permission:settings.shippings.methods.view');
            Route::post('/api/calculate-shipping', 'App\Http\Controllers\Settings\ShippingController@calculateShippingCost')->name('settings.shippings.api.calculate')->middleware('permission:settings.shippings.methods.view');

            // Wilayah.id API Routes (untuk origin address) - need view permission
            Route::get('/api/wilayah/provinces', 'App\Http\Controllers\Settings\ShippingController@getWilayahProvinces')->name('settings.shippings.api.wilayah.provinces')->middleware('permission:settings.shippings.origin.view');
            Route::get('/api/wilayah/regencies/{provinceCode}', 'App\Http\Controllers\Settings\ShippingController@getWilayahRegencies')->name('settings.shippings.api.wilayah.regencies')->middleware('permission:settings.shippings.origin.view');
            Route::get('/api/wilayah/districts/{regencyCode}', 'App\Http\Controllers\Settings\ShippingController@getWilayahDistricts')->name('settings.shippings.api.wilayah.districts')->middleware('permission:settings.shippings.origin.view');
            Route::get('/api/wilayah/villages/{districtCode}', 'App\Http\Controllers\Settings\ShippingController@getWilayahVillages')->name('settings.shippings.api.wilayah.villages')->middleware('permission:settings.shippings.origin.view');
        });
    });

    // Catalog
    Route::group(['prefix' => 'catalog'], function () {
        Route::prefix('products')->group(function () {
            Route::controller(AllProductsController::class)->group(function () {
                Route::get('/all-products', 'allProducts')->name('catalog.products.all-products')->middleware('permission:catalog.products.all-products.view');
                Route::delete('/{id}', 'destroy')->name('catalog.products.destroy')->middleware('permission:catalog.products.all-products.delete');
                Route::post('/{id}/toggle-status', 'toggleStatus')->name('catalog.products.toggle-status')->middleware('permission:catalog.products.all-products.update');
                Route::post('/{id}/toggle-featured', 'toggleFeatured')->name('catalog.products.toggle-featured')->middleware('permission:catalog.products.all-products.update');
            });

            Route::controller(AddProductsController::class)->group(function () {
                Route::get('/add-products', 'addProducts')->name('catalog.products.add-products')->middleware('permission:catalog.products.add-products.view');
                Route::get('/{id}/edit', 'edit')->name('catalog.products.edit')->middleware('permission:catalog.products.all-products.update');
                Route::post('/store', 'store')->name('catalog.products.store')->middleware('permission:catalog.products.add-products.create');
                Route::put('/{id}', 'update')->name('catalog.products.update')->middleware('permission:catalog.products.all-products.update');

                // Product Images
                Route::post('/{id}/images/upload', 'uploadImages')->name('catalog.products.images.upload')->middleware('permission:catalog.products.all-products.update');
                Route::delete('/{productId}/images/{imageId}', 'deleteImage')->name('catalog.products.images.delete')->middleware('permission:catalog.products.all-products.update');
                Route::post('/{productId}/images/{imageId}/set-primary', 'setPrimaryImage')->name('catalog.products.images.set-primary')->middleware('permission:catalog.products.all-products.update');
                Route::post('/{productId}/images/update-order', 'updateImagesOrder')->name('catalog.products.images.update-order')->middleware('permission:catalog.products.all-products.update');

                // Product Variants
                Route::post('/{productId}/variants/store', 'storeVariant')->name('catalog.products.variants.store')->middleware('permission:catalog.products.add-products.create');
                Route::put('/{productId}/variants/{variantId}', 'updateVariant')->name('catalog.products.variants.update')->middleware('permission:catalog.products.all-products.update');
                Route::delete('/{productId}/variants/{variantId}', 'deleteVariant')->name('catalog.products.variants.delete')->middleware('permission:catalog.products.all-products.delete');
            });

            Route::controller(CategoriesController::class)->group(function () {
                Route::get('/categories', 'categories')->name('catalog.products.categories')->middleware('permission:catalog.products.categories.view');
                Route::get('/categories/tree', 'getTree')->name('catalog.products.categories.tree')->middleware('permission:catalog.products.categories.view');
                Route::post('/categories/store', 'store')->name('catalog.products.categories.store')->middleware('permission:catalog.products.categories.create');
                Route::put('/categories/{id}', 'update')->name('catalog.products.categories.update')->middleware('permission:catalog.products.categories.update');
                Route::delete('/categories/{id}', 'destroy')->name('catalog.products.categories.destroy')->middleware('permission:catalog.products.categories.delete');
                Route::post('/categories/{id}/toggle-active', 'toggleActive')->name('catalog.products.categories.toggle-active')->middleware('permission:catalog.products.categories.update');
                Route::post('/categories/update-order', 'updateOrder')->name('catalog.products.categories.update-order')->middleware('permission:catalog.products.categories.update');
                Route::delete('/categories/{id}/delete-image', 'deleteImage')->name('catalog.products.categories.delete-image')->middleware('permission:catalog.products.categories.update');
            });

            Route::controller(BrandsController::class)->group(function () {
                Route::get('/brands', 'brands')->name('catalog.products.brands')->middleware('permission:catalog.products.brands.view');
                Route::post('/brands/store', 'store')->name('catalog.products.brands.store')->middleware('permission:catalog.products.brands.create');
                Route::put('/brands/{id}', 'update')->name('catalog.products.brands.update')->middleware('permission:catalog.products.brands.update');
                Route::delete('/brands/{id}', 'destroy')->name('catalog.products.brands.destroy')->middleware('permission:catalog.products.brands.delete');
                Route::post('/brands/{id}/toggle-active', 'toggleActive')->name('catalog.products.brands.toggle-active')->middleware('permission:catalog.products.brands.update');
                Route::delete('/brands/{id}/delete-logo', 'deleteLogo')->name('catalog.products.brands.delete-logo')->middleware('permission:catalog.products.brands.update');
            });

            Route::controller(VariantsController::class)->group(function () {
                Route::get('/variants', 'variants')->name('catalog.products.variants')->middleware('permission:catalog.products.variants.view');
            });
        });
    });
});
