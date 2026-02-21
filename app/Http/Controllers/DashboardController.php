<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $userCount = User::count();

        return response()->json([
            'success' => true,
            'user_count' => $userCount,

        ]);
    }

    public function update(Request $request, $key)
    {
        // 1. Validation (Allow string or file)
        $request->validate([
            'value' => 'required',
        ]);

        // 2. Find or create the setting
        $setting = Setting::firstOrNew(['key' => $key]);

        // 3. Handle File vs String
        if ($request->hasFile('value')) {
            $file = $request->file('value');

            // 1. Buat nama file baru: setting_1708310000.jpg
            $extension = $file->getClientOriginalExtension();
            $fileName = 'setting_'.time().'.'.$extension;

            // 2. Simpan ke folder 'uploads/settings' di dalam disk 'public'
            // Ini akan tersimpan di: storage/app/public/uploads/settings
            $path = $file->storeAs('uploads/settings', $fileName, 'public');

            $setting->value = $path;
        } else {
            // Save as normal string
            $setting->value = $request->value;
        }

        $setting->save();

        // 4. Kirim respon balik ke Nuxt
        return response()->json([
            'success' => true,
            'message' => 'Setting updated successfully',
            'data' => $setting,
        ]);
    }
}
