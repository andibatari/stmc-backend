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
        'tanggal_mcu' => $this->tanggal_mcu,
        'status' => $this->status,
        'no_antrean' => $this->no_antrean,

        'dokter' => $this->dokter ? [
            'id' => $this->dokter->id,
            'nama' => $this->dokter->nama_dokter ?? null,
        ] : null,

        'paket_mcu' => $this->paketMcu ? [
            'id' => $this->paketMcu->id,
            'nama_paket' => $this->paketMcu->nama_paket,
        ] : null,

        'pasien' => [
            'nama' => $this->patient->nama_lengkap ?? $this->patient->nama_karyawan ?? $this->nama_pasien,
            'nik' => $this->nik_pasien,
        ],
    ];
}

}