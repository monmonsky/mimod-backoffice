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
    Route::get('/store-info', function () {
        return view('pages.settings.store-info');
    })->name('settings.store-info');

    Route::get('/email-settings', function () {
        return view('pages.settings.email-settings');
    })->name('settings.email-settings');

    Route::get('/seo-meta', function () {
        return view('pages.settings.seo-meta');
    })->name('settings.seo-meta');

    Route::get('/system-config', function () {
        return view('pages.settings.system-config');
    })->name('settings.system-config');

    // Payment Routes
    Route::get('/payment-methods', function () {
        return view('pages.settings.payment.payment-methods');
    })->name('settings.payment-methods');

    Route::get('/midtrans-config', function () {
        return view('pages.settings.payment.midtrans-config');
    })->name('settings.midtrans-config');

    Route::get('/tax-settings', function () {
        return view('pages.settings.payment.tax-settings');
    })->name('settings.tax-settings');

    // Shipping Routes
    Route::get('/shipping-methods', function () {
        return view('pages.settings.shipping.shipping-methods');
    })->name('settings.shipping.shipping-methods');

    Route::get('/rajaongkir-config', function () {
        return view('pages.settings.shipping.rajaongkir-config');
    })->name('settings.shipping.rajaongkir-config');

    Route::get('/origin-address', function () {
        return view('pages.settings.shipping.origin-address');
    })->name('settings.shipping.origin-address');
});