<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Google\Auth\Credentials\ServiceAccountCredentials;

class FCMService
{
    public static function sendPushNotification($fcmToken, $title, $body, $link = null, $recipientSap = null, $tipe = 'general')
    {
        Log::info("DEBUG FCM: Mencoba kirim notif ke token: " . $fcmToken);
        try {
            $credentialsPath = storage_path('app/firebase_credentials.json');
            
            if (!file_exists($credentialsPath)) {
                Log::error("FCMService: File kredensial tidak ditemukan.");
                return false;
            }

            $json = json_decode(file_get_contents($credentialsPath), true);
            $projectId = $json['project_id'];

            $channelId = ($tipe === 'panggilan_poli') ? 'channel_panggilan_poli_v6' : 'channel_pengumuman_v1';

            // 🌟 1. PAYLOAD DASAR (100% PURE DATA)
            // Tidak ada kata 'notification' sama sekali di sini. Murni data untuk Flutter.
            $messagePayload = [
                'token' => $fcmToken,
                'data' => [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'action_link' => $link ?? '', 
                    'recipient_sap' => $recipientSap ?? 'ALL',
                    'tipe' => $tipe,
                    'title' => $title, 
                    'body' => $body,
                ],
                'android' => [
                    'priority' => 'high', // Cukup priority high agar Flutter terbangun
                ],
            ];

            // 🌟 2. LOGIKA CERDAS
            // JIKA BUKAN panggilan poli (berarti pengumuman), baru kita tambahkan blok 'notification'
            // agar OS Android yang mengambil alih untuk memunculkan spanduk dan suara standar.
            if ($tipe !== 'panggilan_poli') {
                $messagePayload['notification'] = [
                    'title' => $title,
                    'body' => $body,
                ];
                $messagePayload['android']['notification'] = [
                    'channel_id' => $channelId,
                    'sound' => 'default',
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