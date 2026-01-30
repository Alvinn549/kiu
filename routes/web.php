<?php

use App\Http\Controllers\AjaxController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\CounterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceRouteController;
use App\Http\Controllers\TouchController;
use App\Http\Controllers\UserController;
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

    Route::put('services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])
        ->name('services.toggle-status');

    Route::resource('services.routes', ServiceRouteController::class)
        ->only(['index', 'store', 'destroy']);

    Route::resource('counters', CounterController::class);
});

Route::group(['prefix' => 'ajax'], function () {

    Route::get('/touch', [AjaxController::class, 'touch'])
        ->name('ajax.touch');

    Route::post('/touch/{service}/get-queue-number', [TouchController::class, 'getQueueNumber'])
        ->name('ajax.touch.get-queue-number');

    Route::get('/dashboard/staff/current-queue', [AjaxController::class, 'getCurrentQueue'])
        ->name('ajax.dashboard.staff.current-queue');

    Route::put('/counters/{counter}/set-status', [AjaxController::class, 'setStatusCounter'])
        ->name('ajax.set-status-counter');

    Route::put('/queues/{queue}/call', [AjaxController::class, 'callQueue'])
        ->name('ajax.queues.call');

    Route::put('/queues/{queue}/direct-call', [AjaxController::class, 'directCallQueue'])
        ->name('ajax.queues.direct-call');

    Route::put('/queues/{queue}/skip', [AjaxController::class, 'skipQueue'])
        ->name('ajax.queues.skip');

    Route::put('/queues/{queue}/complete', [AjaxController::class, 'completeQueue'])
        ->name('ajax.queues.complete');

    Route::put('/services/{service}/toggle-status', [AjaxController::class, 'toggleStatusService'])
        ->name('ajax.toggle-status-service');

    Route::get('/display/data', [AjaxController::class, 'displayData'])
        ->name('ajax.display.data');
});

Route::get('/touch', [TouchController::class, 'index'])->name('touch.index');

Route::get('/display', [DisplayController::class, 'index'])->name('display.index');
