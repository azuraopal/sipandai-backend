<?php

use App\Http\Controllers\Api\DistrictController;
use App\Http\Controllers\Api\GoogleAuthController;
use App\Http\Controllers\Api\OpdController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::prefix('v1')->group(function () {
    Route::prefix('/auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::get('/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
        Route::get('/google/callback', [GoogleAuthController::class, 'callback']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('/district')->group(function () {
            Route::get('/', [DistrictController::class, 'index']);
            Route::post('/create', [DistrictController::class, 'store']);
            Route::get('/{id}', [DistrictController::class, 'show']);
            Route::put('/{id}', [DistrictController::class, 'update']);
            Route::delete('/{id}', [DistrictController::class, 'delete']);
        });
        Route::prefix('/opd')->group(function () {
            Route::get('/', [OpdController::class, 'index']);
            Route::post('/create', [OpdController::class, 'store']);
            Route::get('/{id}', [OpdController::class, 'show']);
            Route::put('/{id}', [OpdController::class, 'update']);
            Route::delete('/{id}', [OpdController::class, 'delete']);
        });

    });
});
