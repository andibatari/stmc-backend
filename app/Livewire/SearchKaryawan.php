<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Karyawan; // Impor model Karyawan
use App\Models\UnitKerja; // Impor model UnitKerja
use App\Models\Departemen; // Impor model Departemen


class SearchKaryawan extends Component
{
    // Gunakan trait WithPagination untuk mendukung paginasi
    use WithPagination;

    // Properti untuk menyimpan data karyawan yang dicari
    public $searchSap = '';
    public $searchNama = '';
    public $searchUnitKerja = '';

    // Properti untuk tab aktif
    public $activeTab = 'ptst';

    protected $queryString = ['searchSap', 'searchNama', 'searchUnitKerja', 'sortBy', 'sortDirection', 'activeTab'];

    public function updating($key)
    {
        if (in_array($key, ['searchSap', 'searchNama', 'searchUnitKerja'])) {
            $this->resetPage();
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }


    public function render()
    {

        // Ambil data karyawan dengan relasi yang diperlukan
        $karyawans = Karyawan::with(['departemen', 'unitKerja'])
            ->when($this->searchSap, function ($query) {
                $query->where('no_sap', 'like', '%' . $this->searchSap . '%');
            })
            ->when($this->searchNama, function ($query) {
                $query->where('nama_karyawan', 'like', '%' . $this->searchNama . '%');
            })
            ->when($this->searchUnitKerja, function ($query) {
                $query->whereHas('unitKerja', function ($subQuery) {
                    $subQuery->where('nama_unit_kerja', 'like', '%' . $this->searchUnitKerja . '%');
                });
            })
            ->paginate(20); // Paginasi 10 karyawan per halaman

        return view('livewire.search-karyawan', [
            'karyawans' => $karyawans,
        ]);
    }
}
