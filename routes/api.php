<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::middleware('throttle:10,1')->prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    Route::post('social/exchange', [SocialAuthController::class, 'exchange']);
});

// Protected routes
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'show']);

    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index']);
        Route::get('/trashed', [TaskController::class, 'getAllTrashedTasks']);
        Route::get('{id}', [TaskController::class, 'show']);
        Route::post('/', [TaskController::class, 'store']);
        Route::match(['put', 'patch'], '{id}', [TaskController::class, 'update']);
        Route::put('{id}/restore', [TaskController::class, 'restore']);
        Route::put('{id}/complete', [TaskController::class, 'complete']);
        Route::delete('{id}', [TaskController::class, 'delete']);
        Route::delete('{id}/force', [TaskController::class, 'destroy']);
    });
});
