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

Route::group(['prefix' => 'role'], function () {
    Route::get('/', function () {
        return view('pages.role.index');
    })->name('role.index');

    Route::get('/create', function () {
        return view('pages.role.create');
    })->name('role.create');

    Route::get('/module', function () {
        return view('pages.role.module-access');
    })->name('role.module');
});

Route::group(['prefix' => 'permission'], function () {
    Route::get('/', function () {
        return view('pages.permission.index');
    })->name('permission.index');
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
    Route::prefix('general')->group(function () {
        Route::get('/store', 'App\Http\Controllers\Settings\GeneralController@storeInfo')->name('settings.general.store');
        Route::post('/store', 'App\Http\Controllers\Settings\GeneralController@updateStoreInfo')->name('settings.general.store.update');
        Route::post('/store/upload-logo', 'App\Http\Controllers\Settings\GeneralController@uploadStoreLogo')->name('settings.general.store.upload-logo');
        Route::delete('/store/delete-logo', 'App\Http\Controllers\Settings\GeneralController@deleteStoreLogo')->name('settings.general.store.delete-logo');

        Route::get('/email', 'App\Http\Controllers\Settings\GeneralController@emailSettings')->name('settings.general.email');
        Route::post('/email', 'App\Http\Controllers\Settings\GeneralController@updateEmailSettings')->name('settings.general.email.update');
        Route::post('/email/test', 'App\Http\Controllers\Settings\GeneralController@testEmailConnection')->name('settings.general.email.test');

        Route::get('/seo', 'App\Http\Controllers\Settings\GeneralController@seoMeta')->name('settings.general.seo');
        Route::post('/seo', 'App\Http\Controllers\Settings\GeneralController@updateSeoMeta')->name('settings.general.seo.update');

        Route::get('/system', 'App\Http\Controllers\Settings\GeneralController@systemConfig')->name('settings.general.system');
        Route::post('/system', 'App\Http\Controllers\Settings\GeneralController@updateSystemConfig')->name('settings.general.system.update');
    });

    // Payment Settings Routes
    Route::prefix('payment')->group(function () {
        Route::get('/methods', 'App\Http\Controllers\Settings\PaymentController@paymentMethods')->name('settings.payment.methods');

        Route::get('/midtrans-config', 'App\Http\Controllers\Settings\PaymentController@midtransConfig')->name('settings.payment.midtrans-config');
        Route::post('/midtrans-config/api', 'App\Http\Controllers\Settings\PaymentController@updateMidtransApi')->name('settings.payment.midtrans-config.api.update');
        Route::post('/midtrans-config/methods', 'App\Http\Controllers\Settings\PaymentController@updateMidtransPaymentMethods')->name('settings.payment.midtrans-config.methods.update');
        Route::post('/midtrans-config/transaction', 'App\Http\Controllers\Settings\PaymentController@updateMidtransTransaction')->name('settings.payment.midtrans-config.transaction.update');
        Route::post('/midtrans-config/test', 'App\Http\Controllers\Settings\PaymentController@testMidtransConnection')->name('settings.payment.midtrans-config.test');
        Route::post('/midtrans-config/sync', 'App\Http\Controllers\Settings\PaymentController@syncMidtransPaymentMethods')->name('settings.payment.midtrans-config.sync');

        Route::get('/tax-settings', 'App\Http\Controllers\Settings\PaymentController@taxSettings')->name('settings.payment.tax-settings');
        Route::post('/tax-settings', 'App\Http\Controllers\Settings\PaymentController@updateTaxSettings')->name('settings.payment.tax-settings.update');
    });

    // Shipping Settings Routes
    Route::prefix('shipping')->group(function () {
        Route::get('/methods', 'App\Http\Controllers\Settings\ShippingController@shippingMethods')->name('settings.shipping.methods');

        Route::get('/rajaongkir-config', 'App\Http\Controllers\Settings\ShippingController@rajaongkirConfig')->name('settings.shipping.rajaongkir-config');
        Route::post('/rajaongkir-config', 'App\Http\Controllers\Settings\ShippingController@updateRajaongkirConfig')->name('settings.shipping.rajaongkir-config.update');
        Route::post('/rajaongkir-config/test', 'App\Http\Controllers\Settings\ShippingController@testRajaongkirConnection')->name('settings.shipping.rajaongkir-config.test');
        Route::post('/rajaongkir-config/sync-locations', 'App\Http\Controllers\Settings\ShippingController@syncLocations')->name('settings.shipping.rajaongkir-config.sync-locations');

        Route::get('/origin-address', 'App\Http\Controllers\Settings\ShippingController@originAddress')->name('settings.shipping.origin-address');
        Route::post('/origin-address', 'App\Http\Controllers\Settings\ShippingController@updateOriginAddress')->name('settings.shipping.origin-address.update');

        // Shipping API Routes (untuk dropdown & calculations)
        Route::get('/api/provinces', 'App\Http\Controllers\Settings\ShippingController@getProvinces')->name('settings.shipping.api.provinces');
        Route::get('/api/cities', 'App\Http\Controllers\Settings\ShippingController@getAllCities')->name('settings.shipping.api.all-cities');
        Route::get('/api/cities/{provinceId}', 'App\Http\Controllers\Settings\ShippingController@getCities')->name('settings.shipping.api.cities');
        Route::get('/api/districts/{cityId}', 'App\Http\Controllers\Settings\ShippingController@getDistricts')->name('settings.shipping.api.districts');
        Route::post('/api/calculate-shipping', 'App\Http\Controllers\Settings\ShippingController@calculateShippingCost')->name('settings.shipping.api.calculate');

        // Wilayah.id API Routes (untuk origin address)
        Route::get('/api/wilayah/provinces', 'App\Http\Controllers\Settings\ShippingController@getWilayahProvinces')->name('settings.shipping.api.wilayah.provinces');
        Route::get('/api/wilayah/regencies/{provinceCode}', 'App\Http\Controllers\Settings\ShippingController@getWilayahRegencies')->name('settings.shipping.api.wilayah.regencies');
        Route::get('/api/wilayah/districts/{regencyCode}', 'App\Http\Controllers\Settings\ShippingController@getWilayahDistricts')->name('settings.shipping.api.wilayah.districts');
        Route::get('/api/wilayah/villages/{districtCode}', 'App\Http\Controllers\Settings\ShippingController@getWilayahVillages')->name('settings.shipping.api.wilayah.villages');
    });
});
