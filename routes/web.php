<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest:web')->group(function () {
    Route::view('/login', 'login')->name('login');

    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:20,1')
        ->name('login.store');
});

Route::middleware('auth:web')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    Route::post('/logout', [LoginController::class, 'destroy'])
        ->name('logout');

    Route::prefix('users')
        ->name('users.')
        ->group(function () {
            Route::get('/list', [UserController::class, 'list'])
                ->middleware('form.action:300,فهرست کاربران')
                ->name('list');

            Route::get('/create', [UserController::class, 'create'])
                ->middleware('form.action:332,نمایش فرم ایجاد کاربر')
                ->name('create');

            Route::post('/', [UserController::class, 'store'])
                ->middleware('form.action:333,ثبت کاربر')
                ->name('store');
        });
    Route::get('/dashboard', DashboardController::class)
        ->middleware('auth:web')
        ->name('dashboard');
});