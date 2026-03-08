<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Public Mobile Routes
Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);


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

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/me', [\App\Http\Controllers\Api\AuthController::class, 'me']);
    Route::post('/update-fcm-token', [\App\Http\Controllers\Api\AuthController::class, 'updateFcmToken']);
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);

    // =========== PROFILE ===========
    Route::post('/profile/update', [\App\Http\Controllers\ProfileController::class, 'update']);
    Route::post('/profile/update-photo', [\App\Http\Controllers\ProfileController::class, 'updatePhoto']);


    // =========== DASHBOARD ===========
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard.index');

    // =========== SETTING ===========
    Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])
        ->name('settings.index');
    Route::put('/settings/{key}', [\App\Http\Controllers\SettingController::class, 'update'])
        ->name('settings.update');

    // =========== USER ===========
    Route::get('master/users', [\App\Http\Controllers\Master\UserController::class, 'index'])
        ->name('master.users.index');
    Route::get('master/users/{id}/roles', [\App\Http\Controllers\Master\UserController::class, 'roles'])
        ->name('master.users.roles');
    Route::post('master/users/{id}/assign-role', [\App\Http\Controllers\Master\UserController::class, 'assignRole'])
        ->name('master.users.assignRole');
    Route::get('master/users/{id}/permissions', [\App\Http\Controllers\Master\UserController::class, 'permissions'])
        ->name('master.users.permissions');
    Route::post('master/users/{id}/assign-permission', [\App\Http\Controllers\Master\UserController::class, 'assignPermission'])
        ->name('master.users.assignPermission');

    // =========== ROLE ===========
    Route::get('master/roles', [\App\Http\Controllers\Master\RoleController::class, 'index'])
        ->name('master.roles.index');
    Route::post('master/roles/store', [\App\Http\Controllers\Master\RoleController::class, 'store'])
        ->name('master.roles.store');
    Route::put('master/roles/{id}/update', [\App\Http\Controllers\Master\RoleController::class, 'update'])
        ->name('master.roles.update');
    Route::delete('master/roles/{id}', [\App\Http\Controllers\Master\RoleController::class, 'destroy'])
        ->name('master.roles.destroy');
    Route::get('master/roles/{id}/permissions', [\App\Http\Controllers\Master\RoleController::class, 'permissions'])
        ->name('master.roles.permissions');
    Route::post('master/roles/{id}/assign-permission', [\App\Http\Controllers\Master\RoleController::class, 'assignPermission'])
        ->name('master.roles.assignPermission');
    Route::get('master/roles/{id}/users', [\App\Http\Controllers\Master\RoleController::class, 'users'])
        ->name('master.roles.users');
    Route::post('master/roles/{id}/assign-user', [\App\Http\Controllers\Master\RoleController::class, 'assignUser'])
        ->name('master.roles.assignUser');
    Route::post('master/roles/permission-seeder', [\App\Http\Controllers\Master\RoleController::class, 'permissionSeeder'])
        ->name('master.roles.permissionSeeder');

    // =========== SEKOLAH ===========
    Route::get('master/sekolah', [\App\Http\Controllers\Master\SekolahController::class, 'index'])
        ->name('master.sekolah.index');
    Route::post('master/sekolah/store', [\App\Http\Controllers\Master\SekolahController::class, 'store'])
        ->name('master.sekolah.store');
    Route::put('master/sekolah/{id}/update', [\App\Http\Controllers\Master\SekolahController::class, 'update'])
        ->name('master.sekolah.update');
    Route::delete('master/sekolah/{id}', [\App\Http\Controllers\Master\SekolahController::class, 'destroy'])
        ->name('master.sekolah.destroy');

    // =========== TAHUN AJARAN ===========
    Route::get('master/tahun-ajaran', [\App\Http\Controllers\Master\TahunAjaranController::class, 'index'])
        ->name('master.tahun-ajaran.index');
    Route::post('master/tahun-ajaran/store', [\App\Http\Controllers\Master\TahunAjaranController::class, 'store'])
        ->name('master.tahun-ajaran.store');
    Route::put('master/tahun-ajaran/{id}/update', [\App\Http\Controllers\Master\TahunAjaranController::class, 'update'])
        ->name('master.tahun-ajaran.update');
    Route::delete('master/tahun-ajaran/{id}', [\App\Http\Controllers\Master\TahunAjaranController::class, 'destroy'])
        ->name('master.tahun-ajaran.destroy');
    Route::get('master/tahun-ajaran/active', [\App\Http\Controllers\Master\TahunAjaranController::class, 'tahunAjaranActive'])
        ->name('master.tahun-ajaran.active');

    // =========== KELAS ===========
    Route::get('master/kelas', [\App\Http\Controllers\Master\KelasController::class, 'index'])
        ->name('master.kelas.index');
    Route::post('master/kelas/store', [\App\Http\Controllers\Master\KelasController::class, 'store'])
        ->name('master.kelas.store');
    Route::put('master/kelas/{id}/update', [\App\Http\Controllers\Master\KelasController::class, 'update'])
        ->name('master.kelas.update');
    Route::delete('master/kelas/{id}', [\App\Http\Controllers\Master\KelasController::class, 'destroy'])
        ->name('master.kelas.destroy');
    Route::get('master/kelas/{id}/siswa', [\App\Http\Controllers\Master\KelasController::class, 'getSiswa'])
        ->name('master.kelas.siswa');

    // =========== SISWA ===========
    Route::get('master/siswa', [\App\Http\Controllers\Master\SiswaController::class, 'index'])
        ->name('master.siswa.index');
    Route::post('master/siswa/store', [\App\Http\Controllers\Master\SiswaController::class, 'store'])
        ->name('master.siswa.store');
    Route::put('master/siswa/{id}/update', [\App\Http\Controllers\Master\SiswaController::class, 'update'])
        ->name('master.siswa.update');
    Route::delete('master/siswa/{id}', [\App\Http\Controllers\Master\SiswaController::class, 'destroy'])
        ->name('master.siswa.destroy');
    Route::get('master/siswa/sekolah', [\App\Http\Controllers\Master\SiswaController::class, 'sekolah'])
        ->name('master.siswa.sekolah');

    // =========== SEMESTER ===========
    Route::get('master/semesters', [\App\Http\Controllers\Master\SemesterController::class, 'index'])
        ->name('master.semesters.index');

    // =========== DATA KELAS ===========
    Route::get('/data/kelas', [\App\Http\Controllers\Data\KelasController::class, 'index'])
        ->name('data.kelas.index');
    Route::get('/data/kelas/{id}/siswa', [\App\Http\Controllers\Data\KelasController::class, 'siswa'])
        ->name('data.kelas.siswa');
    Route::get('/data/kelas/options', [\App\Http\Controllers\Data\KelasController::class, 'options'])
        ->name('data.kelas.options');
    Route::get('/data/kelas/siswa-plotting', [\App\Http\Controllers\Data\KelasController::class, 'indexPlotting'])
        ->name('data.kelas.indexPlotting');
    Route::post('/data/kelas/transfer', [\App\Http\Controllers\Data\KelasController::class, 'transfer'])
        ->name('data.kelas.transfer');

    // =========== MASTER ORANG TUA ===========
    Route::get('master/orang-tua', [\App\Http\Controllers\Master\OrangTuaController::class, 'index'])
        ->name('master.orang_tua.index');
    Route::get('master/orang-tua/search', [\App\Http\Controllers\Master\OrangTuaController::class, 'search'])
        ->name('master.orang_tua.search');
    Route::post('master/orang-tua/store', [\App\Http\Controllers\Master\OrangTuaController::class, 'store'])
        ->name('master.orang_tua.store');
    Route::put('master/orang-tua/{id}/update', [\App\Http\Controllers\Master\OrangTuaController::class, 'update'])
        ->name('master.orang_tua.update');
    Route::delete('master/orang-tua/{id}', [\App\Http\Controllers\Master\OrangTuaController::class, 'destroy'])
        ->name('master.orang_tua.destroy');

    // =========== MASTER AL QURAN ===========
    Route::get('master/quran/surah', [\App\Http\Controllers\Master\QuranController::class, 'surah'])
        ->name('master.quran.surah');
    Route::get('master/quran/surah/{number}', [\App\Http\Controllers\Master\QuranController::class, 'surahDetail'])
        ->name('master.quran.surahDetail');
    Route::get('master/quran/bookmark', [\App\Http\Controllers\Master\QuranController::class, 'listBookmarks'])
        ->name('master.quran.listBookmarks');
    Route::post('master/quran/bookmark/store', [\App\Http\Controllers\Master\QuranController::class, 'storeBookmark'])
        ->name('master.quran.storeBookmark');
    Route::delete('master/quran/bookmark/{id}/delete', [\App\Http\Controllers\Master\QuranController::class, 'destroyBookmark'])
        ->name('master.quran.destroyBookmark');
});
