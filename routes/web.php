<?php

use App\Http\Controllers\StatisticsController; // <- agrega esto arriba

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home.index');
    Route::get('/home', [HomeController::class, 'index'])->name('home.index');
    Route::get('/project/{id}', [ProjectController::class, 'show'])->name('project.show');
    Route::get('/user/{id}', [UserController::class, 'show'])->name('user.show');
    Route::get('/users', [UserController::class, 'index'])->name('user.index');

    // ✅ Nueva ruta para estadísticas
    Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');

    // Routes to create/update additional details
    Route::post('/user/{user}/details', [UserDetailController::class, 'store'])->name('user.details.store');
    Route::put('/user/{user}/details', [UserDetailController::class, 'update'])->name('user.details.update');
});

