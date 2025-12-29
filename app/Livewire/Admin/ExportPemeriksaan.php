<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Departemen;
use App\Models\JadwalMcu;
use App\Exports\PemeriksaanExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportPemeriksaan extends Component
{
    public $departemens_id = '';
    public $tipe_anggota = '';
    public $status_kehadiran = '';
    public $date_start;
    public $date_end;
    public $total_preview = 0;

    public function mount()
    {
        $this->date_start = now()->startOfYear()->format('Y-m-d');
        $this->date_end = now()->format('Y-m-d');
        $this->updated();
    }

    public function updated()
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
            // Default: Tampilkan hanya data Karyawan (sesuai permintaan Anda)
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

        return view('livewire.admin.export-pemeriksaan', [
            'listDepartemen' => Departemen::orderBy('nama_departemen')->get(),
            'listKategori'   => \App\Models\PesertaMcu::distinct()->pluck('tipe_anggota')->filter(),
            'statusLabels'   => $statusLabels
        ])->layout('layouts.app');
    }
}