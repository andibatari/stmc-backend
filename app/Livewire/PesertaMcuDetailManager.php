<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PesertaMcu;
use Carbon\Carbon;

class PesertaMcuDetailManager extends Component
{
    // Properti publik untuk menyimpan data
    public $pesertaMcu;
    public $activeTab = 'data';

    // Metode mount() akan dijalankan saat komponen diinisialisasi
    public function mount(PesertaMcu $pesertaMcu)
    {
        // Eager load relasi yang diperlukan untuk mencegah N+1 query
        $this->pesertaMcu = $pesertaMcu->load(['karyawan', 'karyawan.unitKerja', 'karyawan.departemen', 'provinsi', 'kabupaten', 'kecamatan']);
    }

    // Metode untuk mengganti tab di panel kanan
    public function changeTab($tabName)
    {
        $this->activeTab = $tabName;
    }

    public function render()
    {
        // Mengirim data pesertaMcu ke view
        return view('livewire.peserta-mcu-detail-manager');
    }
}