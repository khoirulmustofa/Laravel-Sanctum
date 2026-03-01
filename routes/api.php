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
    // =========== DASHBOARD ===========
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard.index');

    // =========== SETTING ===========
    Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])
        ->name('settings.index');
    Route::put('/settings/{key}', [\App\Http\Controllers\SettingController::class, 'update'])
        ->name('settings.update');

    // =========== USER ===========
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

    // =========== ROLE ===========
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
    Route::get('/roles/{id}/users', [\App\Http\Controllers\RoleController::class, 'users'])
        ->name('roles.users');
    Route::post('/roles/{id}/assign-user', [\App\Http\Controllers\RoleController::class, 'assignUser'])
        ->name('roles.assignUser');
    Route::post('/roles/permission-seeder', [\App\Http\Controllers\RoleController::class, 'permissionSeeder'])
        ->name('roles.permissionSeeder');

    // =========== SEKOLAH ===========
    Route::get('/sekolah', [\App\Http\Controllers\SekolahController::class, 'index'])
        ->name('sekolah.index');
    Route::post('/sekolah/store', [\App\Http\Controllers\SekolahController::class, 'store'])
        ->name('sekolah.store');
    Route::put('/sekolah/{id}/update', [\App\Http\Controllers\SekolahController::class, 'update'])
        ->name('sekolah.update');
    Route::delete('/sekolah/{id}', [\App\Http\Controllers\SekolahController::class, 'destroy'])
        ->name('sekolah.destroy');

    // =========== TAHUN AJARAN ===========
    Route::get('/tahun-ajaran', [\App\Http\Controllers\TahunAjaranController::class, 'index'])
        ->name('tahun-ajaran.index');
    Route::post('/tahun-ajaran/store', [\App\Http\Controllers\TahunAjaranController::class, 'store'])
        ->name('tahun-ajaran.store');
    Route::put('/tahun-ajaran/{id}/update', [\App\Http\Controllers\TahunAjaranController::class, 'update'])
        ->name('tahun-ajaran.update');
    Route::delete('/tahun-ajaran/{id}', [\App\Http\Controllers\TahunAjaranController::class, 'destroy'])
        ->name('tahun-ajaran.destroy');

    // =========== KELAS ===========
    Route::get('/kelas', [\App\Http\Controllers\KelasController::class, 'index'])
        ->name('kelas.index');
    Route::post('/kelas/store', [\App\Http\Controllers\KelasController::class, 'store'])
        ->name('kelas.store');
    Route::put('/kelas/{id}/update', [\App\Http\Controllers\KelasController::class, 'update'])
        ->name('kelas.update');
    Route::delete('/kelas/{id}', [\App\Http\Controllers\KelasController::class, 'destroy'])
        ->name('kelas.destroy');

    // =========== SISWA ===========
    Route::get('/siswa', [\App\Http\Controllers\SiswaController::class, 'index'])
        ->name('siswa.index');
    Route::post('/siswa/store', [\App\Http\Controllers\SiswaController::class, 'store'])
        ->name('siswa.store');
    Route::put('/siswa/{id}/update', [\App\Http\Controllers\SiswaController::class, 'update'])
        ->name('siswa.update');
    Route::delete('/siswa/{id}', [\App\Http\Controllers\SiswaController::class, 'destroy'])
        ->name('siswa.destroy');
    Route::get('/siswa/sekolah', [\App\Http\Controllers\SiswaController::class, 'sekolah'])
        ->name('siswa.sekolah');
});
