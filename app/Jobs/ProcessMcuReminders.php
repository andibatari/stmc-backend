<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\JadwalMcu;
use App\Services\FCMService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessMcuReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $recipientIds;
    protected $log;

    public function __construct(array $recipientIds, $log)
    {
        $this->recipientIds = $recipientIds;
        $this->log = $log;
    }

    public function handle()
    {
        // 🌟 PERBAIKAN 1: Ambil relasi karyawan DAN peserta_mcu (Pasien Umum)
        $jadwals = JadwalMcu::whereIn('id', $this->recipientIds)
                    ->with(['karyawan', 'pesertaMcu'])
                    ->get();
                    
        $fcmSuccessCount = 0;

        foreach ($jadwals as $jadwal) {
            // 🌟 PERBAIKAN 2: Cek apakah ini Karyawan PTST atau Pasien Umum
            $targetUser = $jadwal->karyawan ?? $jadwal->pesertaMcu;

            if ($targetUser && !empty($targetUser->fcm_token)) {
                // Tarik nama sesuai struktur tabelnya
                $nama = $targetUser->nama_karyawan ?? $targetUser->nama_lengkap ?? 'Peserta MCU';
                
                // 🌟 PERBAIKAN 3: Format tanggal agar teksnya akurat
                Carbon::setLocale('id'); // Pastikan format bahasa Indonesia
                $tanggal = Carbon::parse($jadwal->tanggal_mcu)->translatedFormat('l, d F Y');

                $title = "Pengingat Jadwal MCU";
                $body = "Halo {$nama}, pengingat jadwal Medical Check Up kamu pada {$tanggal}. Mohon perhatikan protokol kesehatan dan datang tepat waktu. Terima kasih.";

                try {
                    // Kirim lewat FCMService
                    $statusFCM = FCMService::sendPushNotification(
                        $targetUser->fcm_token,
                        $title,
                        $body
                    );

                    if ($statusFCM) {
                        $fcmSuccessCount++;
                    }
                } catch (\Throwable $e) {
                    Log::warning("Gagal kirim FCM pengingat jadwal MCU ke {$nama}. Error: " . $e->getMessage());
                }
            }
        }

        // Update tabel riwayat log dengan jumlah yang berhasil dikirim
        $this->log->update([
            'fcm_success' => $fcmSuccessCount
        ]);
    }
}