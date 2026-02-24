<?php

namespace App\Http\Controllers;

use App\Jobs\SendPushNotification;
use App\Models\PushNotification;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class TestNotifController extends Controller
{
    public function sendDirect(Request $request, Messaging $messaging)
    {
        // 1. Validasi
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'fcm_token' => 'required|string',
            'title' => 'required|string',
            'body' => 'required|string',
            'image' => 'nullable|string',
            'type' => 'nullable|string',
            'related_id' => 'nullable|string',
        ]);

        // 2. Simpan Log ke Tabel push_notifications (is_sent masih false)
        $log = PushNotification::create([
            'user_id' => $request->user_id,
            'title'   => $request->title,
            'body'    => $request->body,
            'image'   => $request->image,
            'type'    => $request->type ?? 'general',
            'related_id' => $request->related_id,
            'is_sent' => false,
        ]);

        // 3. Lempar ke Queue (Antrean)
        SendPushNotification::dispatch(
            $request->only(['title', 'body', 'type', 'related_id', 'image']),
            $request->fcm_token,
            $log->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi masuk antrean!',
            'log_id' => $log->id
        ]);
    }
}
