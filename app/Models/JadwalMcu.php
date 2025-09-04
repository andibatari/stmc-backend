<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalMcu extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model ini.
     *
     * @var string
     */
    protected $table = 'jadwal_mcus';

    /**
     * Kolom-kolom yang dapat diisi secara massal (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'karyawan_id',
        'tipe_pasien',
        'tanggal_mcu',
        'tanggal_pendaftaran',
        'no_antrean',
        'no_sap',
        'nama_pasien',
        'no_identitas',
        'perusahaan_afiliasi',
        'tanggal_lahir',
        'dokter',
        'status',
    ];

    /**
     * Relasi ke model Karyawan (untuk tipe pasien 'ptst').
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}