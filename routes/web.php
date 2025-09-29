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