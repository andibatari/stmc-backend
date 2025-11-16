<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Karyawan;
use App\Models\PesertaMcu;

class EmployeeDetailManager extends Component
{
    public $karyawan;
    public $pesertaIstri;
    public $pesertaSuami; // Tambahkan properti untuk suami
    public $activeUser;
    public $activeTab = 'data';

    public function mount(Karyawan $karyawan)
    {
            // Eager load relasi yang diperlukan
        $this->karyawan = $karyawan->load('jadwalMcu', 'keluargas'); // Eager load keluarga untuk menghindari N+1 query

        // Cari peserta dengan tipe anggota 'istri'
        $this->pesertaIstri = $this->karyawan->keluargas->firstWhere('tipe_anggota', 'Istri');
    $this->pesertaSuami = $this->karyawan->keluargas->firstWhere('tipe_anggota', 'Suami');
        // Pastikan data pasangan dimuat
        if ($this->pesertaIstri) {
            $this->pesertaIstri->load('jadwalMcu');
        }
        if ($this->pesertaSuami) {
            $this->pesertaSuami->load('jadwalMcu');
        }
        
        // Atur pengguna aktif awal sebagai karyawan
        $this->activeUser = $this->karyawan;
    }
    
    // Metode untuk menampilkan data karyawan
    public function selectKaryawan()
    {
        $this->activeUser = $this->karyawan;
        $this->activeTab = 'data';
    }
    
    // Metode untuk menampilkan data istri
    public function selectIstri()
    {
        if ($this->pesertaIstri) {
            $this->activeUser = $this->pesertaIstri;
            $this->activeTab = 'data';
        }
    }
    
    // Metode untuk menampilkan data suami (TAMBAH KODE INI)
    public function selectSuami()
    {
        if ($this->pesertaSuami) {
            $this->activeUser = $this->pesertaSuami;
            $this->activeTab = 'data';
        }
    }

    // Metode untuk mengganti tab di panel kanan
    public function changeTab($tabName)
    {
        $this->activeTab = $tabName;
    }

    public function render()
    {
        return view('livewire.employee-detail-manager');
    }
}