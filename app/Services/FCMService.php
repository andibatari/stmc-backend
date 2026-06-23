<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Google\Auth\Credentials\ServiceAccountCredentials;

class FCMService
{
    public static function sendPushNotification($fcmToken, $title, $body, $link = null, $recipientSap = null, $tipe = 'general')
    {
        try {
            $credentialsPath = storage_path('app/firebase_credentials.json');
            
            if (!file_exists($credentialsPath)) {
                Log::error("FCMService: File kredensial tidak ditemukan.");
                return false;
            }

            $json = json_decode(file_get_contents($credentialsPath), true);
            $projectId = $json['project_id'];

            // 🌟 Atur Channel ID berdasarkan tipe (harus persis sama dengan yang didaftarkan di Flutter main.dart)
            $channelId = ($tipe === 'panggilan_poli') ? 'channel_panggilan_poli_v5' : 'fcm_default_channel';

            $messagePayload = [
                'token' => $fcmToken,
                // 🌟 KEMBALIKAN BLOK NOTIFICATION INI AGAR GOOGLE PLAY SERVICES BISA MEMBACANYA SAAT APLIKASI DI-KILL
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'action_link' => $link ?? '', 
                    'recipient_sap' => $recipientSap ?? 'ALL',
                    'tipe' => $tipe,
                ],
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'color' => '#C00000',
                        'channel_id' => $channelId, // 🌟 KUNCI UTAMA: Google Play Services akan mencari channel ini
                        'sound' => 'ding_dong.wav' // Opsional, sekadar penegas untuk OS
                    ]
                ],
            ];

            $response = Http::withToken(self::getAccessToken($credentialsPath))
                ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                    'message' => $messagePayload
                ]);

            return $response->successful();

        } catch (\Exception $e) {
            Log::error("FCMService Exception: " . $e->getMessage());
            return false;
        }
    }

    private static function getAccessToken($credentialsPath)
    {
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $credentials = new ServiceAccountCredentials($scopes, $credentialsPath);
        return $credentials->fetchAuthToken()['access_token'];
    }
}