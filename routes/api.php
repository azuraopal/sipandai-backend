<?php

use App\Http\Controllers\Api\DistrictController;
use App\Http\Controllers\Api\GoogleAuthController;
use App\Http\Controllers\Api\OpdController;
use App\Http\Controllers\Api\VillageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::prefix('v1')->group(function () {
    Route::prefix('/auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::get('/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
        Route::get('/google/callback', [GoogleAuthController::class, 'callback']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('/auth')->group(function () {
            Route::post('/change-password', [AuthController::class, 'changePassword']);
        });
        Route::prefix('/district')->group(function () {
            Route::get('/', [DistrictController::class, 'index']);
            Route::post('/', [DistrictController::class, 'store']);
            Route::get('/{id}', [DistrictController::class, 'show']);
            Route::put('/{id}', [DistrictController::class, 'update']);
            Route::delete('/{id}', [DistrictController::class, 'destroy']);
        });

        Route::prefix('/village')->group(function () {
            Route::put('/{id}', [VillageController::class, 'update']);
            Route::delete('/{id}', [VillageController::class, 'destroy']);
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
