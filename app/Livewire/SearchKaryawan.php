<?php

namespace App\Console\Commands; // Sesuaikan dengan namespace asli Anda jika bukan console commands, abaikan baris ini jika berbeda dan pertahankan namespace App\Livewire;

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Karyawan; 
use App\Models\UnitKerja;
use App\Models\Departemen;
use App\Models\PesertaMcu; 

class SearchKaryawan extends Component
{
    use WithPagination;

    // Filter Pencarian
    public $searchSap = '';
    public $searchNama = '';
    public $searchUnitKerja = ''; 
    public $searchDepartemen = ''; 

    public $searchNik = '';
    public $searchNamaPasien = '';
    public $searchPerusahaanAsal = '';

    public $activeTab = 'ptst';

    // List untuk Dropdown
    public $listDepartemen = [];
    public $listUnitKerja = [];

    // FIX: Daftarkan 'activeTab' ke dalam query string agar Livewire memantau perubahan tab di URL
    protected $queryString = [
        'activeTab' => ['except' => 'ptst', 'as' => 'tab'], // Menampilkan ?tab=non-ptst di URL
        'searchSap', 
        'searchNama', 
        'searchUnitKerja', 
        'searchDepartemen', 
        'searchNik', 
        'searchNamaPasien', 
        'searchPerusahaanAsal'
    ];

    public function mount()
    {
        // FIX: Ambil parameter 'tab' langsung dari request URL saat halaman diakses pertama kali
        if (request()->has('tab')) {
            $this->activeTab = request()->query('tab');
        }

        // Tarik semua data departemen saat halaman pertama dimuat
        $this->listDepartemen = Departemen::all();
        
        // Tarik unit kerja jika ada departemen yang tersimpan di URL
        if ($this->searchDepartemen) {
            $this->listUnitKerja = UnitKerja::where('departemens_id', $this->searchDepartemen)->get();
        }
    }

    public function updating($key)
    {
        if (in_array($key, ['searchSap', 'searchNama', 'searchUnitKerja', 'searchDepartemen', 'searchNik', 'searchNamaPasien', 'searchPerusahaanAsal'])) {
            $this->resetPage();
        }
    }

    // Fungsi otomatis berjalan ketika dropdown Departemen dipilih
    public function updatedSearchDepartemen($value)
    {
        $this->searchUnitKerja = ''; // Reset pilihan unit kerja
        
        if ($value) {
            // Ambil anak unit kerja berdasarkan departemen yg dipilih
            $this->listUnitKerja = UnitKerja::where('departemens_id', $value)->get();
        } else {
            $this->listUnitKerja = [];
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->searchSap = '';
        $this->searchNama = '';
        $this->searchUnitKerja = '';
        $this->searchDepartemen = '';
        $this->searchNik = '';
        $this->searchNamaPasien = '';
        $this->searchPerusahaanAsal = '';
        $this->listUnitKerja = [];

        $this->resetPage();
    }
    
    public function render()
    {
        if ($this->activeTab === 'ptst') {
            $query = Karyawan::query()->with(['unitKerja', 'departemen']);

            $query->when($this->searchSap, fn($q) => $q->where('no_sap', 'like', '%' . $this->searchSap . '%'));
            $query->when($this->searchNama, fn($q) => $q->where('nama_karyawan', 'like', '%' . $this->searchNama . '%'));
            
            // Filter Exact Match ID untuk Dropdown
            $query->when($this->searchDepartemen, fn($q) => $q->where('departemens_id', $this->searchDepartemen));
            $query->when($this->searchUnitKerja, fn($q) => $q->where('unit_kerjas_id', $this->searchUnitKerja));
            
            $items = $query->paginate(10, pageName: 'ptstPage');
        } else {
            $query = PesertaMcu::query()->with('karyawan');
            
            $query->when($this->searchNik, fn($q) => $q->where('nik_pasien', 'like', '%' . $this->searchNik . '%'));
            $query->when($this->searchNamaPasien, fn($q) => $q->where('nama_lengkap', 'like', '%' . $this->searchNamaPasien . '%'));
            $query->when($this->searchPerusahaanAsal, fn($q) => $q->where('perusahaan_asal', 'like', '%' . $this->searchPerusahaanAsal . '%'));

            $items = $query->paginate(10, pageName: 'nonPtstPage');
        }

        return view('livewire.search-karyawan', [
            'items' => $items,
        ]);
    }
}