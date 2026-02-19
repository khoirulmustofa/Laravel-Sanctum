<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'User authenticated',
        'user' => $request->user(),
        'permissions' => $request->user()->getAllPermissions()->pluck('name')->toArray(),
        'roles' => $request->user()->getRoleNames()->pluck('name')->toArray(),
    ]);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])
        ->name('settings.index');
    Route::put('/settings/{key}', [\App\Http\Controllers\SettingController::class, 'update'])
        ->name('settings.update');
});
