<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HasilMcu extends Model
{
     use HasFactory;

    protected $guarded = ['id']; // Gunakan ini agar lebih mudah jika kolom terlalu banyak

    /**
     * Definisi relasi: Satu HasilMcu milik satu JadwalMcu.
     */
    public function jadwalMcu()
    {
        return $this->belongsTo(JadwalMcu::class);
    }

}
