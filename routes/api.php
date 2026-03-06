<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\PersonController;
use App\Http\Controllers\Api\AgentController;
use App\Http\Controllers\Api\TickedController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PriorityController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\StatusHistoryController;


Route::apiResource('people', PersonController::class);
Route::apiResource('agents', AgentController::class);
Route::apiResource('tickeds', TickedController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('priorities', PriorityController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('employees', EmployeeController::class);
Route::apiResource('messages', MessageController::class);
Route::apiResource('status_histories', StatusHistoryController::class);
Route::get('/test', function () {
    return "API funcionando";
});
