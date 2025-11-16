<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalMcu extends Model
{
    use HasFactory;

    protected $table = 'jadwal_mcus';
    protected $fillable = [
        'qr_code_id',
        'peserta_mcus_id',
        'karyawan_id',
        'paket_mcus_id',
        'tanggal_mcu',
        'tanggal_pendaftaran',
        'no_antrean',
        'no_sap',
        'nama_pasien',
        'nik_pasien',
        'perusahaan_asal',
        'dokter_id',
        'status',
        'resume_body',
        'resume_saran',
        'resume_kategori',
    ];

    // Relasi ke model Karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    // Relasi ke model PesertaMcu
    public function pesertaMcu()
    {
        return $this->belongsTo(PesertaMcu::class, 'peserta_mcus_id');
    }
    
    // Relasi ke model Dokter
    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'dokter_id');
    }

    // Perbaiki relasi ke model PaketMcu
    public function paketMcu()
    {
        // Pastikan nama kolom foreign key adalah `paket_mcus_id`
        return $this->belongsTo(PaketMcu::class, 'paket_mcus_id');
    }
    
    public function jadwalPoli()
    {
        return $this->hasMany(JadwalPoli::class, 'jadwal_mcus_id');
    }
}
