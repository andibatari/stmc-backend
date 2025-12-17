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
            'tanggal_jadwal' => Carbon::parse($this->tanggal_mcu)->translatedFormat('l, d F Y'),
            'dokter' => $this->dokter ? $this->dokter->nama : 'Menunggu Verifikasi Admin',
            'status' => $this->status,
            'paket_mcu' => $this->paketMcu ? $this->paketMcu->nama_paket : '-',
            
            // Mengambil data hasil checkup dari resume_body
            'resume' => [
                'hasil' => json_decode($this->resume_body), // Menjadi Object di Flutter
                'saran' => $this->resume_saran,
                'kategori' => $this->resume_kategori,
            ],

            // URL Unduhan untuk Flutter
            'url_unduh_laporan' => $this->status == 'Finished' 
                ? url("/api/jadwal-mcu/download-laporan-gabungan/{$this->id}") 
                : null,
        ];
    }
}