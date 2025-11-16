<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesertaMcu extends Model
{
    use HasFactory;

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
        'kabupaten_id',
        'kecamatan_id',
        'departemens_id',
        'unit_kerjas_id',
        'tinggi_badan',
        'berat_badan',
    ];

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

    /**
     * Relasi ke model Kabupaten.
     */
    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kabupaten_id');
    }

    /**
     * Relasi ke model Kecamatan.
     */
    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }
    public function jadwalMcu()
    {
        return $this->hasMany(JadwalMcu::class, 'peserta_mcus_id');
    }

}