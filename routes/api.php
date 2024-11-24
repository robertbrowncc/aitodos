<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TodoController;
use App\Http\Controllers\Api\PersonController;
use App\Http\Controllers\Api\ActivityController;
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

Route::apiResource('todos', TodoController::class);
Route::apiResource('people', PersonController::class);
Route::apiResource('activities', ActivityController::class);

// Konami code endpoint
Route::post('reset-database', [DatabaseController::class, 'resetDatabase']);
