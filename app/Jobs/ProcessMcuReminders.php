<?php

namespace App\Jobs;

use App\Models\JadwalMcu;
use App\Models\NotificationLog;
use App\Mail\McuReminderMail;
use App\Notifications\McuReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Throwable;
use Exception; // Tambahkan import Exception

class ProcessMcuReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $jadwalIds;
    protected $notificationLog;

    public function __construct(array $jadwalIds, $log)
    {
        $this->jadwalIds = $jadwalIds;
        $this->notificationLog = $log;
    }

    public function handle()
    {
        // PENTING: Eager load relasi yang diperlukan oleh Accessor patient()
        $jadwals = JadwalMcu::with(['karyawan', 'pesertaMcu'])
                           ->whereIn('id', $this->jadwalIds)
                           ->get();
        
        $successEmailCount = 0;
        $successAppCount = 0;
        $failedRecipients = []; // Log detail kegagalan

        foreach ($jadwals as $jadwal) {
            $patient = $jadwal->patient; // Menggunakan Accessor 'patient'
            $email = $patient->email_karyawan ?? $patient->email_pasien ?? null;
            $fcmToken = $patient->fcm_token ?? null; 
            $nik = $patient->nik_karyawan ?? $patient->nik_pasien ?? $jadwal->nik_pasien ?? 'N/A';
            
            $emailStatus = false;
            $fcmStatus = false;
            
            // --- 1. PROSES PENGIRIMAN EMAIL ---
            if ($email) {
                try {
                    Mail::to($email)->send(new McuReminderMail($jadwal));
                    $successEmailCount++;
                    $emailStatus = true;
                } catch (Throwable $e) {
                    Log::warning("Gagal kirim EMAIL pengingat MCU ke NIK {$nik}. Error: " . $e->getMessage());
                    $failedRecipients[] = ['nik' => $nik, 'type' => 'Email', 'reason' => substr($e->getMessage(), 0, 100)];
                }
            } else {
                 // Log bahwa email tidak tersedia
                 $failedRecipients[] = ['nik' => $nik, 'type' => 'Email', 'reason' => 'Alamat email tidak tersedia.'];
            }

            // --- 2. PROSES PENGIRIMAN NOTIFIKASI APLIKASI (FCM) ---
            if ($fcmToken && $patient->id) {
                try {
                    // Panggil notifikasi pada Model Patient (Karyawan/PesertaMcu)
                    // Asumsi: Model Karyawan/PesertaMcu menggunakan trait Notifiable
                    $patient->notify(new McuReminderNotification($jadwal)); 
                    $successAppCount++;
                    $fcmStatus = true;
                } catch (Throwable $e) {
                    Log::warning("Gagal kirim FCM pengingat MCU ke NIK {$nik}. Error: " . $e->getMessage());
                    $failedRecipients[] = ['nik' => $nik, 'type' => 'FCM', 'reason' => substr($e->getMessage(), 0, 100)];
                }
            } else {
                 // Log bahwa token FCM tidak tersedia
                 $failedRecipients[] = ['nik' => $nik, 'type' => 'FCM', 'reason' => 'FCM Token tidak tersedia.'];
            }
        }

        // --- 3. PEMBARUAN LOG (Langkah Penting Setelah Semua Selesai) ---
        try {
            $this->notificationLog->update([
                'email_success' => $successEmailCount,
                'fcm_success' => $successAppCount,
                'failed_recipients' => $failedRecipients,
            ]);
        } catch (Throwable $e) {
            Log::error("Gagal memperbarui NotificationLog (ID: {$this->notificationLog->id}). Error: " . $e->getMessage());
        }
    }

    // Jika Job gagal setelah retry, panggil method failed()
    public function failed(Throwable $exception)
    {
        Log::critical("Job ProcessMcuReminders GAGAL TOTAL setelah retry. Log ID: {$this->notificationLog->id}. Error: " . $exception->getMessage());
    }
}