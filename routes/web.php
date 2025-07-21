<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PlannedProjectHourController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDetailController;
use App\Http\Controllers\UserTerminationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Main route redirects to statistics
    Route::get('/', [StatisticsController::class, 'index'])->name('home');

    // Statistics Module Routes
    Route::prefix('statistics')->name('statistics.')->group(function () {
        Route::get('/', [StatisticsController::class, 'index'])->name('index');
        // Get compensation structure data
        Route::get('/compensation', [StatisticsController::class, 'compensationStructure'])
            ->name('compensation');
        
        // Get contractors per company statistics
        Route::get('/companies', [StatisticsController::class, 'contractorsPerCompany'])
            ->name('companies');
        
        // Get contractors seniority distribution
        Route::get('/seniority', [StatisticsController::class, 'contractorsSeniority'])
            ->name('seniority');
        
        // Get marital status distribution by gender
        Route::get('/marital-status', [StatisticsController::class, 'maritalStatusByGender'])
            ->name('marital-status');
        
        // Get contractors distribution by position
        Route::get('/positions', [StatisticsController::class, 'contractorsPerPosition'])
            ->name('positions');
        
        // Get project completion statistics
        Route::get('/project-completion', [StatisticsController::class, 'projectHourCompletion'])
            ->name('project-completion');

        // Get occupancy rate statistics
        Route::get('/occupancy', [StatisticsController::class, 'occupancyRate'])
            ->name('occupancy');
    });

    // Projects Module Routes
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('index');
        Route::get('/{id}', [ProjectController::class, 'show'])->name('show');
        
        // Project planned hours
        Route::post('/{project}/planned-hours', [PlannedProjectHourController::class,'store'])
            ->name('planned-hours.store');
            
        // Project invoices
        Route::get('/{project}/invoices/preview', [InvoiceController::class,'preview'])
            ->name('invoices.preview');
        Route::post('/{project}/invoices/send', [InvoiceController::class,'send'])
            ->name('invoices.send');
    });

    // Users Module
    Route::prefix('users')->name('user.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/{id}', [UserController::class, 'show'])->name('show');
        
        // User details
        Route::post('/{user}/details', [UserDetailController::class, 'store'])
            ->name('details.store');
        Route::put('/{user}/details', [UserDetailController::class, 'update'])
            ->name('details.update');
        
        // User termination
        Route::post('/{user}/termination', [UserTerminationController::class, 'store'])
            ->name('termination.store');
        
        // Hourly rates
        Route::post('/{user}/hourly-rates', [UserController::class, 'bulkUpdateHourlyRates'])
            ->name('rate.bulkUpdate');
    });

    // Reports Module
    Route::prefix('reports')->name('reports.')->group(function() {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/login', [ReportController::class, 'login'])->name('login');
        Route::get('/activity', [ReportController::class, 'activityIndex'])->name('activity');
        Route::get('/new-hires', [ReportController::class, 'newHires'])->name('newHires');
        Route::get('/rate-updates', [ReportController::class, 'rateUpdates'])->name('rateupdates');
        Route::get('/terminations', [ReportController::class, 'terminations'])->name('terminations');
        Route::get('/annual-hours', [ReportController::class, 'annualHours'])->name('annualhours');
        Route::get('/{type}/export', [ReportController::class, 'export'])->name('export');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])
            ->name('markAllRead');
        Route::post('/{id}/mark-read', [NotificationController::class, 'markRead'])
            ->name('markRead');
    });
});

