<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination; // Wajib untuk tabel di bawah
use App\Models\Departemen;
use App\Models\UnitKerja;
use App\Models\JadwalMcu;
use App\Exports\PemeriksaanExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportPemeriksaan extends Component
{
    use WithPagination; // Mengaktifkan fitur pagination/halaman

    // --- Properti Panel Atas (Ekspor Excel) ---
    public $departemens_id = '';
    public $tipe_anggota = '';
    public $status_kehadiran = '';
    public $date_start;
    public $date_end;
    public $total_preview = 0;

    // --- Properti Panel Bawah (Tabel PDF) ---
    public $searchTable = '';
    public $tableDept = '';
    public $tableUnit = '';

    public function mount()
    {
        // Default rentang tanggal (Awal tahun sampai hari ini)
        $this->date_start = now()->startOfYear()->format('Y-m-d');
        $this->date_end = now()->format('Y-m-d');
        $this->hitungPreview();
    }

    // Reset ke halaman 1 setiap kali user mengetik pencarian di tabel bawah
    public function updatingSearchTable() { $this->resetPage(); }
    public function updatingTableDept() { 
        $this->resetPage(); 
        $this->tableUnit = ''; 
    }
    public function updatingTableUnit() { $this->resetPage(); }

    // Otomatis hitung ulang saat filter di panel Atas diubah
    public function updated($property)
    {
        $exportProperties = ['date_start', 'date_end', 'departemens_id', 'tipe_anggota', 'status_kehadiran'];
        if (in_array($property, $exportProperties)) {
            $this->hitungPreview();
        }
    }

    // LOGIKA LAMA KAMU YANG SUDAH DILENGKAPI
    public function hitungPreview()
    {
        $query = JadwalMcu::query()
            ->leftJoin('peserta_mcus', 'jadwal_mcus.peserta_mcus_id', '=', 'peserta_mcus.id')
            ->leftJoin('karyawans', 'jadwal_mcus.karyawan_id', '=', 'karyawans.id');

        // 1. Filter Rentang Tanggal
        if ($this->date_start && $this->date_end) {
            $query->whereBetween('jadwal_mcus.tanggal_mcu', [
                $this->date_start . ' 00:00:00', 
                $this->date_end . ' 23:59:59'
            ]);
        }

        // 2. Filter Status Kehadiran
        if ($this->status_kehadiran) {
            $query->where('jadwal_mcus.status', $this->status_kehadiran);
        }

        // 3. LOGIKA KHUSUS: Filter Departemen & Kategori
        if ($this->departemens_id) {
            // Jika memilih departemen (termasuk HC), PAKSA hanya ambil Karyawan
            $query->whereNotNull('jadwal_mcus.karyawan_id')
                  ->where('karyawans.departemens_id', $this->departemens_id);
            
            // Reset kategori peserta karena departemen hanya untuk karyawan
            $this->tipe_anggota = ''; 
        } elseif ($this->tipe_anggota) {
            // Jika Departemen kosong tapi Kategori diisi (misal: Non-Karyawan)
            $query->where('peserta_mcus.tipe_anggota', $this->tipe_anggota);
        } else {
            // Jika SEMUA KOSONG (Semua Departemen & Semua Kategori)
            // Default: Tampilkan hanya data Karyawan
            $query->whereNotNull('jadwal_mcus.karyawan_id');
        }

        $this->total_preview = $query->count();
    }

    public function exportExcel()
    {
        $filters = [
            'departemens_id'   => $this->departemens_id,
            'tipe_anggota'     => $this->tipe_anggota,
            'status_kehadiran' => $this->status_kehadiran,
            'date_start'       => $this->date_start,
            'date_end'         => $this->date_end,
        ];

        $nama_file = 'Laporan_MCU_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new PemeriksaanExport($filters), $nama_file);
    }

    public function render()
    {
        $statusLabels = [
            'Scheduled' => 'Terjadwal',
            'Present'   => 'Hadir / Proses',
            'Finished'  => 'Selesai',
            'Canceled'  => 'Dibatalkan'
        ];

        // 1. Ambil List Unit Kerja HANYA JIKA Departemen dipilih
        $listUnitKerja = collect();
        if ($this->tableDept) {
            $listUnitKerja = UnitKerja::where('departemens_id', $this->tableDept)->orderBy('nama_unit_kerja')->get();
        }

        // ==========================================
        // QUERY UNTUK TABEL BAWAH (UNDUH PDF)
        // ==========================================
        $queryTable = JadwalMcu::with(['karyawan.departemen', 'pesertaMcu']);

        // Filter Pencarian (Cari Nama, No SAP, atau NIK)
        if ($this->searchTable) {
            $search = '%' . $this->searchTable . '%';
            $queryTable->where(function($q) use ($search) {
                $q->where('nama_pasien', 'like', $search)
                  ->orWhereHas('karyawan', function($k) use ($search) {
                      $k->where('nama_karyawan', 'like', $search)
                        ->orWhere('no_sap', 'like', $search)
                        ->orWhere('nik_karyawan', 'like', $search);
                  })
                  ->orWhereHas('pesertaMcu', function($p) use ($search) {
                      $p->where('nama_lengkap', 'like', $search)
                        ->orWhere('nik_pasien', 'like', $search);
                  });
            });
        }

        // Filter Tabel Departemen
        if ($this->tableDept) {
            $queryTable->whereHas('karyawan', function($q) {
                $q->where('departemens_id', $this->tableDept);
            });
        }

        // Filter Tabel Unit Kerja
        if ($this->tableUnit) {
            $queryTable->whereHas('karyawan', function($q) {
                $q->where('unit_kerjas_id', $this->tableUnit);
            });
        }

        return view('livewire.admin.export-pemeriksaan', [
            'listDepartemen' => Departemen::orderBy('nama_departemen')->get(),
            'listKategori'   => \App\Models\PesertaMcu::distinct()->pluck('tipe_anggota')->filter(),
            'statusLabels'   => $statusLabels,
            'listUnitKerja'  => $listUnitKerja,
            // Mengirim data jadwal untuk tabel bawah, dilimit 10 baris per halaman
            'jadwalTable'    => $queryTable->orderBy('tanggal_mcu', 'desc')->paginate(10)
        ])->layout('layouts.app');
    }
}