<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PlannedProjectHourController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDetailController;
use App\Http\Controllers\UserTerminationController;
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

    // Route to update user termination
    Route::post('/user/{user}/termination', [UserTerminationController::class, 'store'])
        ->name('user.termination.store');

    // Módulo de reportes
    Route::prefix('reports')->name('reports.')->group(function() {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/login', [ReportController::class, 'loginReport'])->name('login');
        Route::get('/activity', [ReportController::class, 'activityIndex'])->name('activity');
        Route::get('/new-hires', [ReportController::class, 'newHires'])->name('newHires');
        Route::get('/rate-updates', [ReportController::class, 'rateUpdates'])->name('rateupdates');
        Route::get('/terminations', [ReportController::class, 'terminations'])->name('terminations');
        // exportación
        Route::get('/{type}/export', [ReportController::class, 'export'])->name('export');
    });

    // Alertas
    Route::post('projects/{project}/planned-hours', [PlannedProjectHourController::class,'store'])
        ->name('projects.planned-hours.store');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllRead'])
        ->name('notifications.markAllRead');
    Route::post('notifications/{id}/mark-read', [NotificationController::class, 'markRead'])
        ->name('notifications.markRead');

    // Invoices
    // Vista previa del corte
    Route::get('project/{project}/invoices/preview', [InvoiceController::class,'preview'])
        ->name('project.invoices.preview');
    // Envío final de las facturas
    Route::post('project/{project}/invoices/send', [InvoiceController::class,'send'])
        ->name('project.invoices.send');

    // Hourly Rate Update
    Route::post(
        '/user/{user}/hourly-rates',
        [UserController::class, 'bulkUpdateHourlyRates']
    )->name('user.rate.bulkUpdate');
});
