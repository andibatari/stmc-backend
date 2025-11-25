<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Karyawan; // Target: Model Karyawan
use App\Models\NotificationLog;
use App\Mail\SubmissionReminderMail; // Mailable baru
use App\Notifications\McuSubmissionNotification; // Notifikasi baru
use Illuminate\Support\Facades\Mail;
use Exception;

class ProcessSubmissionReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $karyawanIds;
    protected $log;

    public function __construct($karyawanIds, $log)
    {
        $this->karyawanIds = $karyawanIds;
        $this->log = $log;
    }

    public function handle()
    {
        $emailSuccessCount = 0;
        $fcmSuccessCount = 0;
        $failedRecipients = [];
        $recipients = Karyawan::whereIn('id', $this->karyawanIds)->get();

        foreach ($recipients as $karyawan) {
            
            // A. Kirim Email (Gunakan Mailable baru)
            try {
                Mail::to($karyawan->email_karyawan)->queue(new SubmissionReminderMail($karyawan, $this->log->scheduled_date));
                $emailSuccessCount++;
            } catch (Exception $e) {
                $failedRecipients[] = ['nik' => $karyawan->nik_karyawan, 'type' => 'Email', 'reason' => $e->getMessage()];
            }

            // B. Kirim FCM (Gunakan Notifikasi baru)
            if ($karyawan->fcm_token) {
                try {
                    $karyawan->notify(new McuSubmissionNotification($karyawan, $this->log->scheduled_date));
                    $fcmSuccessCount++;
                } catch (Exception $e) {
                    $failedRecipients[] = ['nik' => $karyawan->nik_karyawan, 'type' => 'FCM', 'reason' => $e->getMessage()];
                }
            } else {
                 $failedRecipients[] = ['nik' => $karyawan->nik_karyawan, 'type' => 'FCM', 'reason' => 'FCM Token tidak tersedia'];
            }
        }

        // C. Update Log
        $this->log->update([
            'email_success' => $emailSuccessCount,
            'fcm_success' => $fcmSuccessCount,
            'failed_recipients' => $failedRecipients,
        ]);
    }
}