<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Google\Auth\Credentials\ServiceAccountCredentials;

class FCMService
{
    // 🌟 PERBAIKAN 1: Tambahkan parameter $link = null di sini
    public static function sendPushNotification($fcmToken, $title, $body, $link = null)
    {
        try {
            $credentialsPath = storage_path('app/firebase_credentials.json');
            
            if (!file_exists($credentialsPath)) {
                Log::error("FCMService: File kredensial tidak ditemukan di " . $credentialsPath);
                return false;
            }

            // Membaca Project ID dari file JSON
            $json = json_decode(file_get_contents($credentialsPath), true);
            $projectId = $json['project_id'];

            // Menggunakan Laravel Http Client untuk mengirim ke Firebase HTTP v1 API
            $response = Http::withToken(self::getAccessToken($credentialsPath))
                ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                    'message' => [
                        'token' => $fcmToken,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        // 🌟 PERBAIKAN 2: Tambahkan payload 'data' untuk mengirimkan link ke Flutter
                        'data' => [
                            // Jika $link kosong (null), kita kirim string kosong ("") agar Firebase tidak error
                            'link' => $link ?? '', 
                        ],
                        'android' => [
                            'priority' => 'high',
                        ],
                    ],
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
        // Fungsi sederhana untuk mendapatkan token akses dari Google
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $credentials = new ServiceAccountCredentials($scopes, $credentialsPath);
        return $credentials->fetchAuthToken()['access_token'];
    }
}