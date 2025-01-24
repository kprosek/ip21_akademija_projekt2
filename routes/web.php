<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShowPriceController;
use App\Http\Controllers\AuthController;

Route::controller(ShowPriceController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::match(['GET', 'POST'], '/show-price', 'showPrice');
});

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('/login', 'loginProcess')->name('login-submit');
    Route::get('/logout', 'logout')->name('logout');
});
