<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\JadwalMcu;
use App\Models\Karyawan;
use App\Models\PesertaMcu;
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
    public $broadcastLink = ''; 
    
    public $searchEmployeeQuery = '';
    public $employeeSearchResults = [];
    public $selectedIndividualEmployees = []; 

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
    // FUNGSI PENCARIAN (GABUNGAN KARYAWAN & PASIEN UMUM)
    // ==========================================
    public function updatedSearchEmployeeQuery()
    {
        if (strlen($this->searchEmployeeQuery) >= 2) {
            // Cari Karyawan
            $karyawans = Karyawan::where('nama_karyawan', 'like', '%' . $this->searchEmployeeQuery . '%')
                ->orWhere('no_sap', 'like', '%' . $this->searchEmployeeQuery . '%')
                ->take(5)->get()->map(function($k) {
                    return ['id' => 'K_' . $k->id, 'name' => $k->nama_karyawan, 'sap' => $k->no_sap ?? '-'];
                });

            // Cari Pasien Umum / Keluarga
            $pesertas = PesertaMcu::where('nama_lengkap', 'like', '%' . $this->searchEmployeeQuery . '%')
                ->orWhere('nik_pasien', 'like', '%' . $this->searchEmployeeQuery . '%')
                ->take(5)->get()->map(function($p) {
                    return ['id' => 'P_' . $p->id, 'name' => $p->nama_lengkap, 'sap' => $p->no_sap ?? $p->nik_pasien ?? '-'];
                });

            $this->employeeSearchResults = $karyawans->concat($pesertas)->toArray();
        } else {
            $this->employeeSearchResults = [];
        }
    }

    public function addEmployeeToBroadcast($id, $nama, $sap)
    {
        $exists = collect($this->selectedIndividualEmployees)->contains('id', $id);
        if (!$exists) {
            $this->selectedIndividualEmployees[] = [
                'id' => $id,
                'name' => $nama,
                'sap' => $sap
            ];
        }
        $this->searchEmployeeQuery = '';
        $this->employeeSearchResults = [];
    }

    public function removeEmployeeFromBroadcast($id)
    {
        $this->selectedIndividualEmployees = array_values(collect($this->selectedIndividualEmployees)
            ->reject(function ($emp) use ($id) { return $emp['id'] == $id; })->toArray());
    }

    public function updatedBroadcastTargetType()
    {
        $this->broadcastTargetDeptId = '';
        $this->searchEmployeeQuery = '';
        $this->employeeSearchResults = [];
        $this->selectedIndividualEmployees = [];
    }

    // ==========================================
    // FUNGSI KIRIM BROADCAST
    // ==========================================
    public function sendBroadcast()
    {
        $this->validate([
            'broadcastTitle' => 'required|string|max:255',
            'broadcastMessage' => 'required|string',
            'broadcastTargetType' => 'required|in:all,dept,individual',
            'broadcastLink' => 'nullable|url', 
        ]);

        $targets = collect();

        // 2. Tentukan Siapa Targetnya (Gabungan)
        if ($this->broadcastTargetType === 'all') {
            $karyawanT = Karyawan::whereNotNull('fcm_token')->get();
            $pesertaT = PesertaMcu::whereNotNull('fcm_token')->get();
            $targets = $karyawanT->concat($pesertaT);
            
        } elseif ($this->broadcastTargetType === 'dept') {
            $this->validate(['broadcastTargetDeptId' => 'required']);
            $targets = Karyawan::where('departemens_id', $this->broadcastTargetDeptId)
                               ->whereNotNull('fcm_token')->get();
                               
        } elseif ($this->broadcastTargetType === 'individual') {
            $this->validate(['selectedIndividualEmployees' => 'required|array|min:1']);
            foreach ($this->selectedIndividualEmployees as $emp) {
                $parts = explode('_', $emp['id']);
                if ($parts[0] === 'K') {
                    $targets->push(Karyawan::find($parts[1]));
                } else {
                    $targets->push(PesertaMcu::find($parts[1]));
                }
            }
            $targets = $targets->filter(fn($t) => $t != null && !empty($t->fcm_token));
        }

        $fcmSuccessCount = 0;
        $totalTarget = $targets->count();

        if ($totalTarget === 0) {
            session()->flash('error', 'Gagal: Target tidak ditemukan atau belum pernah login di Aplikasi (Token Kosong).');
            return;
        }

        foreach ($targets as $userTarget) {
            if (!empty($userTarget->fcm_token)) {
                $statusFCM = \App\Services\FCMService::sendPushNotification(
                    $userTarget->fcm_token, $this->broadcastTitle, $this->broadcastMessage, $this->broadcastLink
                );
                if ($statusFCM) $fcmSuccessCount++;
            }
        }

        \App\Models\NotificationLog::create([
            'mode' => 'broadcast',
            'scheduled_date' => now(),
            'total_targets' => $totalTarget,
            'email_success' => 0,
            'fcm_success' => $fcmSuccessCount,
            'admin_id' => auth()->id() ?? 1,
        ]);

        session()->flash('message', "Broadcast Selesai! Berhasil terkirim ke $fcmSuccessCount perangkat HP.");
        $this->reset(['broadcastTitle', 'broadcastMessage', 'broadcastLink', 'selectedIndividualEmployees', 'searchEmployeeQuery']);
        $this->broadcastTargetType = 'individual';
    }
    
    // ==========================================
    // FUNGSI TAB 2: PENGINGAT MCU 
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
        
        $recentKaryawanIds = JadwalMcu::whereNotNull('karyawan_id')->whereDate('tanggal_mcu', '>=', Carbon::now()->subYears(1))->pluck('karyawan_id')->toArray();
        $recentPesertaIds = JadwalMcu::whereNotNull('peserta_mcus_id')->whereDate('tanggal_mcu', '>=', Carbon::now()->subYears(1))->pluck('peserta_mcus_id')->toArray();

        // 1. Tarik Data Karyawan
        $karyawanQuery = Karyawan::with('departemen')->whereNotIn('id', $recentKaryawanIds);
        if ($this->filterDepartemenId) $karyawanQuery->where('departemens_id', $this->filterDepartemenId);
        if ($this->searchQuery) {
            $searchTerm = '%' . $this->searchQuery . '%';
            $karyawanQuery->where(function ($q) use ($searchTerm) {
                $q->where('nama_karyawan', 'like', $searchTerm)->orWhere('no_sap', 'like', $searchTerm);
            });
        }
        $karyawans = $karyawanQuery->get()->map(function($k) {
            $k->target_id = 'K_' . $k->id; // ID Unik Karyawan
            return $k;
        });

        // 2. Tarik Data Pasien Umum (Hanya jika "Semua Dept" dipilih)
        $pesertas = collect();
        if (empty($this->filterDepartemenId)) {
            $pesertaQuery = PesertaMcu::whereNotIn('id', $recentPesertaIds);
            if ($this->searchQuery) {
                $searchTerm = '%' . $this->searchQuery . '%';
                $pesertaQuery->where(function ($q) use ($searchTerm) {
                    $q->where('nama_lengkap', 'like', $searchTerm)->orWhere('nik_pasien', 'like', $searchTerm);
                });
            }
            $pesertas = $pesertaQuery->get()->map(function($p) {
                $p->target_id = 'P_' . $p->id; // ID Unik Peserta Umum
                return $p;
            });
        }

        $this->jadwalsToNotify = $karyawans->concat($pesertas);
        $this->selectedRecipients = $this->jadwalsToNotify->pluck('target_id')->toArray();
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
            session()->flash('error', 'Tidak ada pasien yang dipilih.');
            return;
        }

        $log = \App\Models\NotificationLog::create([
            'scheduled_date' => $this->specificDate ?? Carbon::now()->toDateString(),
            'mode' => 'manual',
            'total_targets' => $recipientsCount,
            'fcm_success' => 0, 
            'email_success' => 0,
            'admin_id' => Auth::id() ?? 1,
        ]);
        
        if ($this->notificationMode === 'scheduled') {
            ProcessMcuReminders::dispatchSync($this->selectedRecipients, $log);
            session()->flash('message', "{$recipientsCount} pengingat jadwal berhasil dikirim.");
        } elseif ($this->notificationMode === 'submission') {
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