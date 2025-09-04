<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    /* Area ini milik satu Lokasi */
    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }

    /* Area memiliki banyak data Pemantauan Lingkungan */
    public function pemantauanLingkungans()
    {
        return $this->hasMany(PemantauanLingkungan::class);
    }
}
