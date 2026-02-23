<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
  return ['Laravel' => app()->version()];
});

require __DIR__ . '/auth.php';
Route::get('/roles/{id}/permissions', [\App\Http\Controllers\RoleController::class, 'permissions'])
  ->name('roles.permissions');

Route::post('/send-notif', [\App\Http\Controllers\TestNotifController::class, 'sendDirect']);
