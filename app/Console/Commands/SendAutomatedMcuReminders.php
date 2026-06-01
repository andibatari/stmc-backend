<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JadwalMcu;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendAutomatedMcuReminders extends Command
{
    protected $signature = 'mcu:send-reminders';
    protected $description = 'Otomatis mengirim notifikasi pengingat Jadwal MCU besok hari';

    public function handle()
    {
        $besok = Carbon::tomorrow()->toDateString();
        $jadwalBesok = JadwalMcu::whereDate('tanggal_mcu', $besok)
                                ->where('status', 'Scheduled')
                                ->with('patient') // Pastikan relasi 'patient' (atau karyawan) sudah benar
                                ->get();

        if ($jadwalBesok->isEmpty()) {
            $this->info("Tidak ada jadwal MCU untuk besok ({$besok}).");
            return;
        }

        $successCount = 0;
        foreach ($jadwalBesok as $jadwal) {
            // Ambil data karyawan/pasien dari relasi
            $karyawan = $jadwal->patient; // Sesuaikan dengan nama relasi di model JadwalMcu

            if ($karyawan && !empty($karyawan->fcm_token)) {
                $title = "Pengingat Jadwal MCU";
                $body = "Halo " . ($karyawan->nama_lengkap ?? 'Karyawan') . ", jangan lupa jadwal MCU kamu besok ya!";

                // Panggil Service FCM kita
                $isSent = \App\Services\FCMService::sendPushNotification(
                    $karyawan->fcm_token,
                    $title,
                    $body
                );

                if ($isSent) {
                    $successCount++;
                    Log::info("CRON: Notifikasi berhasil dikirim ke " . $karyawan->nama_lengkap);
                } else {
                    Log::error("CRON: Gagal kirim notifikasi ke " . $karyawan->nama_lengkap);
                }
            } else {
                Log::warning("CRON: Karyawan tidak ditemukan atau Token FCM kosong untuk Jadwal ID: " . $jadwal->id);
            }
        }

        \App\Models\NotificationLog::create([
            'mode' => 'automatic',
            'scheduled_date' => $besok,
            'total_targets' => $jadwalBesok->count(),
            'fcm_success' => $successCount,
            'email_success' => 0,
        ]);

        $this->info("CRON SUKSES: Mengirim {$successCount} pengingat otomatis.");
        Log::info("CRON: Selesai. Total sukses: {$successCount}");
    }
}