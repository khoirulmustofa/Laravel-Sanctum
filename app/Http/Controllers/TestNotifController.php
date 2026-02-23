<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class TestNotifController extends Controller
{
   public function sendDirect(Request $request, Messaging $messaging)
{
    // 1. Validasi input
    $request->validate(['fcm_token' => 'required|string']);

    // 2. Susun pesan dengan standar HTTP v1
    $message = CloudMessage::fromArray([
        'token' => $request->fcm_token,
        'notification' => [
            'title' => 'Tes Berhasil! 🚀',
            'body'  => 'Notifikasi ini dikirim langsung dari Controller Laravel 12.',
            'image' => 'https://static.republika.co.id/uploads/images/inpicture_slide/ilustrasi-puasa-ramadhan_250305205223-178.jpg',
        ],
        'data' => [
            // Gunakan data untuk navigasi di Ionic nanti
            'url' => '/profile', 
            'id' => '1',
            'type' => 'announcement'
        ],
        'android' => [
            'priority' => 'high',
            'notification' => [
                'sound' => 'default',
                'default_sound' => true,
                'notification_priority' => 'PRIORITY_HIGH',
            ],
        ],
    ]);

    try {
        $messaging->send($message);
        return response()->json([
            'status' => 'success',
            'message' => 'Terkirim!',
            'target' => substr($request->fcm_token, 0, 10) . '...' // Log sebagian token
        ]);
    } catch (\Kreait\Firebase\Exception\Messaging\InvalidMessage $e) {
        // Error jika format pesan salah
        return response()->json(['status' => 'error', 'message' => 'Format pesan salah'], 400);
    } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
        // Error jika token sudah tidak valid/expired (User sudah uninstall aplikasi)
        return response()->json(['status' => 'error', 'message' => 'Token tidak ditemukan/expired'], 404);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
}
}
