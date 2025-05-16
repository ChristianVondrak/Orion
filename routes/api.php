<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatisticsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and assigned to the "api"
| middleware group. Enjoy building your API!
|
*/

// Default user endpoint (you can leave this as is)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Statistics endpoints
Route::middleware(['auth:sanctum'])->prefix('statistics')->group(function () {
    Route::get('/compensation', [StatisticsController::class, 'compensationStructure']);
    Route::get('/companies', [StatisticsController::class, 'contractorsPerCompany']);
    Route::get('/seniority', [StatisticsController::class, 'contractorsSeniority']);
    Route::get('/marital-status', [StatisticsController::class, 'maritalStatusByGender']);
    Route::get('/departments', [StatisticsController::class, 'contractorsPerDepartment']);
    Route::get('/project-hours', [StatisticsController::class, 'projectHourCompletion']);
});

