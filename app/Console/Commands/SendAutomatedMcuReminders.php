<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JadwalMcu;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\FCMService;
use App\Models\NotificationLog;

class SendAutomatedMcuReminders extends Command
{
    protected $signature = 'mcu:send-reminders';
    
    protected $description = 'Otomatis mengirim notifikasi pengingat Jadwal MCU (H-1 Jam 19:00 & H-H Jam 06:00)';

    public function handle()
    {
        // 1. Cek Jam Server (Test Mode: 19)
        // $jamSekarang = 19;
        // $jamSekarang = Carbon::now()->hour;
        $timezone = 'Asia/Makassar';
        // 1. Cek Jam Server Saat Ini (Kunci secara eksplisit ke WITA)
        $jamSekarang = Carbon::now($timezone)->format('H:i'); // Format 24 jam (00-23)

        // 2. Tentukan Tanggal Target & Teks
        if ($jamSekarang == '19:00') {
            $targetTanggal = Carbon::tomorrow()->toDateString();
            $waktuTeks = "BESOK";
            $title = "⏰ Pengingat: Besok Jadwal MCU Anda!";
        } elseif ($jamSekarang == '10:39') {
            $targetTanggal = Carbon::today()->toDateString();
            $waktuTeks = "PAGI INI";
            $title = "⏰ Hari Ini Jadwal MCU Anda!";
        } else {
            $this->warn("Command berjalan di luar jadwal. Harus jam 06:00 atau 19:00.");
            return;
        }

        // 3. Tarik Data Jadwal
        $jadwalTarget = JadwalMcu::whereDate('tanggal_mcu', $targetTanggal)
                                ->where('status', 'Scheduled') 
                                ->with(['karyawan', 'pesertaMcu']) 
                                ->get();

        if ($jadwalTarget->isEmpty()) {
            $this->info("Tidak ada jadwal MCU untuk {$waktuTeks} ({$targetTanggal}).");
            return;
        }

        $successCount = 0;
        
        foreach ($jadwalTarget as $jadwal) {
            $targetUser = $jadwal->karyawan ?? $jadwal->pesertaMcu;

            if ($targetUser && !empty($targetUser->fcm_token)) {
                $nama = $targetUser->nama_karyawan ?? $targetUser->nama_lengkap ?? 'Peserta MCU';
                
                // 4. Pesan Unik dan Menarik
                $body = "Halo, {$nama}! 👋\n"
                      . "Kami mengingatkan bahwa {$waktuTeks} adalah jadwal Medical Check Up Anda di Klinik STMC.\n\n"
                      . "🌟 PERSIAPAN WAJIB:\n"
                      . "• 💧 Wajib puasa 10-12 jam sebelum ambil darah (hanya boleh minum air putih).\n"
                      . "• 😴 Hindari begadang dan istirahat yang cukup.\n"
                      . "• 🪪 Jangan lupa bawa KTP / ID Card Perusahaan.\n\n"
                      . "Klik tombol di bawah untuk panduan lengkapnya! 👇";

                $actionLink = 'route:/informasi-mcu'; 

                // 5. Kirim Notifikasi (Tanpa Banner)
                $isSent = FCMService::sendPushNotification(
                    $targetUser->fcm_token,
                    $title,
                    $body,
                    $actionLink
                );

                if ($isSent) {
                    $successCount++;
                    Log::info("CRON: Notifikasi {$waktuTeks} terkirim ke {$nama}");
                }
            }
        }

        try {
            NotificationLog::create([
                'mode' => 'automatic',
                'scheduled_date' => $targetTanggal,
                'total_targets' => $jadwalTarget->count(),
                'fcm_success' => $successCount,
                'email_success' => 0,
                'admin_id' => null, // 🌟 TAMBAHKAN INI! Karena Cron tidak punya Auth, kita default ke ID 1 (Sistem/Superadmin)
            ]);
        } catch (\Exception $e) {
            // Jika gagal simpan, catat error-nya di storage/logs/laravel.log
            Log::error("Gagal menyimpan riwayat otomatis: " . $e->getMessage());
        }

        $this->info("CRON SUKSES: Mengirim {$successCount} pengingat untuk jadwal {$waktuTeks}.");
    }
}