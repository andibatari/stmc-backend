<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\JadwalMcu;
use App\Services\FCMService;

class ProcessMcuReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $recipientIds;
    protected $log;

    public function __construct($recipientIds, $log)
    {
        $this->recipientIds = $recipientIds;
        $this->log = $log;
    }

    public function handle()
    {
        // Ambil data jadwal berdasarkan ID yang dipilih beserta relasi karyawannya
        $jadwals = JadwalMcu::whereIn('id', $this->recipientIds)->with('karyawan')->get();
        $fcmSuccessCount = 0;

        foreach ($jadwals as $jadwal) {
            $karyawan = $jadwal->karyawan;

            if ($karyawan && !empty($karyawan->fcm_token)) {
                $title = "Pengingat Jadwal MCU Manual";
                $body = "Halo " . ($karyawan->nama_karyawan ?? 'Karyawan') . ", jangan lupa jadwal Medical Check Up kamu besok ya! Mohon datang tepat waktu. Terima kasih.";

                // Kirim lewat FCMService
                $statusFCM = FCMService::sendPushNotification(
                    $karyawan->fcm_token,
                    $title,
                    $body
                );

                if ($statusFCM) {
                    $fcmSuccessCount++;
                }
            }
        }

        // Update tabel riwayat log dengan jumlah yang berhasil dikirim
        $this->log->update([
            'fcm_success' => $fcmSuccessCount
        ]);
    }
}