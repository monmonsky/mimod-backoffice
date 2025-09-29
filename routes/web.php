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