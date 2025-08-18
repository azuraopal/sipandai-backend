<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GoogleAuthController;

Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);
