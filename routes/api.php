<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->name('auth.')->controller(AuthController::class)->group(function () {

    Route::post('/register', 'register')->name('register');
    Route::post('/login', 'login')->name('login');

});

Route::get('urls/{url:short_code}/visit', [UrlController::class, 'visit'])->name('urls.visit');

Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('urls', UrlController::class)->except(['show']);

});
