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
use Illuminate\Support\Facades\DB; // Digunakan untuk query mentah

class NotificationDashboard extends Component
{
    public $filterDate = 'tomorrow'; // 'tomorrow', 'specific', 'today', etc.
    public $specificDate;
    public $searchQuery = ''; // Properti baru untuk pencarian

    public $jadwalsToNotify;
    public $selectedRecipients = []; // Untuk kontrol checkbox

    // --- PROPERTI BARU ---
    public $notificationMode = 'scheduled'; // 'scheduled' atau 'submission'
    public $filterDepartemenId = '';
    public $departemenOptions; // Untuk dropdown Departemen

    public function mount()
    {
        // Muat data master
        $this->departemenOptions = Departemen::orderBy('nama_departemen')->get();

        // Tetapkan tanggal spesifik default
        if ($this->filterDate === 'tomorrow') {
            $this->specificDate = Carbon::tomorrow()->toDateString();
        } elseif ($this->filterDate === 'today') {
            $this->specificDate = Carbon::today()->toDateString();
        }

        $this->loadData();
    }
    
    // --- DISPATCHER UTAMA ---
    public function loadData()
    {
        if ($this->notificationMode === 'scheduled') {
            $this->loadScheduledJadwals();
        } 
        elseif ($this->notificationMode === 'submission') {
            $this->loadKaryawanForSubmission();
        }
    }

    // --- LOGIKA MODE 1: PENGINGAT JADWAL (SUDAH TERDAFTAR) ---
    public function loadScheduledJadwals()
    {
        $targetDate = null;
        $this->filterDepartemenId = ''; // Kosongkan filter Departemen di mode ini

        if ($this->filterDate === 'tomorrow') {
            $targetDate = Carbon::tomorrow()->toDateString();
        } elseif ($this->filterDate === 'today') {
            $targetDate = Carbon::today()->toDateString();
        } elseif ($this->filterDate === 'specific' && $this->specificDate) {
            $targetDate = Carbon::parse($this->specificDate)->toDateString();
        }
        
        if ($targetDate) {
            // Eager load relasi Pasien
            $query = JadwalMcu::with(['karyawan', 'pesertaMcu', 'karyawan.departemen']) 
                ->whereDate('tanggal_mcu', $targetDate)
                ->whereIn('status', ['Scheduled', 'Present']);

            // --- LOGIKA PENCARIAN ---
            if ($this->searchQuery) {
                $searchTerm = '%' . $this->searchQuery . '%';
                
                $query->where(function ($q) use ($searchTerm) {
                    // Cari di data lokal JadwalMcu (untuk non-karyawan/cadangan)
                    $q->where('nama_pasien', 'like', $searchTerm)
                      ->orWhere('nik_pasien', 'like', $searchTerm)
                      ->orWhere('no_sap', 'like', $searchTerm);
                    
                    // Cari di tabel Karyawan
                    $q->orWhereHas('karyawan', function ($qKar) use ($searchTerm) {
                        $qKar->where('nama_karyawan', 'like', $searchTerm)
                             ->orWhere('nik_karyawan', 'like', $searchTerm)
                             ->orWhere('no_sap', 'like', $searchTerm);
                    });

                    // Cari di tabel PesertaMcu
                    $q->orWhereHas('pesertaMcu', function ($qPes) use ($searchTerm) {
                        $qPes->where('nama_lengkap', 'like', $searchTerm)
                             ->orWhere('nik_pasien', 'like', $searchTerm);
                    });
                });
            }
            
            $this->jadwalsToNotify = $query->get();
        } else {
            $this->jadwalsToNotify = collect();
        }

        $this->selectedRecipients = $this->jadwalsToNotify->pluck('id')->toArray();
    }

    // --- LOGIKA MODE 2: PENGINGAT PENGAJUAN (BELUM TERDAFTAR) ---
    public function loadKaryawanForSubmission()
    {
        $this->jadwalsToNotify = collect(); 
        
        // HANYA EKSEKUSI JIKA DEPARTEMEN SUDAH DIPILIH
        if (!$this->filterDepartemenId) {
            return; 
        }
        
        $karyawanQuery = Karyawan::with('departemen')
                                 ->where('departemens_id', $this->filterDepartemenId);
        
        // 1. Filter KRITIS: Karyawan yang BELUM memiliki Jadwal MCU terbaru
        // Logika: Cari ID Karyawan yang sudah memiliki jadwal dalam 1 tahun terakhir.
        $recentJadwalKaryawanIds = JadwalMcu::whereNotNull('karyawan_id')
            ->whereDate('tanggal_mcu', '>=', Carbon::now()->subYears(1)) 
            ->pluck('karyawan_id')
            ->toArray();

        // 2. Ambil karyawan yang ID-nya TIDAK ADA dalam daftar jadwal terbaru
        $karyawanQuery->whereNotIn('id', $recentJadwalKaryawanIds);

        // --- Logika Pencarian ---
        if ($this->searchQuery) {
            $searchTerm = '%' . $this->searchQuery . '%';
            $karyawanQuery->where(function ($q) use ($searchTerm) {
                $q->where('nama_karyawan', 'like', $searchTerm)
                  ->orWhere('no_sap', 'like', $searchTerm)
                  ->orWhere('nik_karyawan', 'like', $searchTerm);
            });
        }

        // Hasilnya adalah koleksi Model Karyawan
        $this->jadwalsToNotify = $karyawanQuery->get();
        
        // Set ID Karyawan sebagai yang dipilih
        $this->selectedRecipients = $this->jadwalsToNotify->pluck('id')->toArray();
    }

    public function updatedFilterDate()
    {
        $this->loadData();
    }
    
    public function updatedSpecificDate()
    {
        $this->loadData();
    }

    public function updatedSearchQuery()
    {
        $this->loadData();
    }

    public function updatedNotificationMode()
    {
        // Reset filter departemen saat mode berubah
        $this->filterDepartemenId = ''; 
        $this->loadData();
    }

    public function updatedFilterDepartemenId()
    {
        $this->loadData();
    }

    // --- FUNGSI PENGIRIMAN ---
    public function sendNotifications()
    {
        $recipientsCount = count($this->selectedRecipients);
        if ($recipientsCount === 0) {
            session()->flash('error', 'Tidak ada karyawan yang dipilih.');
            return;
        }

        // 1. Buat Log Awal
        $log = \App\Models\NotificationLog::create([
            'scheduled_date' => $this->specificDate ?? Carbon::now()->toDateString(),
            'mode' => 'manual',
            'total_targets' => $recipientsCount,
            'admin_users_id' => Auth::id(), // Pastikan Auth::id() mendapatkan ID admin
        ]);
        
        // 2. Dispatch Job sesuai Mode
        if ($this->notificationMode === 'scheduled') {
            // Mengirim pengingat jadwal (target ID JadwalMcu)
            ProcessMcuReminders::dispatch($this->selectedRecipients, $log);
            session()->flash('message', "{$recipientsCount} pengingat jadwal sedang diproses.");
        } 
        elseif ($this->notificationMode === 'submission') {
            // Mengirim pengingat pengajuan (target ID Karyawan)
            ProcessSubmissionReminders::dispatch($this->selectedRecipients, $log);
            session()->flash('message', "{$recipientsCount} pengingat pengajuan jadwal sedang diproses.");
        }

        $this->reset(['selectedRecipients', 'searchQuery']);
        $this->loadData();
    }

    public function render()
    {
        // Tetap panggil loadJadwals di sini untuk memastikan data terbaru
        $this->loadData();

        return view('livewire.notification-dashboard')
        ->layout('layouts.app');
    }
}
