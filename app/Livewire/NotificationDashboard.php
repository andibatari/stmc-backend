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
    public array $selectedRecipients = [];
    public $selectAll = false;
    public $notificationMode = 'scheduled'; 
    public $filterDepartemenId = '';
    public $departemenOptions; 
    public $filterUnitKerjaId = '';
    public $unitKerjaOptions = [];

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
    // FUNGSI PENCARIAN (GABUNGAN UNTUK BROADCAST)
    // ==========================================
    public function updatedSearchEmployeeQuery()
    {
        if (strlen($this->searchEmployeeQuery) >= 2) {
            $karyawans = Karyawan::where('nama_karyawan', 'like', '%' . $this->searchEmployeeQuery . '%')
                ->orWhere('no_sap', 'like', '%' . $this->searchEmployeeQuery . '%')
                ->take(5)->get()->map(function($k) {
                    return ['id' => 'K_' . $k->id, 'name' => $k->nama_karyawan, 'sap' => $k->no_sap ?? '-'];
                });

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

        $uniqueTokens = $targets->pluck('fcm_token')->filter()->unique();

        foreach ($uniqueTokens as $token) {
            $statusFCM = \App\Services\FCMService::sendPushNotification(
                $token, 
                $this->broadcastTitle, 
                $this->broadcastMessage, 
                $this->broadcastLink
            );
            
            if ($statusFCM) {
                $fcmSuccessCount++;
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
    // FUNGSI TAB 2: PENGINGAT MCU (KEMBALI KE KARYAWAN ONLY)
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
            // Revert: Hanya narik Karyawan
            $query = JadwalMcu::with(['karyawan', 'karyawan.departemen']) 
                ->whereDate('tanggal_mcu', $targetDate)
                ->whereIn('status', ['Scheduled', 'Present']);

            if ($this->searchQuery) {
                $searchTerm = '%' . $this->searchQuery . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('nama_pasien', 'like', $searchTerm)
                      ->orWhere('no_sap', 'like', $searchTerm)
                      ->orWhereHas('karyawan', function ($qKar) use ($searchTerm) {
                          $qKar->where('nama_karyawan', 'like', $searchTerm)->orWhere('no_sap', 'like', $searchTerm);
                      });
                });
            }

            $this->jadwalsToNotify = $query->get();
        } else {
            $this->jadwalsToNotify = collect(); 
        }

        $this->selectedRecipients = $this->jadwalsToNotify->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selectAll = $this->jadwalsToNotify->count() > 0;
    }

    public function loadKaryawanForSubmission()
    {
        $this->jadwalsToNotify = collect(); 
        if (!$this->filterDepartemenId) return; 
        
        $karyawanQuery = Karyawan::with('departemen')->where('departemens_id', $this->filterDepartemenId);

        // ---- Tambahan Query Unit Kerja ----
        if ($this->filterUnitKerjaId) {
            $karyawanQuery->where('unit_kerja_id', $this->filterUnitKerjaId); // Sesuaikan dengan nama kolom di database kamu
        }
        // ------------------------------------

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
        $this->selectedRecipients = $this->jadwalsToNotify->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selectAll = $this->jadwalsToNotify->count() > 0;
    }

    public function updatedFilterDate() { $this->loadData(); }
    public function updatedSpecificDate() { $this->loadData(); }
    public function updatedSearchQuery() { $this->loadData(); }
    // 2. Tambahkan metode untuk me-reset unit kerja ketika departemen diganti
    public function updatedFilterDepartemenId() 
    { 
        $this->filterUnitKerjaId = '';
        
        // (Opsional) Jika kamu punya Model UnitKerja yang berelasi dengan Departemen:
        // $this->unitKerjaOptions = \App\Models\UnitKerja::where('departemen_id', $this->filterDepartemenId)->get();
        
        $this->loadData(); 
    }

    // 3. Tambahkan trigger saat unit kerja berubah
    public function updatedFilterUnitKerjaId() { $this->loadData(); }

    // 4. Update method updatedNotificationMode() untuk reset Unit Kerja juga
    public function updatedNotificationMode() { 
        $this->filterDepartemenId = ''; 
        $this->filterUnitKerjaId = ''; // Tambahan
        $this->unitKerjaOptions = [];  // Tambahan
        $this->loadData(); 
    }

    public function sendNotifications()
    {
        $recipientsCount = count($this->selectedRecipients);
        if ($recipientsCount === 0) {
            session()->flash('error', 'Tidak ada karyawan yang dipilih.');
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

    // 🌟 FUNGSI BARU 1: Otomatis jalan saat Checkbox Master diklik
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedRecipients = $this->jadwalsToNotify->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedRecipients = [];
        }
    }

    // 🌟 FUNGSI BARU 2: Otomatis jalan saat Checkbox Karyawan diklik satu-satu
    public function updatedSelectedRecipients()
    {
        // Mengecek apakah jumlah karyawan yang dicentang sama dengan total data
        $this->selectAll = count($this->selectedRecipients) === $this->jadwalsToNotify->count() && $this->jadwalsToNotify->count() > 0;
    }

    public function render()
    {
        return view('livewire.notification-dashboard')->layout('layouts.app');
    }
}