<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens; // Wajib ditambahkan

class EmployeeLogin extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens; // Gunakan HasApiTokens untuk Sanctum

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'employee_logins';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'karyawan_id',
        'no_sap', // Digunakan untuk login
        'password',
    ];

    /**
     * Atribut yang harus disembunyikan untuk serialisasi.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Definisi relasi: Satu akun login dimiliki oleh satu Karyawan.
     * Relasi ini sangat penting untuk mengambil data profil karyawan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}
