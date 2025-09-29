<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.signin');
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
