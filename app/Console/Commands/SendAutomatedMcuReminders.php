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
        // 1. Cek Jam Server Saat Ini
        // $jamSekarang = Carbon::now()->hour;
        $jamSekarang = 19;

        // 2. Tentukan Tanggal Target & Teks Notifikasi Berdasarkan Jam
        if ($jamSekarang == 19) {
            // JIKA JAM 7 MALAM -> Kirim Pengingat H-1 (Untuk Jadwal Besok)
            $targetTanggal = Carbon::tomorrow()->toDateString();
            $waktuTeks = "BESOK";
            $title = "⏰ Pengingat: Besok Jadwal MCU Anda!";
        } elseif ($jamSekarang == 6) {
            // JIKA JAM 6 PAGI -> Kirim Pengingat H-H (Untuk Jadwal Hari Ini)
            $targetTanggal = Carbon::today()->toDateString();
            $waktuTeks = "PAGI INI";
            $title = "⏰ Hari Ini Jadwal MCU Anda!";
        } else {
            // Mencegah command berjalan di jam yang salah
            $this->warn("Command berjalan di luar jadwal. Harus jam 06:00 atau 19:00.");
            return;
        }

        // 3. Tarik Data Jadwal Sesuai Target Tanggal
        $jadwalTarget = JadwalMcu::whereDate('tanggal_mcu', $targetTanggal)
                                ->where('status', 'Scheduled') // Hanya yang belum hadir
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
                
                // 4. Susun Pesan Aturan (Teks Unik Sesuai Waktu)
                $body = "Halo {$nama}! Mengingatkan bahwa {$waktuTeks} adalah jadwal Medical Check Up Anda di Klinik STMC.\n\n"
                      . "⚠️ ATURAN SEBELUM MCU:\n"
                      . "1. Wajib puasa 10-12 jam sebelum ambil darah (hanya boleh minum air putih).\n"
                      . "2. Hindari begadang dan istirahat yang cukup.\n"
                      . "3. Jangan lupa bawa KTP / ID Card Perusahaan.\n\n"
                      . "Ketuk tombol di bawah untuk panduan lengkapnya!";
                
                // 5. Tautan ke Aturan MCU (Ditangkap oleh Flutter)
                // Ganti URL ini dengan link panduan yang kamu inginkan. Tombol "Buka Tautan Lampiran" di Flutter akan otomatis muncul!
                $actionLink = 'route:/informasi-mcu'; 

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

        // 6. Simpan Log Riwayat
        NotificationLog::create([
            'mode' => 'automatic',
            'scheduled_date' => $targetTanggal,
            'total_targets' => $jadwalTarget->count(),
            'fcm_success' => $successCount,
            'email_success' => 0,
        ]);

        $this->info("CRON SUKSES: Mengirim {$successCount} pengingat untuk jadwal {$waktuTeks}.");
    }
}