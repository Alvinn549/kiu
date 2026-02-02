<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\CounterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FetchController;
use App\Http\Controllers\MainDisplayController;
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
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::resource('users', UserController::class);

    Route::resource('services', ServiceController::class);

    Route::put('services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])
        ->name('services.toggle-status');

    Route::resource('services.routes', ServiceRouteController::class)
        ->only(['index', 'store', 'destroy']);

    Route::resource('counters', CounterController::class);
});

Route::get('/touch', [TouchController::class, 'index'])
    ->name('touch.index');

Route::get('/display', [MainDisplayController::class, 'index'])
    ->name('display.index');

Route::group(['prefix' => 'fetch'], function () {

    Route::get('/get-csrf-token', [FetchController::class, 'getCsrfToken'])
        ->name('fetch.get-csrf-token');

    Route::get('/get-services', [TouchController::class, 'getServices'])
        ->name('fetch.get-services');

    Route::post('/get-ticket/{service}', [TouchController::class, 'getTicket'])
        ->name('fetch.get-ticket');

    Route::get('/display-data', [MainDisplayController::class, 'getDisplayData'])
        ->name('fetch.display.data');

    Route::get('/counter-dashboard', [FetchController::class, 'counterDashboard'])
        ->name('fetch.counter-dashboard');

    // OLD ROUTES FOR DASHBOARD
    Route::put('/counters/{counter}/set-status', [FetchController::class, 'setStatusCounter'])
        ->name('fetch.set-status-counter');

    Route::put('/queues/{queue}/call', [FetchController::class, 'callQueue'])
        ->name('fetch.queues.call');

    Route::put('/queues/{queue}/direct-call', [FetchController::class, 'directCallQueue'])
        ->name('fetch.queues.direct-call');

    Route::put('/queues/{queue}/skip', [FetchController::class, 'skipQueue'])
        ->name('fetch.queues.skip');

    Route::put('/queues/{queue}/complete', [FetchController::class, 'completeQueue'])
        ->name('fetch.queues.complete');

    Route::put('/services/{service}/toggle-status', [FetchController::class, 'toggleStatusService'])
        ->name('fetch.toggle-status-service');

    Route::get('/display/data', [FetchController::class, 'displayData'])
        ->name('fetch.display.data');
});
