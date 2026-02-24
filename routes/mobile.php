<?php

use Illuminate\Support\Facades\Route;

// Gunakan middleware yang baru dibuat
Route::middleware(['mobile.check'])->group(function () {

    // Public Mobile Routes
    Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

    // Protected Mobile Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [\App\Http\Controllers\Api\AuthController::class, 'me']);
        Route::post('/update-fcm-token', [\App\Http\Controllers\Api\AuthController::class, 'updateFcmToken']);
        Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);

    });
});
