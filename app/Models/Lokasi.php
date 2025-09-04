<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    /* Lokasi memiliki banyak Area */
    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    /* Lokasi memiliki banyak data pemantauan lingkungan */
    public function pemantauanLingkungans()
    {
        return $this->hasMany(PemantauanLingkungan::class);
    }
}
