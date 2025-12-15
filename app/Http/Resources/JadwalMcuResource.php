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
            'check_up_number' => $this->nomor_mcu ?? '#', // Asumsi kolom DB
            'tanggal_jadwal' => Carbon::parse($this->tanggal_jadwal)->isoFormat('dddd, D MMMM Y'),
            
            // [PERBAIKAN] Mengambil NAMA DOKTER dari relasi
            // Asumsi: Model Dokter memiliki kolom 'nama'
            'dokter_piket' => optional($this->dokter)->nama ?? 'Dokter Belum Ditentukan', 
            
            'status' => $this->status,
            'paket_mcu' => $this->paket_mcu,
            // ... data lain yang dibutuhkan Flutter
        ];
    }
}