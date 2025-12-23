<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable; // PENTING

class PesertaMcu extends Model
{
    use HasFactory, Notifiable; 

    protected $table = 'peserta_mcus'; // Pastikan nama tabelnya sudah benar

    protected $fillable = [
        'karyawan_id',
        'tipe_anggota',
        'no_sap',
        'hubungan',
        'nik_pasien',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'umur',
        'golongan_darah',
        'pendidikan',
        'pekerjaan',
        'perusahaan_asal',
        'agama',
        'alamat',
        'no_hp',
        'email',
        'foto_profil',
        'provinsi_id',
        'nama_kabupaten',
        'nama_kecamatan',
        'tinggi_badan',
        'berat_badan',
        'fcm_token',
    ];

    protected $appends = ['email'];

    /**
     * Relasi ke model Karyawan (jika peserta adalah anggota keluarga karyawan).
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    /**
     * Relasi ke model Departemen (jika peserta adalah non-karyawan).
     */
    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemens_id');
    }

    /**
     * Relasi ke model UnitKerja (jika peserta adalah non-karyawan).
     */
    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerjas_id');
    }

    /**
     * Relasi ke model Provinsi.
     */
    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id');
    }

    public function jadwalMcu()
    {
        return $this->hasMany(JadwalMcu::class, 'peserta_mcus_id');
    }

    public function getFcmTokenAttribute()
    {
        return $this->attributes['fcm_token'] ?? null;
    }

    // Relasi ke tabel login
    public function pesertaMcuLogin()
    {
        // Asumsi: peserta_mcu_id ada di tabel peserta_mcu_logins
        return $this->hasOne(PesertaMcuLogin::class, 'peserta_mcu_id', 'id');
    }

}