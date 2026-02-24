<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'User authenticated',
        'user' => $request->user(),
        'permissions' => $request->user()->getAllPermissions()->toArray(),
        'roles' => $request->user()->getRoleNames()->toArray(),
    ]);
});

Route::post('/send-notif', [\App\Http\Controllers\TestNotifController::class, 'sendDirect']);

Route::middleware([''])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard.index');


    Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])
        ->name('settings.index');
    Route::put('/settings/{key}', [\App\Http\Controllers\SettingController::class, 'update'])
        ->name('settings.update');

    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])
        ->name('users.index');
    Route::get('/users/{id}/roles', [\App\Http\Controllers\UserController::class, 'roles'])
        ->name('users.roles');
    Route::post('/users/{id}/assign-role', [\App\Http\Controllers\UserController::class, 'assignRole'])
        ->name('users.assignRole');
    Route::get('/users/{id}/permissions', [\App\Http\Controllers\UserController::class, 'permissions'])
        ->name('users.permissions');
    Route::post('/users/{id}/assign-permission', [\App\Http\Controllers\UserController::class, 'assignPermission'])
        ->name('users.assignPermission');

    Route::get('/roles', [\App\Http\Controllers\RoleController::class, 'index'])
        ->name('roles.index');
    Route::post('/roles/store', [\App\Http\Controllers\RoleController::class, 'store'])
        ->name('roles.store');
    Route::put('/roles/{id}/update', [\App\Http\Controllers\RoleController::class, 'update'])
        ->name('roles.update');
    Route::delete('/roles/{id}', [\App\Http\Controllers\RoleController::class, 'destroy'])
        ->name('roles.destroy');
    Route::get('/roles/{id}/permissions', [\App\Http\Controllers\RoleController::class, 'permissions'])
        ->name('roles.permissions');
    Route::post('/roles/{id}/assign-permission', [\App\Http\Controllers\RoleController::class, 'assignPermission'])
        ->name('roles.assignPermission');
    Route::put('/roles/{id}/permission-update', [\App\Http\Controllers\RoleController::class, 'permissionUpdate'])
        ->name('roles.permissionUpdate');
    Route::get('/roles/{id}/users', [\App\Http\Controllers\RoleController::class, 'users'])
        ->name('roles.users');
    Route::post('/roles/{id}/assign-user', [\App\Http\Controllers\RoleController::class, 'assignUser'])
        ->name('roles.assignUser');


    Route::get('/permissions', [\App\Http\Controllers\PermissionController::class, 'index'])
        ->name('permissions.index');
});
