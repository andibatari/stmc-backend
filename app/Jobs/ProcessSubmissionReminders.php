<?php

namespace App\Jobs;

use App\Models\Karyawan;
use App\Models\PesertaMcu;
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

    protected $recipientIds; // Diubah namanya agar lebih umum (Karyawan & Peserta)
    protected $notificationLog;

    public function __construct(array $recipientIds, $log)
    {
        $this->recipientIds = $recipientIds;
        $this->notificationLog = $log;
    }

    public function handle()
    {
        $successEmailCount = 0;
        $successAppCount = 0;
        $failedRecipients = [];

        foreach ($this->recipientIds as $targetId) {
            // 🌟 1. MEMBELAH ID (Contoh: "K_1" menjadi "K" dan "1")
            $parts = explode('_', $targetId);
            
            // Keamanan tambahan jika format ID tidak sesuai
            if (count($parts) !== 2) continue; 
            
            $type = $parts[0];
            $id = $parts[1];

            $userTarget = null;
            $email = null;
            $fcmToken = null;
            $nik = 'N/A';

            // 🌟 2. AMBIL DATA BERDASARKAN TIPE PENGGUNA
            if ($type === 'K') {
                $userTarget = Karyawan::with('departemen')->find($id);
                if ($userTarget) {
                    $email = $userTarget->email_karyawan ?? $userTarget->email ?? null;
                    $fcmToken = $userTarget->fcm_token ?? null;
                    $nik = $userTarget->nik_karyawan ?? 'N/A';
                }
            } elseif ($type === 'P') {
                $userTarget = PesertaMcu::find($id);
                if ($userTarget) {
                    $email = $userTarget->email ?? null;
                    $fcmToken = $userTarget->fcm_token ?? null;
                    $nik = $userTarget->nik_pasien ?? 'N/A';
                }
            }

            // Jika akun sudah terhapus di database
            if (!$userTarget) {
                $failedRecipients[] = ['nik' => $targetId, 'type' => 'All', 'reason' => 'Data pengguna tidak ditemukan di database.'];
                continue;
            }

            // --- 3. PROSES PENGIRIMAN EMAIL ---
            if ($email) {
                try {
                    Mail::to($email)->send(new SubmissionReminderMail($userTarget, $this->notificationLog->scheduled_date));
                    $successEmailCount++;
                } catch (Throwable $e) {
                    Log::warning("Gagal kirim EMAIL pengajuan MCU ke NIK {$nik}. Error: " . $e->getMessage());
                    $failedRecipients[] = ['nik' => $nik, 'type' => 'Email', 'reason' => substr($e->getMessage(), 0, 100)];
                }
            } else {
                $failedRecipients[] = ['nik' => $nik, 'type' => 'Email', 'reason' => 'Alamat email tidak tersedia.'];
            }

            // --- 4. PROSES PENGIRIMAN NOTIFIKASI APLIKASI (FCM) ---
            if ($fcmToken) {
                try {
                    // Buat pesan ajakan yang unik dan menarik
                    $nama = $userTarget->nama_karyawan ?? $userTarget->nama_lengkap ?? 'Karyawan';
                    
                    $title = "📢 Yuk, Atur Jadwal MCU Kamu!";
                    $body = "Halo {$nama}! 👋\n\n"
                          . "Kami perhatikan kamu belum menentukan jadwal Medical Check Up di Klinik STMC. "
                          . "Jika kebetulan besok kamu ada waktu kosong, yuk segera ajukan jadwal MCU kamu sekarang!\n\n"
                          . "Kesehatanmu adalah prioritas kami. 🏥✨\n"
                          . "Klik tombol di bawah ini untuk memilih jadwal ya! 👇";
                          
                    // Sesuaikan actionLink ini dengan route/halaman pengajuan di aplikasi Android kamu
                    $actionLink = 'route:/pengajuan-mcu'; 

                    // Kirim notifikasi menggunakan FCM Service
                    $statusFCM = \App\Services\FCMService::sendPushNotification(
                        $fcmToken,
                        $title,
                        $body,
                        $actionLink
                    );

                    if ($statusFCM) {
                        $successAppCount++;
                    }

                    // Opsional: Jika kamu tetap butuh menjalankan notifikasi bawaan Laravel untuk simpan ke database
                    // $userTarget->notify(new McuSubmissionNotification($userTarget, $this->notificationLog->scheduled_date));
                    
                } catch (Throwable $e) {
                    Log::warning("Gagal kirim FCM pengajuan MCU ke NIK {$nik}. Error: " . $e->getMessage());
                    $failedRecipients[] = ['nik' => $nik, 'type' => 'FCM', 'reason' => substr($e->getMessage(), 0, 100)];
                }
            } else {
                 $failedRecipients[] = ['nik' => $nik, 'type' => 'FCM', 'reason' => 'FCM Token tidak tersedia.'];
            }
        }

        // --- 5. PEMBARUAN LOG ---
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