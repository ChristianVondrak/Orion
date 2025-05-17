<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;         // ← IMPORTA HomeController
use App\Http\Controllers\ProjectController;      // ← IMPORTA ProjectController
use App\Http\Controllers\UserController;         // ← IMPORTA UserController
use App\Http\Controllers\UserDetailController;   // ← IMPORTA UserDetailController
use App\Http\Controllers\StatisticsController;   // ← IMPORTA StatisticsController

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/',             [HomeController::class,      'index'])->name('home.index');
    Route::get('/home',         [HomeController::class,      'index'])->name('home.index');
    Route::get('/project/{id}', [ProjectController::class,   'show'])->name('project.show');
    Route::get('/user/{id}',    [UserController::class,      'show'])->name('user.show');
    Route::get('/users',        [UserController::class,      'index'])->name('user.index');

    // Nueva ruta para estadísticas
    Route::get('/statistics',   [StatisticsController::class,'index'])->name('statistics.index');

    // Rutas para detalles adicionales
    Route::post('/user/{user}/details', [UserDetailController::class, 'store'])
        ->name('user.details.store');
    Route::put('/user/{user}/details',  [UserDetailController::class, 'update'])
        ->name('user.details.update');
});

