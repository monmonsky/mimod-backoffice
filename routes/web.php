<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.signin');
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');



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

Route::group(['prefix' => 'settings'], function () {
    // General Settings Routes
    Route::get('/store-info', 'App\Http\Controllers\Settings\GeneralController@storeInfo')->name('settings.store-info');
    Route::post('/store-info', 'App\Http\Controllers\Settings\GeneralController@updateStoreInfo')->name('settings.store-info.update');
    Route::post('/store-info/upload-logo', 'App\Http\Controllers\Settings\GeneralController@uploadStoreLogo')->name('settings.store-info.upload-logo');
    Route::delete('/store-info/delete-logo', 'App\Http\Controllers\Settings\GeneralController@deleteStoreLogo')->name('settings.store-info.delete-logo');

    Route::get('/email-settings', 'App\Http\Controllers\Settings\GeneralController@emailSettings')->name('settings.email-settings');
    Route::post('/email-settings', 'App\Http\Controllers\Settings\GeneralController@updateEmailSettings')->name('settings.email-settings.update');
    Route::post('/email-settings/test', 'App\Http\Controllers\Settings\GeneralController@testEmailConnection')->name('settings.email-settings.test');

    Route::get('/seo-meta', 'App\Http\Controllers\Settings\GeneralController@seoMeta')->name('settings.seo-meta');
    Route::post('/seo-meta', 'App\Http\Controllers\Settings\GeneralController@updateSeoMeta')->name('settings.seo-meta.update');

    Route::get('/system-config', 'App\Http\Controllers\Settings\GeneralController@systemConfig')->name('settings.system-config');
    Route::post('/system-config', 'App\Http\Controllers\Settings\GeneralController@updateSystemConfig')->name('settings.system-config.update');

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