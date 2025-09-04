<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PemantauanLingkungan extends Model
{
    /* Data ini milik satu Lokasi */
    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }

    /* Data ini milik satu Area */
    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
