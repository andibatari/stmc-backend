<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class JadwalMcuResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'qr_code_id' => $this->qr_code_id,
            'check_up_number' => $this->no_antrean,
            'tanggal_jadwal' => $this->tanggal_mcu 
                ? \Carbon\Carbon::parse($this->tanggal_mcu)->translatedFormat('l, d F Y') 
                : '-',            
            'dokter' => $this->dokter ? $this->dokter->nama : 'Menunggu Verifikasi Admin',
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