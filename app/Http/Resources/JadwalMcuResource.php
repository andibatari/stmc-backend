<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class JadwalMcuResource extends JsonResource
{
    public function toArray($request)
    {
        // Hitung urutan pemeriksaan khusus untuk user ini (#1, #2, dst)
        $iteration = \App\Models\JadwalMcu::where(function($q) {
                $q->where('karyawan_id', $this->karyawan_id)
                  ->where('peserta_mcus_id', $this->peserta_mcus_id);
            })
            ->where('id', '<=', $this->id)
            ->count();
            
        return [
            'id' => $this->id,
            'qr_code_id' => $this->qr_code_id,
            'no_antrean' => $this->no_antrean,
            'iteration_number' => "#" . $iteration, // Ini akan menghasilkan #1, #2, dst
            'tanggal_mcu' => $this->tanggal_mcu 
                ? \Carbon\Carbon::parse($this->tanggal_mcu)->translatedFormat('l, d F Y') 
                : '-',            
            'dokter' => $this->dokter->nama_lengkap ?? 'Menunggu Verifikasi Admin',
            'status' => $this->status,
            'paket_mcu' => $this->paketMcu->nama_paket ?? '-',
            
            // Mengambil data hasil checkup dari resume_body
            'resume' => [
                // json_decode akan error jika resume_body bukan string JSON valid
                'hasil' => $this->isValidJson($this->resume_body) ? json_decode($this->resume_body) : null,
                'saran' => $this->resume_saran ?? '-',
                'kategori' => $this->resume_kategori ?? '-',
            ],

            // URL Unduhan untuk Flutter
            'url_unduh_laporan' => $this->status == 'Finished' 
                ? url("/api/jadwal-mcu/download-laporan-gabungan/{$this->id}") 
                : null,
        ];
    }

    // Fungsi bantu untuk cek validitas JSON agar tidak error 500
    private function isValidJson($string) {
        if (!is_string($string)) return false;
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}