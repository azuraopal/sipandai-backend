<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Contoh rute yang terproteksi
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });