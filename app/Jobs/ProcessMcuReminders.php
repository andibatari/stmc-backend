<?php

namespace App\Jobs;

use App\Models\JadwalMcu;
use App\Models\NotificationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

// Import yang WAJIB untuk FCM
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\FirebaseMessaging;


class ProcessMcuReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $jadwalIds;
    protected $notificationLog;

    /**
     * @param array $jadwalIds Array of JadwalMcu IDs to process
     * @param NotificationLog $log The NotificationLog model instance
     */
    public function __construct(array $jadwalIds, NotificationLog $log)
    {
        $this->jadwalIds = $jadwalIds;
        $this->notificationLog = $log;
    }

    public function handle(): void
    {
        // Eager load relasi yang diperlukan oleh Accessor patient()
        $jadwals = JadwalMcu::with(['karyawan', 'pesertaMcu'])
                            ->whereIn('id', $this->jadwalIds)
                            ->get();
        
        // Hapus: $successEmailCount = 0;
        $successFCMCount = 0;
        $failedRecipients = [];

        foreach ($jadwals as $jadwal) {
            // Asumsi: Accessor 'patient' mengembalikan Model Karyawan/PesertaMcu
            $patient = $jadwal->patient; 
            
            // Asumsi: Model Karyawan/PesertaMcu memiliki kolom 'fcm_token'
            $fcmToken = $patient->fcm_token ?? null; 
            $nik = $patient->nik_karyawan ?? $patient->nik_pasien ?? $jadwal->nik_pasien ?? 'N/A';
            
            // --- Hapus: $emailStatus = false; ---
            $fcmStatus = false;
            
            
            // --- 1. PROSES PENGIRIMAN NOTIFIKASI APLIKASI (FCM) ---
            if ($fcmToken) {
                try {
                    // Konten Notifikasi
                    $title = "🔔 Pengingat Jadwal MCU!";
                    $body = "Jadwal MCU Anda adalah besok, " . 
                            \Carbon\Carbon::parse($jadwal->tanggal_mcu)->format('d M Y');
                    
                    // Konfigurasi pesan FCM
                    $message = CloudMessage::withTarget('token', $fcmToken)
                        ->withNotification(
                            \Kreait\Firebase\Messaging\Notification::create($title, $body)
                        )
                        // Data payload untuk aplikasi Flutter
                        ->withData([
                            'mcu_id' => (string) $jadwal->id,
                            'type' => 'mcu_reminder',
                            'tanggal' => $jadwal->tanggal_mcu,
                        ]);
                        
                    // Kirim pesan
                    FirebaseMessaging::send($message);
                    
                    $successFCMCount++;
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

        // --- 2. PEMBARUAN LOG ---
        try {
            $this->notificationLog->update([
                'email_success' => 0, // Ditetapkan ke 0 karena dibatalkan
                'fcm_success' => $successFCMCount,
                'failed_recipients' => $failedRecipients,
            ]);
        } catch (Throwable $e) {
            Log::error("Gagal memperbarui NotificationLog (ID: {$this->notificationLog->id}). Error: " . $e->getMessage());
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        Log::critical("Job ProcessMcuReminders GAGAL TOTAL setelah retry. Log ID: {$this->notificationLog->id}. Error: " . $exception->getMessage());
        // Anda mungkin ingin mengupdate log dengan status kegagalan di sini
    }
}