<?php

use App\Http\Controllers\Auth\LoginController;
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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::controller(LoginController::class)->group(function () {
        // Logout routes
        Route::post('/logout', 'logout')->name('logout');
    });


    Route::group(['prefix' => 'user'], function () {
        Route::get('/', function () {
            return view('pages.user.index');
        })->name('user.index');

        Route::get('/create', function () {
            return view('pages.user.create');
        })->name('user.create');

        Route::get('/{id}/edit', function () {
            return view('pages.user.edit');
        })->name('user.edit');
    });

    Route::group(['prefix' => 'access-control'], function () {
        Route::group(['prefix' => 'modules'], function () {
            Route::get('/', 'App\Http\Controllers\AccessControl\ModuleController@index')->name('modules.index');
            Route::get('/create', 'App\Http\Controllers\AccessControl\ModuleController@create')->name('modules.create');
            Route::get('/{id}/edit', 'App\Http\Controllers\AccessControl\ModuleController@edit')->name('modules.edit');
            Route::get('/all', 'App\Http\Controllers\AccessControl\ModuleController@getAll')->name('modules.all');
            Route::post('/store', 'App\Http\Controllers\AccessControl\ModuleController@store')->name('modules.store');
            Route::put('/{id}', 'App\Http\Controllers\AccessControl\ModuleController@update')->name('modules.update');
            Route::delete('/{id}', 'App\Http\Controllers\AccessControl\ModuleController@destroy')->name('modules.destroy');
            Route::post('/{id}/toggle-active', 'App\Http\Controllers\AccessControl\ModuleController@toggleActive')->name('modules.toggle-active');
            Route::post('/{id}/toggle-visible', 'App\Http\Controllers\AccessControl\ModuleController@toggleVisible')->name('modules.toggle-visible');
            Route::post('/update-order', 'App\Http\Controllers\AccessControl\ModuleController@updateOrder')->name('modules.update-order');
        });

        Route::group(['prefix' => 'role'], function () {
            Route::get('/', 'App\Http\Controllers\AccessControl\RoleController@index')->name('role.index');
            Route::get('/create', 'App\Http\Controllers\AccessControl\RoleController@create')->name('role.create');
            Route::post('/store', 'App\Http\Controllers\AccessControl\RoleController@store')->name('role.store');
            Route::get('/{id}/edit', 'App\Http\Controllers\AccessControl\RoleController@edit')->name('role.edit');
            Route::put('/{id}', 'App\Http\Controllers\AccessControl\RoleController@update')->name('role.update');
            Route::delete('/{id}', 'App\Http\Controllers\AccessControl\RoleController@destroy')->name('role.destroy');
            Route::post('/{id}/toggle-active', 'App\Http\Controllers\AccessControl\RoleController@toggleActive')->name('role.toggle-active');
        });

        Route::group(['prefix' => 'permission'], function () {
            Route::get('/', 'App\Http\Controllers\AccessControl\PermissionController@index')->name('permission.index');
            Route::get('/create', 'App\Http\Controllers\AccessControl\PermissionController@create')->name('permission.create');
            Route::post('/store', 'App\Http\Controllers\AccessControl\PermissionController@store')->name('permission.store');
            Route::get('/{id}/edit', 'App\Http\Controllers\AccessControl\PermissionController@edit')->name('permission.edit');
            Route::put('/{id}', 'App\Http\Controllers\AccessControl\PermissionController@update')->name('permission.update');
            Route::delete('/{id}', 'App\Http\Controllers\AccessControl\PermissionController@destroy')->name('permission.destroy');
        });

        Route::group(['prefix' => 'permission-group'], function () {
            Route::get('/', 'App\Http\Controllers\AccessControl\PermissionGroupController@index')->name('permission-group.index');
            Route::get('/create', 'App\Http\Controllers\AccessControl\PermissionGroupController@create')->name('permission-group.create');
            Route::post('/store', 'App\Http\Controllers\AccessControl\PermissionGroupController@store')->name('permission-group.store');
            Route::get('/{id}/edit', 'App\Http\Controllers\AccessControl\PermissionGroupController@edit')->name('permission-group.edit');
            Route::put('/{id}', 'App\Http\Controllers\AccessControl\PermissionGroupController@update')->name('permission-group.update');
            Route::delete('/{id}', 'App\Http\Controllers\AccessControl\PermissionGroupController@destroy')->name('permission-group.destroy');
        });

        Route::group(['prefix' => 'activity-log'], function () {
            Route::get('/', function () {
                return view('pages.activity-log.index');
            })->name('activity-log.index');
        });

        Route::group(['prefix' => 'session'], function () {
            Route::get('/', function () {
                return view('pages.session.index');
            })->name('session.index');
        });
    });
    

    Route::group(['prefix' => 'reports'], function () {
        Route::get('/sales', function () {
            return view('pages.reports.sales');
        })->name('reports.sales');

        Route::get('/product-performance', function () {
            return view('pages.reports.product-performance');
        })->name('reports.product-performance');

        Route::get('/customer', function () {
            return view('pages.reports.customer');
        })->name('reports.customer');

        Route::get('/payment', function () {
            return view('pages.reports.payment');
        })->name('reports.payment');

        Route::get('/inventory', function () {
            return view('pages.reports.inventory');
        })->name('reports.inventory');
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

    Route::group(['prefix' => 'settings'], function () {
        // General Settings Routes
        Route::prefix('generals')->group(function () {
            Route::get('/store', 'App\Http\Controllers\Settings\GeneralController@storeInfo')->name('settings.generals.store');
            Route::post('/store', 'App\Http\Controllers\Settings\GeneralController@updateStoreInfo')->name('settings.generals.store.update');
            Route::post('/store/upload-logo', 'App\Http\Controllers\Settings\GeneralController@uploadStoreLogo')->name('settings.generals.store.upload-logo');
            Route::delete('/store/delete-logo', 'App\Http\Controllers\Settings\GeneralController@deleteStoreLogo')->name('settings.generals.store.delete-logo');

            Route::get('/email', 'App\Http\Controllers\Settings\GeneralController@emailSettings')->name('settings.generals.email');
            Route::post('/email', 'App\Http\Controllers\Settings\GeneralController@updateEmailSettings')->name('settings.generals.email.update');
            Route::post('/email/test', 'App\Http\Controllers\Settings\GeneralController@testEmailConnection')->name('settings.generals.email.test');

            Route::get('/seo', 'App\Http\Controllers\Settings\GeneralController@seoMeta')->name('settings.generals.seo');
            Route::post('/seo', 'App\Http\Controllers\Settings\GeneralController@updateSeoMeta')->name('settings.generals.seo.update');

            Route::get('/system', 'App\Http\Controllers\Settings\GeneralController@systemConfig')->name('settings.generals.system');
            Route::post('/system', 'App\Http\Controllers\Settings\GeneralController@updateSystemConfig')->name('settings.generals.system.update');

            Route::get('/api-tokens', 'App\Http\Controllers\Settings\ApiTokenController@index')->name('settings.generals.api-tokens');
            Route::post('/api-tokens/generate', 'App\Http\Controllers\Settings\ApiTokenController@generate')->name('settings.generals.api-tokens.generate');
            Route::delete('/api-tokens/{tokenId}', 'App\Http\Controllers\Settings\ApiTokenController@revoke')->name('settings.generals.api-tokens.revoke');
            Route::delete('/api-tokens', 'App\Http\Controllers\Settings\ApiTokenController@revokeAll')->name('settings.generals.api-tokens.revoke-all');
            Route::get('/api-tokens/{tokenId}/show', 'App\Http\Controllers\Settings\ApiTokenController@show')->name('settings.generals.api-tokens.show');
        });

        // Payment Settings Routes
        Route::prefix('payments')->group(function () {
            Route::get('/methods', 'App\Http\Controllers\Settings\PaymentController@paymentMethods')->name('settings.payments.methods');

            Route::get('/midtrans-config', 'App\Http\Controllers\Settings\PaymentController@midtransConfig')->name('settings.payments.midtrans-config');
            Route::post('/midtrans-config/api', 'App\Http\Controllers\Settings\PaymentController@updateMidtransApi')->name('settings.payments.midtrans-config.api.update');
            Route::post('/midtrans-config/methods', 'App\Http\Controllers\Settings\PaymentController@updateMidtransPaymentMethods')->name('settings.payments.midtrans-config.methods.update');
            Route::post('/midtrans-config/transaction', 'App\Http\Controllers\Settings\PaymentController@updateMidtransTransaction')->name('settings.payments.midtrans-config.transaction.update');
            Route::post('/midtrans-config/test', 'App\Http\Controllers\Settings\PaymentController@testMidtransConnection')->name('settings.payments.midtrans-config.test');
            Route::post('/midtrans-config/sync', 'App\Http\Controllers\Settings\PaymentController@syncMidtransPaymentMethods')->name('settings.payments.midtrans-config.sync');

            Route::get('/tax-settings', 'App\Http\Controllers\Settings\PaymentController@taxSettings')->name('settings.payments.tax-settings');
            Route::post('/tax-settings', 'App\Http\Controllers\Settings\PaymentController@updateTaxSettings')->name('settings.payments.tax-settings.update');
        });

        // Shipping Settings Routes
        Route::prefix('shippings')->group(function () {
            Route::get('/methods', 'App\Http\Controllers\Settings\ShippingController@shippingMethods')->name('settings.shippings.methods');

            Route::get('/rajaongkir-config', 'App\Http\Controllers\Settings\ShippingController@rajaongkirConfig')->name('settings.shippings.rajaongkir-config');
            Route::post('/rajaongkir-config', 'App\Http\Controllers\Settings\ShippingController@updateRajaongkirConfig')->name('settings.shippings.rajaongkir-config.update');
            Route::post('/rajaongkir-config/test', 'App\Http\Controllers\Settings\ShippingController@testRajaongkirConnection')->name('settings.shippings.rajaongkir-config.test');
            Route::post('/rajaongkir-config/sync-locations', 'App\Http\Controllers\Settings\ShippingController@syncLocations')->name('settings.shippings.rajaongkir-config.sync-locations');

            Route::get('/origin-address', 'App\Http\Controllers\Settings\ShippingController@originAddress')->name('settings.shippings.origin-address');
            Route::post('/origin-address', 'App\Http\Controllers\Settings\ShippingController@updateOriginAddress')->name('settings.shippings.origin-address.update');

            // Shipping API Routes (untuk dropdown & calculations)
            Route::get('/api/provinces', 'App\Http\Controllers\Settings\ShippingController@getProvinces')->name('settings.shippings.api.provinces');
            Route::get('/api/cities', 'App\Http\Controllers\Settings\ShippingController@getAllCities')->name('settings.shippings.api.all-cities');
            Route::get('/api/cities/{provinceId}', 'App\Http\Controllers\Settings\ShippingController@getCities')->name('settings.shippings.api.cities');
            Route::get('/api/districts/{cityId}', 'App\Http\Controllers\Settings\ShippingController@getDistricts')->name('settings.shippings.api.districts');
            Route::post('/api/calculate-shipping', 'App\Http\Controllers\Settings\ShippingController@calculateShippingCost')->name('settings.shippings.api.calculate');

            // Wilayah.id API Routes (untuk origin address)
            Route::get('/api/wilayah/provinces', 'App\Http\Controllers\Settings\ShippingController@getWilayahProvinces')->name('settings.shippings.api.wilayah.provinces');
            Route::get('/api/wilayah/regencies/{provinceCode}', 'App\Http\Controllers\Settings\ShippingController@getWilayahRegencies')->name('settings.shippings.api.wilayah.regencies');
            Route::get('/api/wilayah/districts/{regencyCode}', 'App\Http\Controllers\Settings\ShippingController@getWilayahDistricts')->name('settings.shippings.api.wilayah.districts');
            Route::get('/api/wilayah/villages/{districtCode}', 'App\Http\Controllers\Settings\ShippingController@getWilayahVillages')->name('settings.shippings.api.wilayah.villages');
        });
    });
});