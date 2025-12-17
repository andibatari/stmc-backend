<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens; // Wajib ditambahkan

class PesertaMcuLogin extends Authenticatable
{
    use HasFactory, HasApiTokens; // Gunakan HasApiTokens untuk Sanctum

    protected $table = 'peserta_mcu_logins';

    protected $fillable = [
        'peserta_mcus_id',
        'nik_pasien',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Relasi ke model PesertaMcu.
     */
    public function pasien()
    {
        return $this->belongsTo(PesertaMcu::class, 'peserta_mcus_id');
    }
}