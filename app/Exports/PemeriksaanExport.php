<?php

namespace App\Exports;

use App\Models\JadwalMcu;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class PemeriksaanExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = JadwalMcu::query()
            ->leftJoin('peserta_mcus', 'jadwal_mcus.peserta_mcus_id', '=', 'peserta_mcus.id')
            ->leftJoin('karyawans', 'jadwal_mcus.karyawan_id', '=', 'karyawans.id')
            ->leftJoin('departemens', 'karyawans.departemens_id', '=', 'departemens.id')
            ->select([
                'jadwal_mcus.*', 
                'peserta_mcus.nama_lengkap as nama_umum',
                'karyawans.nama_karyawan', 
                'karyawans.no_sap as sap_karyawan',
                'departemens.nama_departemen as dept_karyawan',
            ]);

        $query->whereBetween('jadwal_mcus.tanggal_mcu', [
            $this->filters['date_start'] . ' 00:00:00', 
            $this->filters['date_end'] . ' 23:59:59'
        ]);

        if (!empty($this->filters['status_kehadiran'])) {
            $query->where('jadwal_mcus.status', $this->filters['status_kehadiran']);
        }

        // LOGIKA SINKRON: Mengunci data karyawan jika departemen atau default dipilih
        if (!empty($this->filters['departemens_id'])) {
            $query->whereNotNull('jadwal_mcus.karyawan_id')
                ->where('karyawans.departemens_id', $this->filters['departemens_id']);
        } elseif (!empty($this->filters['tipe_anggota'])) {
            $query->where('peserta_mcus.tipe_anggota', $this->filters['tipe_anggota']);
        } else {
            // Jika tidak ada filter khusus, tampilkan hanya karyawan saja
            $query->whereNotNull('jadwal_mcus.karyawan_id');
        }

        return $query->orderBy('jadwal_mcus.tanggal_mcu', 'desc');
    }

    public function map($jadwal): array
    {
        $resume = json_decode($jadwal->resume_body, true) ?? [];
        
        // Logika Fallback agar Nama dan SAP tidak kosong di Excel
        $namaFinal = $jadwal->nama_karyawan ?: ($jadwal->nama_umum ?: $jadwal->nama_pasien);
        $sapFinal = $jadwal->sap_karyawan ?: ($jadwal->no_sap ?: '-');

        $statusIndo = [
            'Scheduled' => 'Terjadwal',
            'Present'   => 'Hadir',
            'Finished'  => 'Selesai',
            'Canceled'  => 'Dibatalkan'
        ];

        return [
            $sapFinal,
            $namaFinal,
            $jadwal->dept_karyawan ?: 'Umum/Non-PTST',
            $jadwal->tanggal_mcu ? \Carbon\Carbon::parse($jadwal->tanggal_mcu)->format('d-m-Y') : '-',
            $resume['bmi'] ?? '-',
            $resume['laboratorium'] ?? '-',
            $resume['ecg'] ?? '-',
            $resume['mata'] ?? '-',
            $resume['thorax_photo'] ?? '-',
            $resume['spirometri'] ?? '-',
            $resume['audiometri'] ?? '-',
            $jadwal->resume_kategori ?? '-',
            $jadwal->resume_saran ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            "NIP/SAP",
            "Nama Pasien",
            "Departemen",
            "Tgl Periksa",
            "BMI",
            "Laboratorium",
            "ECG/EKG",
            "Mata",
            "Thorax",
            "Spirometri",
            "Audiometri",
            "Kesimpulan (Kategori)",
            "Saran Dokter"
        ];
    }
}