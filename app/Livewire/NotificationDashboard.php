<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\JadwalMcu;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Jobs\ProcessMcuReminders;
use App\Jobs\ProcessSubmissionReminders;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationDashboard extends Component
{
    // ==========================================
    // PROPERTI TAB 1: PENGUMUMAN BEBAS (BROADCAST)
    // ==========================================
    public $broadcastTitle = '';
    public $broadcastMessage = '';
    public $broadcastTargetType = 'all'; 
    public $broadcastTargetDeptId = '';
    
    // Properti Baru untuk Pencarian Multi-Karyawan
    public $searchEmployeeQuery = '';
    public $employeeSearchResults = [];
    public $selectedIndividualEmployees = []; // Format: [['id'=>1, 'name'=>'Budi', 'sap'=>'123']]

    // ==========================================
    // PROPERTI TAB 2: PENGINGAT MCU 
    // ==========================================
    public $filterDate = 'tomorrow'; 
    public $specificDate;
    public $searchQuery = ''; 
    public $jadwalsToNotify;
    public $selectedRecipients = []; 
    public $notificationMode = 'scheduled'; 
    public $filterDepartemenId = '';
    public $departemenOptions; 

    public function mount()
    {
        $this->departemenOptions = Departemen::orderBy('nama_departemen')->get();

        if ($this->filterDate === 'tomorrow') {
            $this->specificDate = Carbon::tomorrow()->toDateString();
        } elseif ($this->filterDate === 'today') {
            $this->specificDate = Carbon::today()->toDateString();
        }

        $this->loadData();
    }

    // ==========================================
    // FUNGSI PENCARIAN & PILIH KARYAWAN INDIVIDU
    // ==========================================
    public function updatedSearchEmployeeQuery()
    {
        // Hanya mencari jika diketik minimal 2 huruf agar database tidak terbebani
        if (strlen($this->searchEmployeeQuery) >= 2) {
            $this->employeeSearchResults = Karyawan::where('nama_karyawan', 'like', '%' . $this->searchEmployeeQuery . '%')
                ->orWhere('no_sap', 'like', '%' . $this->searchEmployeeQuery . '%')
                ->take(10) // Batasi 10 hasil teratas agar ringan
                ->get();
        } else {
            $this->employeeSearchResults = [];
        }
    }

    public function addEmployeeToBroadcast($id, $nama, $sap)
    {
        // Cek agar tidak memasukkan orang yang sama dua kali
        $exists = collect($this->selectedIndividualEmployees)->contains('id', $id);
        if (!$exists) {
            $this->selectedIndividualEmployees[] = [
                'id' => $id,
                'name' => $nama,
                'sap' => $sap
            ];
        }
        
        // Reset kolom pencarian setelah diklik
        $this->searchEmployeeQuery = '';
        $this->employeeSearchResults = [];
    }

    public function removeEmployeeFromBroadcast($id)
    {
        // Hapus karyawan dari daftar pilihan
        $this->selectedIndividualEmployees = array_values(collect($this->selectedIndividualEmployees)
            ->reject(function ($emp) use ($id) {
                return $emp['id'] == $id;
            })->toArray());
    }

    public function updatedBroadcastTargetType()
    {
        // Reset pilihan jika tipe target diubah
        $this->broadcastTargetDeptId = '';
        $this->searchEmployeeQuery = '';
        $this->employeeSearchResults = [];
        $this->selectedIndividualEmployees = [];
    }

    // ==========================================
    // FUNGSI KIRIM BROADCAST PENGUMUMAN
    // ==========================================
    public function sendBroadcast()
    {
        // 1. Validasi Input
        $this->validate([
            'broadcastTitle' => 'required|string|max:255',
            'broadcastMessage' => 'required|string',
            'broadcastTargetType' => 'required|in:all,dept,individual',
        ]);

        $targets = collect();
        $totalTarget = 0;

        // 2. Tentukan Siapa Targetnya
        if ($this->broadcastTargetType === 'all') {
            $targets = \App\Models\Karyawan::whereNotNull('fcm_token')->get();
        } elseif ($this->broadcastTargetType === 'dept') {
            $this->validate(['broadcastTargetDeptId' => 'required']);
            $targets = \App\Models\Karyawan::where('departemen_id', $this->broadcastTargetDeptId)
                                           ->whereNotNull('fcm_token')->get();
        } elseif ($this->broadcastTargetType === 'individual') {
            $this->validate(['selectedIndividualEmployees' => 'required|array|min:1']);
            // Ambil ID dari array pilihan
            $ids = array_column($this->selectedIndividualEmployees, 'id');
            $targets = \App\Models\Karyawan::whereIn('id', $ids)->get();
        }

        $fcmSuccessCount = 0;
        $totalTarget = $targets->count();

        if ($totalTarget === 0) {
            session()->flash('error', 'Gagal dikirim: Target tidak ditemukan atau belum memiliki Token HP (Belum login di Aplikasi).');
            return;
        }

        // 3. Tembakkan Notifikasi ke HP Karyawan (Satu per satu)
        foreach ($targets as $karyawan) {
            if (!empty($karyawan->fcm_token)) {
                $statusFCM = \App\Services\FCMService::sendPushNotification(
                    $karyawan->fcm_token,
                    $this->broadcastTitle,
                    $this->broadcastMessage
                );

                if ($statusFCM) {
                    $fcmSuccessCount++;
                }
            }
        }

        // 4. Catat ke Tabel Riwayat
        \App\Models\NotificationLog::create([
            'mode' => 'broadcast',
            'scheduled_date' => now(), // Broadcast tidak punya target tanggal
            'total_targets' => $totalTarget,
            'email_success' => 0,
            'fcm_success' => $fcmSuccessCount,
            'admin_id' => auth()->id() ?? 1,
        ]);

        // 5. Bersihkan Form dan Tampilkan Pesan Sukses
        session()->flash('message', "Broadcast Selesai! Berhasil terkirim ke $fcmSuccessCount perangkat HP.");
        $this->reset(['broadcastTitle', 'broadcastMessage', 'selectedIndividualEmployees', 'searchEmployeeQuery']);
        $this->broadcastTargetType = 'individual';
    }
    
    // ==========================================
    // FUNGSI TAB 2: PENGINGAT MCU (Tidak Diubah)
    // ==========================================
    public function loadData()
    {
        if ($this->notificationMode === 'scheduled') {
            $this->loadScheduledJadwals();
        } elseif ($this->notificationMode === 'submission') {
            $this->loadKaryawanForSubmission();
        }
    }

    public function loadScheduledJadwals()
    {
        $targetDate = null;
        $this->filterDepartemenId = ''; 

        if ($this->filterDate === 'tomorrow') $targetDate = Carbon::tomorrow()->toDateString();
        elseif ($this->filterDate === 'today') $targetDate = Carbon::today()->toDateString();
        elseif ($this->filterDate === 'specific' && $this->specificDate) $targetDate = Carbon::parse($this->specificDate)->toDateString();
        
        if ($targetDate) {
            $query = JadwalMcu::with(['karyawan', 'pesertaMcu', 'karyawan.departemen']) 
                ->whereDate('tanggal_mcu', $targetDate)
                ->whereIn('status', ['Scheduled', 'Present']);

            if ($this->searchQuery) {
                $searchTerm = '%' . $this->searchQuery . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('nama_pasien', 'like', $searchTerm)
                      ->orWhere('nik_pasien', 'like', $searchTerm)
                      ->orWhere('no_sap', 'like', $searchTerm)
                      ->orWhereHas('karyawan', function ($qKar) use ($searchTerm) {
                          $qKar->where('nama_karyawan', 'like', $searchTerm)->orWhere('no_sap', 'like', $searchTerm);
                      })
                      ->orWhereHas('pesertaMcu', function ($qPes) use ($searchTerm) {
                          $qPes->where('nama_lengkap', 'like', $searchTerm)->orWhere('nik_pasien', 'like', $searchTerm);
                      });
                });
            }
            $this->jadwalsToNotify = $query->get();
        } else {
            $this->jadwalsToNotify = collect();
        }

        $this->selectedRecipients = $this->jadwalsToNotify->pluck('id')->toArray();
    }

    public function loadKaryawanForSubmission()
    {
        $this->jadwalsToNotify = collect(); 
        if (!$this->filterDepartemenId) return; 
        
        $karyawanQuery = Karyawan::with('departemen')->where('departemens_id', $this->filterDepartemenId);
        
        $recentJadwalKaryawanIds = JadwalMcu::whereNotNull('karyawan_id')
            ->whereDate('tanggal_mcu', '>=', Carbon::now()->subYears(1)) 
            ->pluck('karyawan_id')
            ->toArray();

        $karyawanQuery->whereNotIn('id', $recentJadwalKaryawanIds);

        if ($this->searchQuery) {
            $searchTerm = '%' . $this->searchQuery . '%';
            $karyawanQuery->where(function ($q) use ($searchTerm) {
                $q->where('nama_karyawan', 'like', $searchTerm)->orWhere('no_sap', 'like', $searchTerm);
            });
        }

        $this->jadwalsToNotify = $karyawanQuery->get();
        $this->selectedRecipients = $this->jadwalsToNotify->pluck('id')->toArray();
    }

    public function updatedFilterDate() { $this->loadData(); }
    public function updatedSpecificDate() { $this->loadData(); }
    public function updatedSearchQuery() { $this->loadData(); }
    public function updatedNotificationMode() { $this->filterDepartemenId = ''; $this->loadData(); }
    public function updatedFilterDepartemenId() { $this->loadData(); }

    public function sendNotifications()
    {
        $recipientsCount = count($this->selectedRecipients);
        if ($recipientsCount === 0) {
            session()->flash('error', 'Tidak ada karyawan yang dipilih.');
            return;
        }

        // Buat log awal dengan default 0
        $log = \App\Models\NotificationLog::create([
            'scheduled_date' => $this->specificDate ?? Carbon::now()->toDateString(),
            'mode' => 'manual',
            'total_targets' => $recipientsCount,
            'fcm_success' => 0, // Inisialisasi awal
            'email_success' => 0,
            'admin_users_id' => Auth::id(),
        ]);
        
        if ($this->notificationMode === 'scheduled') {
            // GANTI MENGGUNAKAN dispatchSync
            ProcessMcuReminders::dispatchSync($this->selectedRecipients, $log);
            session()->flash('message', "{$recipientsCount} pengingat jadwal berhasil dikirim.");
        } elseif ($this->notificationMode === 'submission') {
            // GANTI MENGGUNAKAN dispatchSync
            ProcessSubmissionReminders::dispatchSync($this->selectedRecipients, $log);
            session()->flash('message', "{$recipientsCount} pengingat pengajuan jadwal berhasil dikirim.");
        }

        $this->reset(['selectedRecipients', 'searchQuery']);
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.notification-dashboard')->layout('layouts.app');
    }
}