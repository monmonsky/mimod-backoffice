<?php

use App\Http\Controllers\AccessControl\ModuleController;
use App\Http\Controllers\AccessControl\PermissionController;
use App\Http\Controllers\AccessControl\PermissionGroupController;
use App\Http\Controllers\AccessControl\RoleController;
use App\Http\Controllers\AccessControl\UserActivityController;
use App\Http\Controllers\AccessControl\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Catalog\AddProductsController;
use App\Http\Controllers\Catalog\AllProductsController;
use App\Http\Controllers\Catalog\BrandsController;
use App\Http\Controllers\Catalog\CategoriesController;
use App\Http\Controllers\Catalog\VariantsController;
use App\Http\Controllers\Customers\AllCustomersController;
use App\Http\Controllers\Customers\CustomerSegmentsController;
use App\Http\Controllers\Customers\CustomerGroupsController;
use App\Http\Controllers\Customers\CustomerLoyaltyController;
use App\Http\Controllers\Customers\CustomerReviewsController;
use App\Http\Controllers\Customers\VipCustomersController;
use App\Http\Controllers\Marketing\BundleDealsController;
use App\Http\Controllers\Marketing\CouponsController;
use App\Http\Controllers\Marketing\FlashSalesController;
use App\Http\Controllers\Orders\AllOrdersController;
use App\Http\Controllers\Orders\CancelledOrdersController;
use App\Http\Controllers\Orders\CompletedOrdersController;
use App\Http\Controllers\Orders\PendingOrdersController;
use App\Http\Controllers\Orders\ProcessingOrdersController;
use App\Http\Controllers\Orders\ShippedOrdersController;
use App\Http\Controllers\Reports\InventoryReportController;
use App\Http\Controllers\Reports\ProductPerformanceController;
use App\Http\Controllers\Reports\RevenueReportController;
use App\Http\Controllers\Reports\SalesReportController;
use App\Http\Controllers\Settings\ApiTokenController;
use App\Http\Controllers\Settings\GeneralController;
use App\Http\Controllers\Settings\PaymentController;
use App\Http\Controllers\Settings\ShippingController;
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
    Route::prefix('user')->group(function () {
        Route::controller(UserController::class)->group(function () {
            Route::get('/', 'index')->name('user.index')->middleware('permission:access-control.users.view');
            Route::get('/create', 'create')->name('user.create')->middleware('permission:access-control.users.create');
            Route::post('/store', 'store')->name('user.store')->middleware('permission:access-control.users.create');
            Route::get('/{id}/edit', 'edit')->name('user.edit')->middleware('permission:access-control.users.update');
            Route::put('/{id}', 'update')->name('user.update')->middleware('permission:access-control.users.update');
            Route::delete('/{id}', 'destroy')->name('user.destroy')->middleware('permission:access-control.users.delete');
            Route::post('/{id}/toggle-active', 'toggleActive')->name('user.toggle-active')->middleware('permission:access-control.users.update');
        });
    });

    // Access Control
    Route::group(['prefix' => 'access-control'], function () {
        // Modules
        Route::prefix('modules')->group(function () {
            Route::controller(ModuleController::class)->group(function () {
                Route::get('/', 'index')->name('modules.index')->middleware('permission:access-control.modules.view');
                Route::get('/create', 'create')->name('modules.create')->middleware('permission:access-control.modules.create');
                Route::get('/{id}/edit', 'edit')->name('modules.edit')->middleware('permission:access-control.modules.update');
                Route::get('/all', 'getAll')->name('modules.all')->middleware('permission:access-control.modules.view');
                Route::post('/store', 'store')->name('modules.store')->middleware('permission:access-control.modules.create');
                Route::put('/{id}', 'update')->name('modules.update')->middleware('permission:access-control.modules.update');
                Route::delete('/{id}', 'destroy')->name('modules.destroy')->middleware('permission:access-control.modules.delete');
                Route::post('/{id}/toggle-active', 'toggleActive')->name('modules.toggle-active')->middleware('permission:access-control.modules.update');
                Route::post('/{id}/toggle-visible', 'toggleVisible')->name('modules.toggle-visible')->middleware('permission:access-control.modules.update');
                Route::post('/update-order', 'updateOrder')->name('modules.update-order')->middleware('permission:access-control.modules.update');
                Route::post('/update-group-order', 'updateGroupOrder')->name('modules.update-group-order')->middleware('permission:access-control.modules.update');
            });
        });

        // Roles
        Route::prefix('role')->group(function () {
            Route::controller(RoleController::class)->group(function () {
                Route::get('/', 'index')->name('role.index')->middleware('permission:access-control.roles.view');
                Route::get('/create', 'create')->name('role.create')->middleware('permission:access-control.roles.create');
                Route::post('/store', 'store')->name('role.store')->middleware('permission:access-control.roles.create');
                Route::get('/{id}/edit', 'edit')->name('role.edit')->middleware('permission:access-control.roles.update');
                Route::get('/{id}/detail', 'detail')->name('role.detail')->middleware('permission:access-control.roles.view');
                Route::put('/{id}', 'update')->name('role.update')->middleware('permission:access-control.roles.update');
                Route::delete('/{id}', 'destroy')->name('role.destroy')->middleware('permission:access-control.roles.delete');
                Route::post('/{id}/toggle-active', 'toggleActive')->name('role.toggle-active')->middleware('permission:access-control.roles.update');
            });
        });

        // Permissions
        Route::prefix('permission')->group(function () {
            Route::controller(PermissionController::class)->group(function () {
                Route::get('/', 'index')->name('permission.index')->middleware('permission:access-control.permissions.view');
                Route::get('/create', 'create')->name('permission.create')->middleware('permission:access-control.permissions.create');
                Route::post('/store', 'store')->name('permission.store')->middleware('permission:access-control.permissions.create');
                Route::get('/{id}/edit', 'edit')->name('permission.edit')->middleware('permission:access-control.permissions.update');
                Route::put('/{id}', 'update')->name('permission.update')->middleware('permission:access-control.permissions.update');
                Route::delete('/{id}', 'destroy')->name('permission.destroy')->middleware('permission:access-control.permissions.delete');
            });
        });

        // Permission Groups
        Route::prefix('permission-group')->group(function () {
            Route::controller(PermissionGroupController::class)->group(function () {
                Route::get('/', 'index')->name('permission-group.index')->middleware('permission:access-control.permissions.view');
                Route::get('/create', 'create')->name('permission-group.create')->middleware('permission:access-control.permissions.create');
                Route::post('/store', 'store')->name('permission-group.store')->middleware('permission:access-control.permissions.create');
                Route::get('/{id}/edit', 'edit')->name('permission-group.edit')->middleware('permission:access-control.permissions.update');
                Route::put('/{id}', 'update')->name('permission-group.update')->middleware('permission:access-control.permissions.update');
                Route::delete('/{id}', 'destroy')->name('permission-group.destroy')->middleware('permission:access-control.permissions.delete');
            });
        });

        // User Activities
        Route::prefix('user-activities')->group(function () {
            Route::controller(UserActivityController::class)->group(function () {
                Route::get('/', 'index')->name('access-control.user-activities.index')->middleware('permission:access-control.user-activities.view');
                Route::get('/{id}', 'show')->name('access-control.user-activities.show')->middleware('permission:access-control.user-activities.view');
                Route::delete('/clear', 'clear')->name('access-control.user-activities.clear')->middleware('permission:access-control.user-activities.clear');
                Route::get('/export/csv', 'export')->name('access-control.user-activities.export')->middleware('permission:access-control.user-activities.export');
            });
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
        Route::prefix('sales')->group(function () {
            Route::controller(SalesReportController::class)->group(function () {
                Route::get('/', 'index')->name('reports.sales')->middleware('permission:reports.sales.view');
                Route::post('/export', 'export')->name('reports.sales.export')->middleware('permission:reports.sales.export');
            });
        });

        // Revenue Report
        Route::prefix('revenue')->group(function () {
            Route::controller(RevenueReportController::class)->group(function () {
                Route::get('/', 'index')->name('reports.revenue')->middleware('permission:reports.revenue.view');
                Route::post('/export', 'export')->name('reports.revenue.export')->middleware('permission:reports.revenue.export');
            });
        });

        // Product Performance
        Route::prefix('product-performance')->group(function () {
            Route::controller(ProductPerformanceController::class)->group(function () {
                Route::get('/', 'index')->name('reports.product-performance')->middleware('permission:reports.product-performance.view');
                Route::post('/export', 'export')->name('reports.product-performance.export')->middleware('permission:reports.product-performance.export');
            });
        });

        // Inventory Report
        Route::prefix('inventory')->group(function () {
            Route::controller(InventoryReportController::class)->group(function () {
                Route::get('/', 'index')->name('reports.inventory')->middleware('permission:reports.inventory.view');
                Route::post('/export', 'export')->name('reports.inventory.export')->middleware('permission:reports.inventory.export');
            });
        });
    });

    // Marketing
    Route::group(['prefix' => 'marketing'], function () {
        Route::prefix('coupons')->group(function () {
            Route::controller(CouponsController::class)->group(function () {
                Route::get('/', 'index')->name('marketing.coupons.index')->middleware('permission:marketing.coupons.view');
            });
        });

        Route::prefix('flash-sales')->group(function () {
            Route::controller(FlashSalesController::class)->group(function () {
                Route::get('/', 'index')->name('marketing.flash-sales.index')->middleware('permission:marketing.flash-sales.view');
            });
        });

        Route::prefix('bundle-deals')->group(function () {
            Route::controller(BundleDealsController::class)->group(function () {
                Route::get('/', 'index')->name('marketing.bundle-deals.index')->middleware('permission:marketing.bundle-deals.view');
            });
        });
    });

    // Customers Management
    Route::group(['prefix' => 'customers'], function () {
        // All Customers
        Route::prefix('all-customers')->group(function () {
            Route::controller(AllCustomersController::class)->group(function () {
                Route::get('/', 'index')->name('customers.all-customers.index')->middleware('permission:customers.all-customers.view');
                Route::get('/{id}', 'show')->name('customers.all-customers.show')->middleware('permission:customers.all-customers.view');
                Route::get('/{id}/detail', 'detail')->name('customers.all-customers.detail')->middleware('permission:customers.all-customers.view');
                Route::post('/', 'store')->name('customers.all-customers.store')->middleware('permission:customers.all-customers.create');
                Route::put('/{id}', 'update')->name('customers.all-customers.update')->middleware('permission:customers.all-customers.update');
                Route::delete('/{id}', 'destroy')->name('customers.all-customers.destroy')->middleware('permission:customers.all-customers.delete');
                Route::post('/{id}/toggle-status', 'toggleStatus')->name('customers.all-customers.toggle-status')->middleware('permission:customers.all-customers.update');
            });
        });

        // Customer Segments
        Route::prefix('customer-segments')->group(function () {
            Route::controller(CustomerSegmentsController::class)->group(function () {
                Route::get('/', 'index')->name('customers.customer-segments.index')->middleware('permission:customers.customer-segments.view');
                Route::get('/{id}', 'show')->name('customers.customer-segments.show')->middleware('permission:customers.customer-segments.view');
                Route::post('/', 'store')->name('customers.customer-segments.store')->middleware('permission:customers.customer-segments.create');
                Route::put('/{id}', 'update')->name('customers.customer-segments.update')->middleware('permission:customers.customer-segments.update');
                Route::delete('/{id}', 'destroy')->name('customers.customer-segments.destroy')->middleware('permission:customers.customer-segments.delete');
                Route::post('/{id}/recalculate', 'recalculateCustomers')->name('customers.customer-segments.recalculate')->middleware('permission:customers.customer-segments.update');
            });
        });

        // Customer Groups
        Route::prefix('customer-groups')->group(function () {
            Route::controller(CustomerGroupsController::class)->group(function () {
                Route::get('/', 'index')->name('customers.groups.index')->middleware('permission:customers.customer-groups.view');
                Route::get('/{id}/members', 'members')->name('customers.groups.members')->middleware('permission:customers.customer-groups.view');
            });
        });

        // Customer Loyalty
        Route::prefix('customer-loyalty')->group(function () {
            Route::controller(CustomerLoyaltyController::class)->group(function () {
                Route::get('/', 'index')->name('customers.loyalty.index')->middleware('permission:customers.customer-loyalty.view');
            });
        });

        // Customer Reviews
        Route::prefix('customer-reviews')->group(function () {
            Route::controller(CustomerReviewsController::class)->group(function () {
                Route::get('/', 'index')->name('customers.reviews.index')->middleware('permission:customers.customer-reviews.view');
            });
        });

        // VIP Customers
        Route::prefix('vip-customers')->group(function () {
            Route::controller(VipCustomersController::class)->group(function () {
                Route::get('/', 'index')->name('customers.vip-customers.index')->middleware('permission:customers.vip-customers.view');
                Route::post('/{id}/toggle-vip', 'toggleVip')->name('customers.vip-customers.toggle-vip')->middleware('permission:customers.vip-customers.manage');
            });
        });
    });

    // Orders Management
    Route::group(['prefix' => 'orders'], function () {
        // All Orders
        Route::prefix('all-orders')->group(function () {
            Route::controller(AllOrdersController::class)->group(function () {
                Route::get('/', 'index')->name('orders.all-orders.index')->middleware('permission:orders.all-orders.view');
                Route::get('/{id}', 'show')->name('orders.all-orders.show')->middleware('permission:orders.all-orders.view');
                Route::put('/{id}', 'update')->name('orders.all-orders.update')->middleware('permission:orders.all-orders.update');
                Route::delete('/{id}', 'destroy')->name('orders.all-orders.destroy')->middleware('permission:orders.all-orders.delete');
                Route::post('/{id}/status', 'updateStatus')->name('orders.all-orders.update-status')->middleware('permission:orders.all-orders.update');
                Route::post('/export', 'export')->name('orders.all-orders.export')->middleware('permission:orders.all-orders.export');
            });
        });

        // Pending Orders
        Route::prefix('pending-orders')->group(function () {
            Route::controller(PendingOrdersController::class)->group(function () {
                Route::get('/', 'index')->name('orders.pending-orders.index')->middleware('permission:orders.pending-orders.view');
                Route::post('/{id}/confirm', 'confirm')->name('orders.pending-orders.confirm')->middleware('permission:orders.pending-orders.confirm');
                Route::post('/{id}/cancel', 'cancel')->name('orders.pending-orders.cancel')->middleware('permission:orders.pending-orders.cancel');
            });
        });

        // Processing Orders
        Route::prefix('processing-orders')->group(function () {
            Route::controller(ProcessingOrdersController::class)->group(function () {
                Route::get('/', 'index')->name('orders.processing-orders.index')->middleware('permission:orders.processing-orders.view');
                Route::post('/{id}/ship', 'ship')->name('orders.processing-orders.ship')->middleware('permission:orders.processing-orders.ship');
            });
        });

        // Shipped Orders
        Route::prefix('shipped-orders')->group(function () {
            Route::controller(ShippedOrdersController::class)->group(function () {
                Route::get('/', 'index')->name('orders.shipped-orders.index')->middleware('permission:orders.shipped-orders.view');
                Route::post('/{id}/complete', 'complete')->name('orders.shipped-orders.complete')->middleware('permission:orders.shipped-orders.complete');
            });
        });

        // Completed Orders
        Route::prefix('completed-orders')->group(function () {
            Route::controller(CompletedOrdersController::class)->group(function () {
                Route::get('/', 'index')->name('orders.completed-orders.index')->middleware('permission:orders.completed-orders.view');
            });
        });

        // Cancelled Orders
        Route::prefix('cancelled-orders')->group(function () {
            Route::controller(CancelledOrdersController::class)->group(function () {
                Route::get('/', 'index')->name('orders.cancelled-orders.index')->middleware('permission:orders.cancelled-orders.view');
            });
        });
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
            Route::controller(GeneralController::class)->group(function () {
                Route::get('/store', 'storeInfo')->name('settings.generals.store')->middleware('permission:settings.generals.store.view');
                Route::post('/store', 'updateStoreInfo')->name('settings.generals.store.update')->middleware('permission:settings.generals.store.update');
                Route::post('/store/upload-logo', 'uploadStoreLogo')->name('settings.generals.store.upload-logo')->middleware('permission:settings.generals.store.update');
                Route::delete('/store/delete-logo', 'deleteStoreLogo')->name('settings.generals.store.delete-logo')->middleware('permission:settings.generals.store.update');

                Route::get('/email', 'emailSettings')->name('settings.generals.email')->middleware('permission:settings.generals.email.view');
                Route::post('/email', 'updateEmailSettings')->name('settings.generals.email.update')->middleware('permission:settings.generals.email.update');
                Route::post('/email/test', 'testEmailConnection')->name('settings.generals.email.test')->middleware('permission:settings.generals.email.update');

                Route::get('/seo', 'seoMeta')->name('settings.generals.seo')->middleware('permission:settings.generals.seo.view');
                Route::post('/seo', 'updateSeoMeta')->name('settings.generals.seo.update')->middleware('permission:settings.generals.seo.update');

                Route::get('/system', 'systemConfig')->name('settings.generals.system')->middleware('permission:settings.generals.system.view');
                Route::post('/system', 'updateSystemConfig')->name('settings.generals.system.update')->middleware('permission:settings.generals.system.update');
            });

            Route::controller(ApiTokenController::class)->group(function () {
                Route::get('/api-tokens', 'index')->name('settings.generals.api-tokens')->middleware('permission:settings.generals.api-tokens.view');
                Route::post('/api-tokens/generate', 'generate')->name('settings.generals.api-tokens.generate')->middleware('permission:settings.generals.api-tokens.generate');
                Route::delete('/api-tokens/{tokenId}', 'revoke')->name('settings.generals.api-tokens.revoke')->middleware('permission:settings.generals.api-tokens.revoke');
                Route::delete('/api-tokens', 'revokeAll')->name('settings.generals.api-tokens.revoke-all')->middleware('permission:settings.generals.api-tokens.revoke');
                Route::get('/api-tokens/{tokenId}/show', 'show')->name('settings.generals.api-tokens.show')->middleware('permission:settings.generals.api-tokens.view');
            });
        });

        // Payment Settings Routes
        Route::prefix('payments')->group(function () {
            Route::controller(PaymentController::class)->group(function () {
                Route::get('/methods', 'paymentMethods')->name('settings.payments.methods')->middleware('permission:settings.payments.methods.view');
                Route::post('/methods/{method}/toggle', 'togglePaymentMethod')->name('settings.payments.methods.toggle')->middleware('permission:settings.payments.methods.update');

                // Bank Account Routes
                Route::post('/methods/banks/store', 'storeBankAccount')->name('settings.payments.methods.banks.store')->middleware('permission:settings.payments.methods.update');
                Route::get('/methods/banks/{bankId}', 'getBankAccount')->name('settings.payments.methods.banks.show')->middleware('permission:settings.payments.methods.view');
                Route::delete('/methods/banks/{bankId}', 'deleteBankAccount')->name('settings.payments.methods.banks.delete')->middleware('permission:settings.payments.methods.update');
                Route::post('/methods/banks/{bankId}/toggle', 'toggleBankActive')->name('settings.payments.methods.banks.toggle')->middleware('permission:settings.payments.methods.update');

                Route::get('/midtrans-config', 'midtransConfig')->name('settings.payments.midtrans-config')->middleware('permission:settings.payments.midtrans.view');
                Route::post('/midtrans-config/api', 'updateMidtransApi')->name('settings.payments.midtrans-config.api.update')->middleware('permission:settings.payments.midtrans.update');
                Route::post('/midtrans-config/methods', 'updateMidtransPaymentMethods')->name('settings.payments.midtrans-config.methods.update')->middleware('permission:settings.payments.midtrans.update');
                Route::post('/midtrans-config/transaction', 'updateMidtransTransaction')->name('settings.payments.midtrans-config.transaction.update')->middleware('permission:settings.payments.midtrans.update');
                Route::post('/midtrans-config/test', 'testMidtransConnection')->name('settings.payments.midtrans-config.test')->middleware('permission:settings.payments.midtrans.update');
                Route::post('/midtrans-config/sync', 'syncMidtransPaymentMethods')->name('settings.payments.midtrans-config.sync')->middleware('permission:settings.payments.midtrans.update');

                Route::get('/tax-settings', 'taxSettings')->name('settings.payments.tax-settings')->middleware('permission:settings.payments.tax.view');
                Route::post('/tax-settings', 'updateTaxSettings')->name('settings.payments.tax-settings.update')->middleware('permission:settings.payments.tax.update');
            });
        });

        // Shipping Settings Routes
        Route::prefix('shippings')->group(function () {
            Route::controller(ShippingController::class)->group(function () {
                Route::get('/methods', 'shippingMethods')->name('settings.shippings.methods')->middleware('permission:settings.shippings.methods.view');

                Route::get('/rajaongkir-config', 'rajaongkirConfig')->name('settings.shippings.rajaongkir-config')->middleware('permission:settings.shippings.rajaongkir.view');
                Route::post('/rajaongkir-config', 'updateRajaongkirConfig')->name('settings.shippings.rajaongkir-config.update')->middleware('permission:settings.shippings.rajaongkir.update');
                Route::post('/rajaongkir-config/test', 'testRajaongkirConnection')->name('settings.shippings.rajaongkir-config.test')->middleware('permission:settings.shippings.rajaongkir.update');
                Route::post('/rajaongkir-config/sync-locations', 'syncLocations')->name('settings.shippings.rajaongkir-config.sync-locations')->middleware('permission:settings.shippings.rajaongkir.update');

                Route::get('/origin-address', 'originAddress')->name('settings.shippings.origin-address')->middleware('permission:settings.shippings.origin.view');
                Route::post('/origin-address', 'updateOriginAddress')->name('settings.shippings.origin-address.update')->middleware('permission:settings.shippings.origin.update');

                // Shipping API Routes (untuk dropdown & calculations) - need view permission
                Route::get('/api/provinces', 'getProvinces')->name('settings.shippings.api.provinces')->middleware('permission:settings.shippings.methods.view');
                Route::get('/api/cities', 'getAllCities')->name('settings.shippings.api.all-cities')->middleware('permission:settings.shippings.methods.view');
                Route::get('/api/cities/{provinceId}', 'getCities')->name('settings.shippings.api.cities')->middleware('permission:settings.shippings.methods.view');
                Route::get('/api/districts/{cityId}', 'getDistricts')->name('settings.shippings.api.districts')->middleware('permission:settings.shippings.methods.view');
                Route::post('/api/calculate-shipping', 'calculateShippingCost')->name('settings.shippings.api.calculate')->middleware('permission:settings.shippings.methods.view');

                // Wilayah.id API Routes (untuk origin address) - need view permission
                Route::get('/api/wilayah/provinces', 'getWilayahProvinces')->name('settings.shippings.api.wilayah.provinces')->middleware('permission:settings.shippings.origin.view');
                Route::get('/api/wilayah/regencies/{provinceCode}', 'getWilayahRegencies')->name('settings.shippings.api.wilayah.regencies')->middleware('permission:settings.shippings.origin.view');
                Route::get('/api/wilayah/districts/{regencyCode}', 'getWilayahDistricts')->name('settings.shippings.api.wilayah.districts')->middleware('permission:settings.shippings.origin.view');
                Route::get('/api/wilayah/villages/{districtCode}', 'getWilayahVillages')->name('settings.shippings.api.wilayah.villages')->middleware('permission:settings.shippings.origin.view');
            });
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
