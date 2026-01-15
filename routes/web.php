<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\Dashboard\CounterController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\ServiceController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Frontend\TouchController;
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
    Route::resource('services', ServiceController::class);
    Route::put('services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');
    Route::resource('counters', CounterController::class);
});

Route::get('/touch', [TouchController::class, 'index'])->name('touch.index');
Route::post('/touch/{service}/get-queue-number', [TouchController::class, 'getQueueNumber'])->name('touch.get-queue-number');
