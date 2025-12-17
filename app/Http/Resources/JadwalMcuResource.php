<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JadwalMcuResource extends JsonResource
{
    public function toArray($request)
    {
        $patient = $this->patient;

        return [
            'id' => $this->id,
            'tanggal_mcu' => $this->tanggal_mcu,
            'status' => $this->status,
            'no_antrean' => $this->no_antrean,

            'dokter' => $this->dokter ? [
                'id' => $this->dokter->id,
                'nama' => $this->dokter->nama_dokter,
            ] : null,

            'paket_mcu' => $this->paketMcu ? [
                'id' => $this->paketMcu->id,
                'nama_paket' => $this->paketMcu->nama_paket,
            ] : null,

            'pasien' => [
                'nama' => $patient->nama_lengkap ?? $patient->nama ?? '-',
                'nik'  => $patient->nik_pasien ?? $patient->nik_karyawan ?? '-',
            ],
        ];
    }
}
