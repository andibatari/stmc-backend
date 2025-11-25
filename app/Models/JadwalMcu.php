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

    /**
     * Relasi dinamis untuk mendapatkan data Pasien (baik Karyawan atau Peserta MCU).
     * Ini digunakan di Livewire untuk menyatukan relasi.
     * * @return BelongsTo|MorphTo
     */
    public function patient()
    {
        // Jika ada karyawan_id, gunakan relasi karyawan.
        if ($this->karyawan_id !== null) {
            return $this->karyawan();
        }
        
        // Jika ada peserta_mcus_id, gunakan relasi pesertaMcu.
        if ($this->peserta_mcus_id !== null) {
            return $this->pesertaMcu();
        }

        // Fallback: Gunakan kolom lokal di JadwalMcu sebagai object dummy
        return new class extends BelongsTo {
            public function getResults() {
                // Membuat objek Patient palsu dari data lokal JadwalMcu
                return (object)[
                    'nama_lengkap' => $this->getParent()->nama_pasien ?? 'Pasien Tidak Terdaftar',
                    'nama_karyawan' => $this->getParent()->nama_pasien ?? 'Pasien Tidak Terdaftar',
                    'nik_karyawan' => $this->getParent()->nik_pasien ?? 'N/A',
                    'no_sap' => $this->getParent()->no_sap ?? 'N/A',
                ];
            }
        };
    }
}
