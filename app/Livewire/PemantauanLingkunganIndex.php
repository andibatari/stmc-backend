<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PemantauanLingkungan;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PemantauanLingkunganExport; 
use App\Models\Departemen; 
use App\Models\UnitKerja; 

class PemantauanLingkunganIndex extends Component
{
    // Properti utama untuk menampung semua data
    public $pemantauanLingkungan;

    // --- Properti Filter Baru ---
    public $filterArea = '';
    public $filterDepartemen = '';
    public $filterUnitKerja = '';

    // Properti untuk mengelola status edit/tambah
    public $isEditing = false;
    public $isAddingNewLocation = false; 

    public $editingDataId;
    public $editingData = [];
    public $newLocationData = [];

    // Data Departemen dan Unit Kerja (diambil dari Model)
    public $departments;
    public $unitKerjas;
    public $availableUnitsEdit = []; 
    public $availableUnitsNew = []; 
    
    // Properti untuk daftar nilai unik Area
    public $uniqueAreas;

    // Menambahkan $listeners untuk memastikan Unit Kerja diperbarui
    protected $listeners = ['departemenFilterUpdated' => 'applyDepartemenFilter'];

    // Lifecycle hook to handle Department change in EDIT modal
    public function updatedEditingDataDepartemensId($value) 
    {
        $this->editingData['unit_kerjas_id'] = null; 
        $this->availableUnitsEdit = UnitKerja::where('departemens_id', $value)->get()->toArray(); 
    }
    
    // Lifecycle hook to handle Department change in NEW LOCATION modal
    public function updatedNewLocationDataDepartemensId($value) 
    {
        $this->newLocationData['unit_kerjas_id'] = null; 
        $this->availableUnitsNew = UnitKerja::where('departemens_id', $value)->get()->toArray(); 
    }

    // --- Logika Filter Unit Kerja (khusus di modal) ---
    public function updatedFilterDepartemen($value)
    {
        // Reset Unit Kerja saat Departemen Filter berubah
        $this->filterUnitKerja = ''; 
    }

    // --- FUNGSI BARU UNTUK MERESET SEMUA FILTER SECARA EKSPLISIT ---
    public function resetFilters()
    {
        // Menggunakan $this->reset() adalah cara paling bersih di Livewire
        $this->reset(['filterArea', 'filterDepartemen', 'filterUnitKerja']);
        
        // PENTING: Karena ini adalah aksi Livewire, method render() akan otomatis dipanggil
        // dan data tabel akan dimuat ulang dengan filter kosong.
    }
    
    protected function getDefaultPemantauanData()
    {
        return [
            'cahaya' => null, 'bising' => null, 'debu' => null,
            'suhu_basah' => null, 'suhu_kering' => null, 'suhu_radiasi' => null,
            'isbb_indoor' => null, 'isbb_outdoor' => null, 'rh' => null,
        ];
    }

    public function checkNabStatus($data, $dataKey, $nabKey)
    {
        $dataValue = $data->data_pemantauan[$dataKey] ?? null;
        $nabValue = null;

        if ($nabKey === 'nab_cahaya') {
            $nabValue = $data->nab_cahaya;
        } elseif ($nabKey === 'nab_bising') {
            $nabValue = $data->nab_bising;
        } elseif ($nabKey === 'nab_debu') {
            $nabString = $data->nab_debu;
            $matches = [];
            if (preg_match('/^(\d+(\.\d+)?)/', $nabString, $matches)) {
                $nabValue = (float)$matches[1];
            }
            $dataValue = (float) $dataValue; 
        } elseif ($nabKey === 'nab_suhu') {
            if ($dataKey === 'isbb_indoor' || $dataKey === 'isbb_outdoor') {
                $nabValue = $data->nab_suhu; 
            }
        }
        
        if (is_numeric($dataValue) && is_numeric($nabValue) && $nabValue > 0) {
            return $dataValue > $nabValue; 
        }

        return false;
    }

    public function mount()
    {
        // Load data master untuk dropdown dan filter
        $this->departments = Departemen::all();
        $this->unitKerjas = UnitKerja::all();
        
        // Memuat semua area unik saat mount
        $this->uniqueAreas = PemantauanLingkungan::distinct()->pluck('area')->sort()->toArray();

        // KRITIS: Cek query string untuk filter dari Dashboard
        if (request()->has('filterArea')) {
            $this->filterArea = request()->query('filterArea');
            // Flash message opsional untuk memberi tahu filter telah diterapkan
            session()->flash('message', 'Filter Area "' . $this->filterArea . '" otomatis diterapkan dari Dashboard karena melampaui NAB.');
        }
    }

    public function downloadExcel()
    {
         // Untuk fungsi ini, kita harus mengambil data berdasarkan filter yang aktif
        $dataToExport = $this->applyFilters(PemantauanLingkungan::query())->get();
        return Excel::download(new PemantauanLingkunganExport($dataToExport), 'LaporanPemantauanLingkungan_' . Carbon::now()->format('Ymd_His') . '.xlsx');
    }

    // --- Metode Baru untuk Menerapkan Filter Query ---
    protected function applyFilters($query)
    {
        // Eager load relasi untuk tampilan
        $query->with(['departemen', 'unitKerja']);

        if ($this->filterArea) {
            $query->where('area', $this->filterArea);
        }

        if ($this->filterDepartemen) {
            // Filter berdasarkan ID Departemen
            $query->where('departemens_id', $this->filterDepartemen);
        }

        if ($this->filterUnitKerja) {
            // Filter berdasarkan ID Unit Kerja
            $query->where('unit_kerjas_id', $this->filterUnitKerja);
        }
        
        return $query;
    }

    // Metode Edit, SaveNewLocation, Update, Cancel, Delete tetap sama
    public function edit($id)
    {
        $data = PemantauanLingkungan::find($id);
        if ($data) {
            $this->isAddingNewLocation = false;
            $this->isEditing = true;
            $this->editingDataId = $id;
            
            $this->editingData = [
                'id' => $data->id,
                'departemens_id' => $data->departemens_id, 
                'unit_kerjas_id' => $data->unit_kerjas_id, 
                'area' => $data->area,
                'lokasi' => $data->lokasi,
                'tanggal_pemantauan' => Carbon::parse($data->tanggal_pemantauan)->format('Y-m-d'),
                'data_pemantauan' => $data->data_pemantauan,
                'nab_cahaya' => $data->nab_cahaya,
                'nab_bising' => $data->nab_bising,
                'nab_debu' => $data->nab_debu,
                'nab_suhu' => $data->nab_suhu, 
                'kesimpulan' => $data->kesimpulan,
            ];
            
            $this->availableUnitsEdit = UnitKerja::where('departemens_id', $data->departemens_id)->get()->toArray();
        }
    }

    public function startAddLocation()
    {
        if (isset($this->editingData['area']) && isset($this->editingData['tanggal_pemantauan'])) {
            $this->isEditing = false;
            $this->isAddingNewLocation = true;
            
            $this->availableUnitsNew = UnitKerja::where('departemens_id', $this->editingData['departemens_id'])->get()->toArray();

            $this->newLocationData = [
                'departemens_id' => $this->editingData['departemens_id'],
                'unit_kerjas_id' => $this->editingData['unit_kerjas_id'],
                'area' => $this->editingData['area'],
                'tanggal_pemantauan' => $this->editingData['tanggal_pemantauan'],
                'lokasi' => '',
                
                'nab_cahaya' => $this->editingData['nab_cahaya'],
                'nab_bising' => $this->editingData['nab_bising'],
                'nab_debu' => $this->editingData['nab_debu'],
                'nab_suhu' => $this->editingData['nab_suhu'], 
                
                'pemantauan' => $this->getDefaultPemantauanData(), 
                'kesimpulan' => null,
            ];
        }
    }

    public function saveNewLocation()
    {
        $this->validate([
             'newLocationData.departemens_id' => 'required|numeric|exists:departemens,id',
             'newLocationData.unit_kerjas_id' => 'required|numeric|exists:unit_kerjas,id',
             'newLocationData.lokasi' => 'required|string|max:255',
             'newLocationData.nab_cahaya' => 'required|numeric',
             'newLocationData.nab_bising' => 'required|numeric',
             'newLocationData.nab_debu' => 'required|string',
             'newLocationData.nab_suhu' => 'required|numeric',
             'newLocationData.pemantauan.cahaya' => 'nullable|numeric',
             'newLocationData.pemantauan.bising' => 'nullable|numeric',
             'newLocationData.pemantauan.debu' => 'nullable|numeric',
             'newLocationData.pemantauan.suhu_basah' => 'nullable|numeric',
             'newLocationData.pemantauan.suhu_kering' => 'nullable|numeric',
             'newLocationData.pemantauan.suhu_radiasi' => 'nullable|numeric',
             'newLocationData.pemantauan.isbb_indoor' => 'nullable|numeric',
             'newLocationData.pemantauan.isbb_outdoor' => 'nullable|numeric',
             'newLocationData.pemantauan.rh' => 'nullable|numeric',
             'newLocationData.kesimpulan' => 'nullable|string|max:2000',
        ]);
        
        PemantauanLingkungan::create([
            'departemens_id' => $this->newLocationData['departemens_id'],
            'unit_kerjas_id' => $this->newLocationData['unit_kerjas_id'],
            'area' => $this->newLocationData['area'],
            'lokasi' => $this->newLocationData['lokasi'],
            'tanggal_pemantauan' => $this->newLocationData['tanggal_pemantauan'],
            'data_pemantauan' => $this->newLocationData['pemantauan'],
            'nab_cahaya' => $this->newLocationData['nab_cahaya'],
            'nab_bising' => $this->newLocationData['nab_bising'],
            'nab_debu' => $this->newLocationData['nab_debu'],
            'nab_suhu' => $this->newLocationData['nab_suhu'], 
            'kesimpulan' => $this->newLocationData['kesimpulan'] ?? null,
        ]);
        
        session()->flash('message', 'Lokasi baru berhasil ditambahkan ke Area ' . $this->newLocationData['area'] . '!');
        $this->cancelAddLocation(); 
    }

    public function update()
    {
        $this->validate([
             'editingData.departemens_id' => 'required|numeric|exists:departemens,id',
             'editingData.unit_kerjas_id' => 'required|numeric|exists:unit_kerjas,id',
             'editingData.area' => 'required|string|max:255',
             'editingData.lokasi' => 'required|string|max:255',
             'editingData.tanggal_pemantauan' => 'required|date',
             'editingData.nab_cahaya' => 'required|numeric',
             'editingData.nab_bising' => 'required|numeric',
             'editingData.nab_debu' => 'required|string',
             'editingData.nab_suhu' => 'required|numeric',
             'editingData.data_pemantauan.cahaya' => 'nullable|numeric',
             'editingData.data_pemantauan.bising' => 'nullable|numeric',
             'editingData.data_pemantauan.debu' => 'nullable|numeric',
             'editingData.data_pemantauan.suhu_basah' => 'nullable|numeric',
             'editingData.data_pemantauan.suhu_kering' => 'nullable|numeric',
             'editingData.data_pemantauan.suhu_radiasi' => 'nullable|numeric',
             'editingData.data_pemantauan.isbb_indoor' => 'nullable|numeric',
             'editingData.data_pemantauan.isbb_outdoor' => 'nullable|numeric',
             'editingData.data_pemantauan.rh' => 'nullable|numeric',
             'editingData.kesimpulan' => 'nullable|string|max:2000',
        ]);

        $data = PemantauanLingkungan::find($this->editingDataId);
        if ($data) {
            $data->update([
                'departemens_id' => $this->editingData['departemens_id'],
                'unit_kerjas_id' => $this->editingData['unit_kerjas_id'],
                'area' => $this->editingData['area'],
                'lokasi' => $this->editingData['lokasi'],
                'tanggal_pemantauan' => $this->editingData['tanggal_pemantauan'],
                'data_pemantauan' => $this->editingData['data_pemantauan'],
                'nab_cahaya' => $this->editingData['nab_cahaya'],
                'nab_bising' => $this->editingData['nab_bising'],
                'nab_debu' => $this->editingData['nab_debu'],
                'nab_suhu' => $this->editingData['nab_suhu'], 
                'kesimpulan' => $this->editingData['kesimpulan'] ?? null,
            ]);

            session()->flash('message', 'Data pemantauan berhasil diperbarui!');
            $this->cancelEdit();
        }
    }

    public function cancelEdit()
    {
        $this->isEditing = false;
        $this->editingDataId = null;
        $this->reset(['editingData', 'availableUnitsEdit']);
        $this->mount();
    }
    
    public function cancelAddLocation()
    {
        $this->isAddingNewLocation = false;
        $this->reset(['newLocationData', 'availableUnitsNew']);
        $this->mount();
    }
    
    public function delete($id)
    {
        PemantauanLingkungan::destroy($id);
        session()->flash('message', 'Data pemantauan berhasil dihapus!');
        $this->mount();
    }

    public function render()
    {
        // Panggil query dengan filter
        $pemantauanLingkungan = $this->applyFilters(PemantauanLingkungan::query())->get();

        // Grouping data yang sudah difilter
        $pemantauanLingkunganGrouped = $pemantauanLingkungan->groupBy('area');

        // Filter daftar Unit Kerja berdasarkan Departemen yang dipilih (untuk filter view)
        $filteredUnits = $this->unitKerjas;
        if ($this->filterDepartemen) {
            $filteredUnits = $this->unitKerjas->where('departemens_id', $this->filterDepartemen);
        }

        return view('livewire.pemantauan-lingkungan-index', [
            'pemantauanLingkunganGrouped' => $pemantauanLingkunganGrouped,
            'departments' => $this->departments, 
            'unitKerjas' => $this->unitKerjas,   
            'filteredUnits' => $filteredUnits, // Kirim unit yang sudah difilter
        ])->layout('layouts.app');
    }
}
