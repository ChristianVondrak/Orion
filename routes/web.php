<?php

use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDetailController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home.index');
    Route::get('/home', [HomeController::class, 'index'])->name('home.index');
    Route::get('/project/{id}', [ProjectController::class, 'show'])->name('project.show');
    Route::get('/user/{id}',[UserController::class, 'show'])->name('user.show');
    Route::get('/users',[UserController::class, 'index'])->name('user.index');

    // Routes to create/update additional details
    Route::post('/user/{user}/details',  [UserDetailController::class, 'store'])
        ->name('user.details.store');
    Route::put('/user/{user}/details',   [UserDetailController::class, 'update'])
        ->name('user.details.update');

    // Módulo de reportes
    Route::prefix('reports')->name('reports.')->group(function() {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/login', [ReportController::class, 'loginReport'])->name('login');
        Route::get('/activity', [ReportController::class, 'activityIndex'])->name('activity');
        Route::get('/new-hires', [ReportController::class, 'newHires'])->name('newHires');
        Route::get('/rate-updates', [ReportController::class, 'rateUpdates'])->name('rateupdates');
        // exportación
        Route::get('/{type}/export', [ReportController::class, 'export'])->name('export');
    });

    // Hourly Rate Update
    Route::post(
        '/user/{user}/hourly-rates',
        [UserController::class, 'bulkUpdateHourlyRates']
    )->name('user.rate.bulkUpdate');
});
