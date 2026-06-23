<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Google\Auth\Credentials\ServiceAccountCredentials;

class FCMService
{
    // Tambahkan parameter $tipe di akhir
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

            // Atur nama file suara dan ID channel khusus jika tipenya panggilan poli
            $soundName = ($tipe === 'panggilan_poli') ? 'ding_dong.wav' : 'default';
            $channelId = ($tipe === 'panggilan_poli') ? 'channel_panggilan_poli_v3' : 'fcm_default_channel';

            $messagePayload = [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'action_link' => $link ?? '', 
                    'recipient_sap' => $recipientSap ?? 'ALL',
                    'tipe' => $tipe, // Data ini akan dibaca oleh main.dart Flutter
                ],
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'color' => '#C00000',
                        'sound' => $soundName,
                        'channel_id' => $channelId,
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