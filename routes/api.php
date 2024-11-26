<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PersonController;
use App\Http\Controllers\Api\ChecklistController;
use App\Http\Controllers\Api\ChecklistItemController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\TodoController;
use App\Http\Controllers\Api\WeatherController;
use App\Http\Controllers\DatabaseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('api')->group(function () {
    // Resource routes
    Route::apiResource('todos', TodoController::class);
    Route::apiResource('people', PersonController::class);
    Route::apiResource('checklists', ChecklistController::class);

    // Nested routes for checklist items
    Route::post('checklists/{checklist}/items/reorder', [ChecklistItemController::class, 'reorder']);
    Route::get('checklists/{checklist}/items', [ChecklistItemController::class, 'index']);
    Route::post('checklists/{checklist}/items', [ChecklistItemController::class, 'store']);
    Route::patch('checklists/{checklist}/items/{item}', [ChecklistItemController::class, 'update']);
    Route::delete('checklists/{checklist}/items/{item}', [ChecklistItemController::class, 'destroy']);

    // Activities routes
    Route::apiResource('activities', ActivityController::class);
    Route::get('activities/person/{person}', [ActivityController::class, 'getActivitiesForPerson']);

    // Weather route
    Route::get('weather', [WeatherController::class, 'getWeather']);
});

// Database reset route (local only)
if (app()->environment('local')) {
    Route::post('reset-database', [DatabaseController::class, 'resetDatabase']);
}
