<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DistrictController;
use App\Http\Controllers\Api\GoogleAuthController;
use App\Http\Controllers\Api\OpdController;
use App\Http\Controllers\Api\ReportCategoryController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ReportTypeController;
use App\Http\Controllers\Api\ReportUserAssignmentController;
use App\Http\Controllers\Api\VillageController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('/report')->group(function () {
        Route::post('/', [ReportController::class, 'store']);
        Route::get('/type', [ReportTypeController::class, 'index']);
        Route::post('/request-otp', [ReportController::class, 'requestOtp']);
        Route::post('/verify-otp', [ReportController::class, 'verifyOtp']);
        Route::post('/resend-otp', [ReportController::class, 'resendOtp']);
    });

    Route::prefix('/auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
        Route::post('/register', [AuthController::class, 'register']);
        Route::get('/google', [GoogleAuthController::class, 'redirect']);
        Route::get('/google/callback', [GoogleAuthController::class, 'callback']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
        Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
        Route::post('/resend-verification', [AuthController::class, 'resendVerificationCode']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('/auth')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::put('/me', [AuthController::class, 'updateProfile']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/change-password', [AuthController::class, 'changePassword']);
        });

        Route::prefix('/districts')->group(function () {
            Route::get('/', [DistrictController::class, 'index']);
            Route::post('/', [DistrictController::class, 'store']);
            Route::get('/{code}', [DistrictController::class, 'show']);
            Route::put('/{code}', [DistrictController::class, 'update']);
            Route::delete('/{code}', [DistrictController::class, 'destroy']);

            Route::get('/{code}/villages', [VillageController::class, 'index']);
            Route::post('/{code}/villages', [VillageController::class, 'store']);
            Route::put('/{code}/villages/{village_code}', [VillageController::class, 'update']);
            Route::delete('/{code}/villages/{village_code}', [VillageController::class, 'destroy']);
        });


        Route::prefix('/opd')->group(function () {
            Route::get('/', [OpdController::class, 'index']);
            Route::post('/create', [OpdController::class, 'store']);
            Route::get('/{id}', [OpdController::class, 'show']);
            Route::put('/{id}', [OpdController::class, 'update']);
            Route::delete('/{id}', [OpdController::class, 'delete']);
        });

        Route::prefix('/report')->group(function () {
            Route::get('/', [ReportController::class, 'index']);
            Route::get('/{id}', [ReportController::class, 'show']);
            Route::get('{id}/attachments', [ReportController::class, 'attachments']);
            Route::get('{id}/status-history', [ReportController::class, 'statusHistories']);

            Route::post('/submit', [ReportUserAssignmentController::class, 'handle']);

            Route::prefix('/types')->group(function () {
                Route::post('/', [ReportTypeController::class, 'store']);
                Route::put('/{id}', [ReportTypeController::class, 'update']);
                Route::delete('/{id}', [ReportTypeController::class, 'destroy']);

                Route::post('/{id}/categories', [ReportCategoryController::class, 'store']);
                Route::put('/{id}/categories/{category}', [ReportCategoryController::class, 'update']);
                Route::delete('/{id}/categories/{category}', [ReportCategoryController::class, 'destroy']);
            });
        });
    });
});