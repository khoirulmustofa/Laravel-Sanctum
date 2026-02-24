<?php

namespace App\Jobs;

use App\Models\PushNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;

class SendPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public array $data,
        public string $fcmToken,
        public $notifLogId // ID dari tabel push_notifications
    ) {}

    public function handle(Messaging $messaging): void
    {
        $message = CloudMessage::fromArray([
            'token' => $this->fcmToken,
            'notification' => [
                'title' => $this->data['title'],
                'body'  => $this->data['body'],
                'image' => $this->data['image'] ?? null,
            ],
            'data' => [
                'notification_id' => (string) $this->notifLogId,
                'type' => $this->data['type'] ?? 'general',
                'related_id' => (string) ($this->data['related_id'] ?? ''),
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
            // Update status di database jika berhasil
            PushNotification::find($this->notifLogId)?->update(['is_sent' => true]);
        } catch (\Exception $e) {
            Log::error("Gagal kirim FCM: " . $e->getMessage());
        }
    }
}