<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Poli extends Model
{

    use HasFactory;

     protected $fillable = [
        'nama_poli',
    ];
    
    public function paketMcus()
    {
        return $this->belongsToMany(PaketMcu::class, 'paket_poli');
    }
}
