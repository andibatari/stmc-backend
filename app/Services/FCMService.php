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

            // 🌟 1. KITA MASUKKAN SEMUA INFO KE DALAM BLOK DATA
            $messagePayload = [
                'token' => $fcmToken,
                'data' => [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'action_link' => $link ?? '', 
                    'recipient_sap' => $recipientSap ?? 'ALL',
                    'tipe' => $tipe,
                    'title' => $title, // Pindahkan title ke sini
                    'body' => $body,   // Pindahkan body ke sini
                ],
            ];

            // 🌟 2. JIKA INI BUKAN ALARM PANGGILAN, BARU KITA TAMBAHKAN BLOK NOTIFICATION
            // (Agar notifikasi pengumuman biasa tetap muncul normal)
            if ($tipe !== 'panggilan_poli') {
                $messagePayload['notification'] = [
                    'title' => $title,
                    'body' => $body,
                ];
            }

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