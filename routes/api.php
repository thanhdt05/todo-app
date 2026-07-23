<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'show']);

    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index']);
        Route::get('{id}', [TaskController::class, 'show']);
        Route::post('/', [TaskController::class, 'store']);
        Route::put('{id}', [TaskController::class, 'update']);
        Route::put('{id}/restore', [TaskController::class, 'restore']);
        Route::put('{id}/complete', [TaskController::class, 'complete']);
        Route::delete('{id}', [TaskController::class, 'delete']);
        Route::delete('{id}/force', [TaskController::class, 'destroy']);
    });
});