<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Google\Auth\Credentials\ServiceAccountCredentials;

class FCMService
{
    public static function sendPushNotification($fcmToken, $title, $body, $link = null)
    {
        try {
            $credentialsPath = storage_path('app/firebase_credentials.json');
            
            if (!file_exists($credentialsPath)) {
                Log::error("FCMService: File kredensial tidak ditemukan di " . $credentialsPath);
                return false;
            }

            $json = json_decode(file_get_contents($credentialsPath), true);
            $projectId = $json['project_id'];

            // 🌟 Payload bersih tanpa gambar
            $messagePayload = [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'link' => $link ?? '', 
                ],
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'color' => '#C00000',
                        'sound' => 'default',
                    ]
                ],
            ];

            $response = Http::withToken(self::getAccessToken($credentialsPath))
                ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                    'message' => $messagePayload
                ]);

            if ($response->successful()) {
                Log::info("FCMService: Berhasil dikirim ke " . substr($fcmToken, 0, 10));
                return true;
            } else {
                Log::error("FCMService Error: " . $response->body());
                return false;
            }

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