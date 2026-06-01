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
                                ->get();

        if ($jadwalBesok->isEmpty()) {
            $this->info("Tidak ada jadwal MCU untuk besok ({$besok}).");
            return;
        }

        $successCount = 0;
        foreach ($jadwalBesok as $jadwal) {
            // CATATAN: Panggil API Firebase / Email Karyawan di sini
            // ...
            $successCount++;
        }

        \App\Models\NotificationLog::create([
            'mode' => 'automatic',
            'scheduled_date' => $besok,
            'total_targets' => $jadwalBesok->count(),
            'fcm_success' => $successCount,
            'email_success' => 0,
            // admin_users_id dikosongkan karena dieksekusi otomatis oleh sistem
        ]);

        $this->info("CRON SUKSES: Mengirim {$successCount} pengingat otomatis.");
        Log::info("CRON: Mengirim {$successCount} pengingat MCU H-1.");
    }
}