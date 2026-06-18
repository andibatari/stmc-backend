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
    protected $description = 'Otomatis mengirim notifikasi pengingat Jadwal MCU';

    public function handle()
    {
        $timezone = 'Asia/Makassar';
        
        // 🌟 BYPASS WAKTU: Kita paksa sistem mencari jadwal untuk BESOK tanpa peduli jam!
        $targetTanggal = Carbon::tomorrow($timezone)->toDateString();
        $waktuTeks = "TEST OTOMATIS";
        $title = "⏰ TEST: Besok Jadwal MCU Anda!";

        $jadwalTarget = JadwalMcu::whereDate('tanggal_mcu', $targetTanggal)
                                ->where('status', 'Scheduled') 
                                ->with(['karyawan', 'pesertaMcu']) 
                                ->get();

        $totalTarget = $jadwalTarget->count();
        $successCount = 0;
        
        if ($totalTarget > 0) {
            foreach ($jadwalTarget as $jadwal) {
                $targetUser = $jadwal->karyawan ?? $jadwal->pesertaMcu;

                if ($targetUser && !empty($targetUser->fcm_token)) {
                    $nama = $targetUser->nama_karyawan ?? $targetUser->nama_lengkap ?? 'Peserta MCU';
                    
                    $body = "Ini adalah pengujian notifikasi otomatis.\n\n"
                          . "Klik tombol di bawah untuk panduan lengkapnya! 👇";

                    $recipientSap = $targetUser->no_sap ?? $targetUser->nik_karyawan ?? $targetUser->nik_pasien ?? 'ALL';

                    $isSent = FCMService::sendPushNotification(
                        $targetUser->fcm_token, $title, $body, 'route:/informasi-mcu', $recipientSap
                    );

                    if ($isSent) {
                        $successCount++;
                    }
                }
            }
        }

        // 🌟 PAKSA SIMPAN KE RIWAYAT DETIK INI JUGA!
        try {
            NotificationLog::create([
                'mode' => 'automatic',
                'scheduled_date' => $targetTanggal,
                'total_targets' => $totalTarget,
                'fcm_success' => $successCount,
                'email_success' => 0,
                'admin_users_id' => null, 
            ]);
            Log::info("CRON TEST BERHASIL: Riwayat berhasil dicatat.");
        } catch (\Exception $e) {
            Log::error("Gagal simpan riwayat Cron Test: " . $e->getMessage());
        }
    }
}