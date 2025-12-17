<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JadwalMcuResource extends JsonResource
{
    public function toArray($request)
    {
        // AMAN: patient bisa NULL
        $patient = $this->patient ?? null;

        return [
            'id' => $this->id,
            'tanggal_mcu' => $this->tanggal_mcu,
            'status' => $this->status,
            'no_antrean' => $this->no_antrean,

            // ======================
            // HASIL MCU (INI YANG HILANG)
            // ======================
            'resume' => [
                'body' => $this->resume_body
                    ? json_decode($this->resume_body, true)
                    : null,
                'saran' => $this->resume_saran,
                'kategori' => $this->resume_kategori,
            ],

            'dokter' => $this->dokter ? [
                'id' => $this->dokter->id,
                'nama' => $this->dokter->nama_dokter,
            ] : null,

            'paket_mcu' => $this->paketMcu ? [
                'id' => $this->paketMcu->id,
                'nama_paket' => $this->paketMcu->nama_paket,
            ] : null,

            // ======================
            // PASIEN (NULL SAFE)
            // ======================
            'pasien' => [
                'nama' => $patient->nama_lengkap
                    ?? $patient->nama_karyawan
                    ?? $this->nama_pasien
                    ?? '-',
                'nik' => $patient->nik_karyawan
                    ?? $patient->nik_pasien
                    ?? $this->nik_pasien
                    ?? '-',
            ],
        ];
    }
}
