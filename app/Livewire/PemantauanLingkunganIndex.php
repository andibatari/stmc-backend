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
use Livewire\WithPagination; 
use Illuminate\Pagination\LengthAwarePaginator; 

class PemantauanLingkunganIndex extends Component
{
    use WithPagination;
    
    public $pemantauanLingkungan;

    public $filterArea = '';
    public $filterDepartemen = '';
    public $filterUnitKerja = '';

    public $isEditing = false;
    public $isAddingNewLocation = false; 

    public $editingDataId;
    public $editingData = [];
    public $newLocationData = [];

    public $departments;
    public $unitKerjas;
    public $availableUnitsEdit = []; 
    public $availableUnitsNew = []; 
    
    public $uniqueAreas;

    protected $listeners = ['departemenFilterUpdated' => 'applyDepartemenFilter'];

    public $filterNabCahaya = ''; 
    public $filterNabBising = ''; 
    public $filterNabDebu = '';
    public $filterNabSuhuIsbb = ''; 

    public $searchQuery = '';
    public $startDate = '';
    public $endDate = '';

    public function updatingSearchQuery() { $this->resetPage(); }
    public function updatingStartDate() { $this->resetPage(); }
    public function updatingEndDate() { $this->resetPage(); }
    public function updatingFilterDepartemen() { $this->resetPage(); }
    public function updatingFilterArea() { $this->resetPage(); }

    public function updatedEditingDataDepartemensId($value) 
    {
        $this->editingData['unit_kerjas_id'] = null; 
        $this->availableUnitsEdit = UnitKerja::where('departemens_id', $value)->get()->toArray(); 
    }
    
    public function updatedNewLocationDataDepartemensId($value) 
    {
        $this->newLocationData['unit_kerjas_id'] = null; 
        $this->availableUnitsNew = UnitKerja::where('departemens_id', $value)->get()->toArray(); 
    }

    public function updatedFilterDepartemen($value)
    {
        $this->filterUnitKerja = ''; 
    }

    public function resetFilters()
    {
        $this->reset([
            'filterArea', 'filterDepartemen', 'filterUnitKerja', 
            'filterNabCahaya', 'filterNabBising', 'filterNabDebu', 'filterNabSuhuIsbb',
            'searchQuery', 'startDate', 'endDate'
        ]);
        $this->resetPage();
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
            if ($dataKey === 'cahaya') {
                return $dataValue < $nabValue;
            }
            return $dataValue > $nabValue; 
        }

        return false;
    }

    public function mount()
    {
        $this->departments = Departemen::all();
        $this->unitKerjas = UnitKerja::all();
        $this->uniqueAreas = PemantauanLingkungan::distinct()->pluck('area')->sort()->toArray();

        if (request()->has('filterArea')) {
            $this->filterArea = request()->query('filterArea');
            session()->flash('message', 'Filter Area "' . $this->filterArea . '" otomatis diterapkan dari Dashboard karena melampaui NAB.');
        }
    }

    public function downloadExcel()
    {
        $dataToExport = $this->applyFilters(PemantauanLingkungan::query()); 
        return Excel::download(new PemantauanLingkunganExport($dataToExport), 'LaporanPemantauanLingkungan_' . Carbon::now()->format('Ymd_His') . '.xlsx');
    }

    protected function applyFilters($query)
    {
        $query->with(['departemen', 'unitKerja']);

        if ($this->searchQuery) {
            $query->where('lokasi', 'like', '%' . $this->searchQuery . '%');
        }

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('tanggal_pemantauan', [$this->startDate, $this->endDate]);
        } elseif ($this->startDate) {
            $query->whereDate('tanggal_pemantauan', '>=', $this->startDate);
        } elseif ($this->endDate) {
            $query->whereDate('tanggal_pemantauan', '<=', $this->endDate);
        }

        if ($this->filterArea) {
            $query->where('area', $this->filterArea);
        }
        if ($this->filterDepartemen) {
            $query->where('departemens_id', $this->filterDepartemen);
        }
        if ($this->filterUnitKerja) {
            $query->where('unit_kerjas_id', $this->filterUnitKerja);
        }
        
        $allData = $query->latest('tanggal_pemantauan')->get();

        $filteredData = $allData->filter(function ($data) {
            $cahayaStatus = $this->checkNabStatus($data, 'cahaya', 'nab_cahaya'); 
            $bisingStatus = $this->checkNabStatus($data, 'bising', 'nab_bising');
            $debuStatus = $this->checkNabStatus($data, 'debu', 'nab_debu');
            
            $isbbIndoorStatus = $this->checkNabStatus($data, 'isbb_indoor', 'nab_suhu');
            $isbbOutdoorStatus = $this->checkNabStatus($data, 'isbb_outdoor', 'nab_suhu');
            $isbbStatus = $isbbIndoorStatus || $isbbOutdoorStatus; 

            if ($this->filterNabCahaya === 'above' && !$cahayaStatus) return false;
            if ($this->filterNabCahaya === 'below' && $cahayaStatus) return false;
            
            if ($this->filterNabBising === 'above' && !$bisingStatus) return false;
            if ($this->filterNabBising === 'below' && $bisingStatus) return false;

            if ($this->filterNabDebu === 'above' && !$debuStatus) return false;
            if ($this->filterNabDebu === 'below' && $debuStatus) return false;
            
            if ($this->filterNabSuhuIsbb === 'above' && !$isbbStatus) return false;
            if ($this->filterNabSuhuIsbb === 'below' && $isbbStatus) return false;
            
            return true;
        });

        return $filteredData; 
    }

    public function edit($id)
    {
        $data = PemantauanLingkungan::find($id);
        if ($data) {
            $this->isAddingNewLocation = false;
            $this->isEditing = true;
            $this->editingDataId = $id;
            
            $dataPemantauanAsli = is_string($data->data_pemantauan) ? json_decode($data->data_pemantauan, true) : $data->data_pemantauan;
            $dataPemantauanAman = is_array($dataPemantauanAsli) ? array_merge($this->getDefaultPemantauanData(), $dataPemantauanAsli) : $this->getDefaultPemantauanData();
            
            $this->editingData = [
                'id' => $data->id,
                'departemens_id' => $data->departemens_id, 
                'unit_kerjas_id' => $data->unit_kerjas_id, 
                'area' => $data->area,
                'lokasi' => $data->lokasi,
                'tanggal_pemantauan' => $data->tanggal_pemantauan ? Carbon::parse($data->tanggal_pemantauan)->format('Y-m-d') : '',
                'data_pemantauan' => $dataPemantauanAman,
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

    public function isBahaya($data)
    {
        return $this->checkNabStatus($data, 'cahaya', 'nab_cahaya') ||
               $this->checkNabStatus($data, 'bising', 'nab_bising') ||
               $this->checkNabStatus($data, 'debu', 'nab_debu') ||
               $this->checkNabStatus($data, 'isbb_indoor', 'nab_suhu') ||
               $this->checkNabStatus($data, 'isbb_outdoor', 'nab_suhu');
    }

    public function render()
    {
        $filteredCollection = $this->applyFilters(PemantauanLingkungan::query());
        
        $totalData = $filteredCollection->count();
        $lokasiBahaya = 0;
        foreach ($filteredCollection as $data) {
            if ($this->isBahaya($data)) {
                $lokasiBahaya++;
            }
        }
        $lokasiAman = $totalData - $lokasiBahaya;

        // 🌟 PEMBATASAN DATA (PAGINATION)
        $perPage = 15;
        $page = $this->getPage();
        $paginatedItems = new LengthAwarePaginator(
            $filteredCollection->forPage($page, $perPage),
            $totalData,
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        $pemantauanLingkunganGrouped = collect($paginatedItems->items())->groupBy('area');

        $filteredUnits = $this->unitKerjas;
        if ($this->filterDepartemen) {
            $filteredUnits = $this->unitKerjas->where('departemens_id', $this->filterDepartemen);
        }

        return view('livewire.pemantauan-lingkungan-index', [
            'pemantauanLingkunganGrouped' => $pemantauanLingkunganGrouped,
            'paginator' => $paginatedItems, 
            'totalData' => $totalData,
            'lokasiAman' => $lokasiAman,
            'lokasiBahaya' => $lokasiBahaya,
            'departments' => $this->departments, 
            'unitKerjas' => $this->unitKerjas,   
            'filteredUnits' => $filteredUnits, 
        ])->layout('layouts.app');
    }
}