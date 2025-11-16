<?php

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

    // Separate search properties for each tab
    public $searchSap = '';
    public $searchNama = '';
    public $searchUnitKerja = '';

    public $searchNik = '';
    public $searchNamaPasien = '';
    public $searchPerusahaanAsal = '';

    public $activeTab = 'ptst';

    // The queryString configuration is correct for keeping the search in the URL
    protected $queryString = ['searchSap', 'searchNama', 'searchUnitKerja', 'searchNik', 'searchNamaPasien', 'searchPerusahaanAsal'];

    // This method handles resetting the page when any search property changes
    public function updating($key)
    {
        if (in_array($key, ['searchSap', 'searchNama', 'searchUnitKerja', 'searchNik', 'searchNamaPasien', 'searchPerusahaanAsal'])) {
            $this->resetPage();
        }
    }

    // Reset all search fields when the tab is changed
    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->searchSap = '';
        $this->searchNama = '';
        $this->searchUnitKerja = '';

        $this->searchNik = '';
        $this->searchNamaPasien = '';
        $this->searchPerusahaanAsal = '';

        $this->resetPage();
    }
    
    public function render()
    {
        if ($this->activeTab === 'ptst') {
            $query = Karyawan::query()
                ->with(['unitKerja', 'departemen']);

            // Apply search filters for the PTST tab
            $query->when($this->searchSap, fn($q) => $q->where('no_sap', 'like', '%' . $this->searchSap . '%'));
            $query->when($this->searchNama, fn($q) => $q->where('nama_karyawan', 'like', '%' . $this->searchNama . '%'));
            $query->when($this->searchUnitKerja, fn($q) => $q->whereHas('unitKerja', fn($sq) => $sq->where('nama_unit_kerja', 'like', '%' . $this->searchUnitKerja . '%')));
            
            $items = $query->paginate(15, pageName: 'ptstPage');
        } else {
            $query = PesertaMcu::query()
                ->with('karyawan');
            
            // Apply search filters for the Non-PTST tab
            $query->when($this->searchNik, fn($q) => $q->where('nik_pasien', 'like', '%' . $this->searchNik . '%'));
            $query->when($this->searchNamaPasien, fn($q) => $q->where('nama_lengkap', 'like', '%' . $this->searchNamaPasien . '%'));
            $query->when($this->searchPerusahaanAsal, fn($q) => $q->where('perusahaan_asal', 'like', '%' . $this->searchPerusahaanAsal . '%'));

            $items = $query->paginate(15, pageName: 'nonPtstPage');
        }

        return view('livewire.search-karyawan', [
            'items' => $items,
        ]);
    }
}