<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\JadwalMcu;
use App\Models\Notif; 
use App\Models\UnitKerja;
use App\Models\Departemen;
use App\Models\Kecamatan;
use App\Models\Kabupaten;
use App\Models\Provinsi;
use App\Models\PesertaMcu; // Pastikan model ini diimpor

class Karyawan extends Model
{
    use Notifiable;
    use HasFactory;

    protected $table = 'karyawans';

    protected $fillable = [
        'no_sap',
        'nik_karyawan',
        'nama_karyawan',
        'pekerjaan',
        'tempat_lahir',
        'tanggal_lahir',
        'umur',
        'jenis_kelamin',
        'golongan_darah',
        'agama',
        'status_pernikahan',
        'hubungan',
        'kebangsaan',
        'jabatan',
        'eselon',
        'pendidikan',
        'departemens_id',
        'unit_kerjas_id',
        'provinsi_id',
        'kabupaten_id',
        'kecamatan_id',
        'email',
        'suami_istri',
        'pekerjaan_suami_istri',
        'alamat',
        'no_hp',
        'foto_profil',
        'tinggi_badan',
        'berat_badan',
        'fcm_token',
    ];

    // Accesor untuk mengambil email (Jika Job menggunakan $karyawan->email_karyawan)
    public function getEmailKaryawanAttribute()
    {
        return $this->attributes['email']; // Mengambil dari kolom 'email'
    }

    // Accesor untuk mengambil FCM Token
    public function getFcmTokenAttribute()
    {
        return $this->attributes['fcm_token'] ?? null;
    }

    public function jadwalMcu()
    {
        return $this->hasMany(JadwalMcu::class, 'karyawan_id');
    }

    public function notifs()
    {
        return $this->hasMany(Notif::class);
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerjas_id');
    }
 
    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemens_id');
    }
    
    public function   kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
    }
    
    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class);
    }
    public function keluargas(): HasMany
    {
        return $this->hasMany(PesertaMcu::class, 'karyawan_id','id');
    }
    // Relasi tambahan
    public function employeeLogin()
    {
        return $this->hasOne(EmployeeLogin::class);
    }
     public function pasangan()
    {
        return $this->hasOne(PesertaMcu::class, 'karyawan_id', 'id')
                    ->whereIn('tipe_anggota', ['Suami', 'Istri']);
    }
}
