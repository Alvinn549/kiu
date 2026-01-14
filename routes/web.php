<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthenticationController::class, 'showLoginForm'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthenticationController::class, 'authenticate'])
    ->middleware('guest')
    ->name('login.submit');

Route::post('/logout', [AuthenticationController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::group(['prefix' => 'dashboard', 'middleware' => 'auth'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class);
});
