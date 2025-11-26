<?php

namespace App\Jobs;

use App\Models\Karyawan;
use App\Models\NotificationLog;
use App\Mail\SubmissionReminderMail;
use App\Notifications\McuSubmissionNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Throwable;
use Exception;

class ProcessSubmissionReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $karyawanIds;
    protected $notificationLog;

    public function __construct(array $karyawanIds, $log)
    {
        $this->karyawanIds = $karyawanIds;
        $this->notificationLog = $log;
    }

    public function handle()
    {
        // Eager load departemen untuk template email
        $recipients = Karyawan::with('departemen')->whereIn('id', $this->karyawanIds)->get();
        
        $successEmailCount = 0;
        $successAppCount = 0;
        $failedRecipients = [];

        foreach ($recipients as $karyawan) {
            $email = $karyawan->email_karyawan ?? null;
            $fcmToken = $karyawan->fcm_token ?? null;
            $nik = $karyawan->nik_karyawan ?? 'N/A';
            
            // --- 1. PROSES PENGIRIMAN EMAIL ---
            if ($email) {
                try {
                    Mail::to($email)->send(new SubmissionReminderMail($karyawan, $this->notificationLog->scheduled_date));
                    $successEmailCount++;
                } catch (Throwable $e) {
                    Log::warning("Gagal kirim EMAIL pengajuan MCU ke NIK {$nik}. Error: " . $e->getMessage());
                    $failedRecipients[] = ['nik' => $nik, 'type' => 'Email', 'reason' => substr($e->getMessage(), 0, 100)];
                }
            } else {
                $failedRecipients[] = ['nik' => $nik, 'type' => 'Email', 'reason' => 'Alamat email tidak tersedia.'];
            }

            // --- 2. PROSES PENGIRIMAN NOTIFIKASI APLIKASI (FCM) ---
            if ($fcmToken) {
                try {
                    // Panggil notifikasi pada Model Karyawan
                    $karyawan->notify(new McuSubmissionNotification($karyawan, $this->notificationLog->scheduled_date));
                    $successAppCount++;
                } catch (Throwable $e) {
                    Log::warning("Gagal kirim FCM pengajuan MCU ke NIK {$nik}. Error: " . $e->getMessage());
                    $failedRecipients[] = ['nik' => $nik, 'type' => 'FCM', 'reason' => substr($e->getMessage(), 0, 100)];
                }
            } else {
                 $failedRecipients[] = ['nik' => $nik, 'type' => 'FCM', 'reason' => 'FCM Token tidak tersedia.'];
            }
        }

        // --- 3. PEMBARUAN LOG ---
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

    public function failed(Throwable $exception)
    {
        Log::critical("Job ProcessSubmissionReminders GAGAL TOTAL. Log ID: {$this->notificationLog->id}. Error: " . $exception->getMessage());
    }
}