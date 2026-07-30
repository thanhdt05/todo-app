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
        Route::get('trashed', [TaskController::class, 'getAllTrashedTasks']);
        Route::put('bulk-restore', [TaskController::class, 'bulkRestore']);
        Route::post('/', [TaskController::class, 'store']);

        Route::get('{id}', [TaskController::class, 'show'])->whereNumber('id');
        Route::match(['put', 'patch'], '{id}', [TaskController::class, 'update'])->whereNumber('id');
        Route::put('{id}/restore', [TaskController::class, 'restore'])->whereNumber('id');
        Route::put('{id}/complete', [TaskController::class, 'complete'])->whereNumber('id');
        Route::delete('{id}', [TaskController::class, 'delete'])->whereNumber('id');
        Route::delete('{id}/force', [TaskController::class, 'destroy'])->whereNumber('id');
    });
});
