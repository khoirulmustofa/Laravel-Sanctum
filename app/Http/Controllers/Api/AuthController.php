<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * REGISTER
     */
    public function register(Request $request)
    {
        // 1. Validasi Langsung di Controller
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // butuh field password_confirmation
        ]);

        // 2. Create User
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // 3. Create Token
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. Return JSON Manual
        return response()->json([
            'success' => true,
            'message' => 'Registrasi Berhasil',
            'data' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * LOGIN
     */
    public function login(Request $request)
    {
        try {

            // 1. Validasi Input
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            // 2. Cek Auth
            if (! Auth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password salah.',
                    'data' => null,
                ], 401);
            }

            // 3. Ambil User & Token
            $user = User::where('email', $request->email)->firstOrFail();

            // Hapus token lama (opsional, agar 1 device login)
            // $user->tokens()->delete();

            $token = $user->createToken('auth_token')->plainTextToken;

            // 4. Return JSON Manual
            return response()->json([
                'success' => true,
                'message' => 'Login Berhasil',
                'data' => $user,
                'token' => $token,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'data' => $e->errors(),
            ], 422);
        }
    }

    /**
     * LOGOUT
     */
    public function logout(Request $request)
    {
        // Hapus token yang sedang dipakai (Revoke)
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout Berhasil',
            'data' => null,
        ], 200);
    }

    /**
     * GET USER (ME)
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Data User',
            'data' => $request->user(),
        ], 200);
    }

    /**
     * UPDATE FCM TOKEN
     */
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'nullable|string',
        ]);

        // Simpan token ke user yang sedang login
        $request->user()->update([
            'fcm_token' => $request->fcm_token,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Token updated',
            'data' => $request->user(),
        ], 200);
    }
}
