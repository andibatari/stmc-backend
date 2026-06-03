@section('title', 'Pemantauan Lingkungan')
<div>
    {{-- Padding disusutkan untuk mengakomodasi tabel raksasa di layar kecil --}}
    <div class="px-2 md:px-4 py-4 md:py-6 min-h-screen">

        <div class="flex flex-col md:flex-row md:items-center justify-between mb-4 md:mb-6 gap-3">
            
            <div class="flex gap-2 w-full md:w-auto">
                <button wire:click="downloadExcel" class="flex-1 md:flex-none inline-flex items-center justify-center bg-white border border-emerald-500 text-emerald-600 font-bold py-2 px-3 rounded-lg hover:bg-emerald-50 text-xs shadow-sm">
                    <i class="fas fa-file-excel mr-1.5"></i> Excel
                </button>
                <a href="{{ route('pemantauan.create') }}" class="flex-1 md:flex-none inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-3 rounded-lg shadow-md text-xs">
                    <i class="fas fa-plus mr-1.5"></i> Tambah
                </a>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center shadow-sm mb-4 text-xs font-bold"><i class="fas fa-check-circle mr-2"></i> {{ session('message') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-3 md:p-5">
                
                {{-- Dashboard Mini dipadatkan strukturnya agar meminimalisir space atas --}}
                <div class="grid grid-cols-3 gap-2 md:gap-4 mb-5">
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-2 md:p-3 text-center">
                        <p class="text-[8px] md:text-[10px] font-bold text-blue-600 uppercase mb-0.5">Total</p>
                        <h3 class="text-sm md:text-xl font-black text-slate-800">{{ $totalData }}</h3>
                    </div>
                    <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-2 md:p-3 text-center">
                        <p class="text-[8px] md:text-[10px] font-bold text-emerald-600 uppercase mb-0.5">Aman</p>
                        <h3 class="text-sm md:text-xl font-black text-slate-800">{{ $lokasiAman }}</h3>
                    </div>
                    <div class="bg-red-50 border border-red-100 rounded-xl p-2 md:p-3 text-center">
                        <p class="text-[8px] md:text-[10px] font-bold text-red-600 uppercase mb-0.5">Bahaya</p>
                        <h3 class="text-sm md:text-xl font-black text-slate-800">{{ $lokasiBahaya }}</h3>
                    </div>
                </div>

                {{-- Filter Data: Semua jarak, padding, dan tinggi input disusutkan maksimal --}}
                <div class="bg-slate-50 rounded-xl p-3 md:p-4 border border-slate-100 mb-5">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3 mb-3 pb-3 border-b border-slate-200">
                        <div class="col-span-2">
                            <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Cari Lokasi</label>
                            <input type="text" wire:model.live.debounce.500ms="searchQuery" placeholder="Nama lokasi..." class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2.5 focus:border-red-500 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Mulai Tgl</label>
                            <input type="date" wire:model.live="startDate" class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2 focus:border-red-500">
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Sampai Tgl</label>
                            <input type="date" wire:model.live="endDate" class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2 focus:border-red-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3 mb-3">
                        <div>
                            <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Departemen</label>
                            <select wire:model.live="filterDepartemen" class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2">
                                <option value="">Semua</option>
                                @foreach ($departments as $dept) <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option> @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Unit Kerja</label>
                            <select wire:model.live="filterUnitKerja" class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2 disabled:bg-slate-100" @if(empty($filterDepartemen)) disabled @endif>
                                <option value="">Semua</option>
                                @foreach ($filteredUnits as $unit) <option value="{{ $unit->id }}">{{ $unit->nama_unit_kerja }}</option> @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Area</label>
                            <select wire:model.live="filterArea" class="w-full rounded-lg border-slate-200 text-xs py-1.5 px-2">
                                <option value="">Semua</option>
                                @foreach ($uniqueAreas as $area) <option value="{{ $area }}">{{ $area }}</option> @endforeach
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button wire:click="resetFilters" class="w-full bg-white border border-slate-300 text-xs font-bold py-1.5 px-2 rounded-lg text-slate-600 hover:bg-slate-100">Reset</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-2 mt-3 pt-3 border-t border-slate-200"> 
                        @foreach (['Cahaya' => 'filterNabCahaya', 'Bising' => 'filterNabBising', 'Debu' => 'filterNabDebu', 'Suhu' => 'filterNabSuhuIsbb'] as $label => $model)
                        <div>
                            <label class="block text-[8px] font-bold text-slate-500 uppercase mb-1">{{ $label }} (NAB)</label>
                            <select wire:model.live="{{ $model }}" class="w-full rounded-lg border-slate-200 text-[10px] py-1 px-1.5">
                                <option value="">Semua</option>
                                <option value="below">Aman</option>
                                <option value="above">Bahaya</option>
                            </select>
                        </div>
                        @endforeach
                    </div>
                </div>

                @if ($pemantauanLingkunganGrouped->count() > 0)
                    <div class="overflow-x-auto border border-slate-200 rounded-lg hide-scrollbar max-h-[500px]">
                        <table class="min-w-full divide-y divide-slate-200 bg-white text-left border-collapse whitespace-nowrap sticky-header">
                            <thead class="bg-slate-100 sticky top-0 z-10 shadow-sm">
                                <tr>
                                    <th rowspan="2" class="px-3 py-2 text-[9px] font-bold text-slate-600 uppercase border-r border-slate-200">No</th>
                                    <th rowspan="2" class="px-3 py-2 text-[9px] font-bold text-slate-600 uppercase border-r border-slate-200">Lokasi & Waktu</th>
                                    <th colspan="9" class="px-3 py-1.5 text-[9px] font-bold text-slate-600 uppercase text-center border-r border-slate-200 bg-slate-200/50">Hasil Ukur Aktual</th>
                                    <th rowspan="2" class="px-3 py-2 text-[9px] font-bold text-slate-600 uppercase text-center">Aksi</th>
                                </tr>
                                <tr class="bg-slate-50">
                                    <th class="px-2 py-1 text-[8px] font-bold text-slate-500 uppercase border-r border-slate-200">Cahaya</th>
                                    <th class="px-2 py-1 text-[8px] font-bold text-slate-500 uppercase border-r border-slate-200">Bising</th>
                                    <th class="px-2 py-1 text-[8px] font-bold text-slate-500 uppercase border-r border-slate-200">Debu</th>
                                    <th class="px-2 py-1 text-[8px] font-bold text-slate-500 uppercase border-r border-slate-200">S.Basah</th>
                                    <th class="px-2 py-1 text-[8px] font-bold text-slate-500 uppercase border-r border-slate-200">S.Kering</th>
                                    <th class="px-2 py-1 text-[8px] font-bold text-slate-500 uppercase border-r border-slate-200">S.Radiasi</th>
                                    <th class="px-2 py-1 text-[8px] font-bold text-slate-500 uppercase border-r border-slate-200">ISBB In</th>
                                    <th class="px-2 py-1 text-[8px] font-bold text-slate-500 uppercase border-r border-slate-200">ISBB Out</th>
                                    <th class="px-2 py-1 text-[8px] font-bold text-slate-500 uppercase border-r border-slate-200">RH %</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @php $globalIndex = 0; @endphp
                                @foreach ($pemantauanLingkunganGrouped as $area => $lokasis)
                                    <tr class="bg-slate-100/70 border-t border-slate-200">
                                        <td colspan="12" class="px-3 py-1.5 text-[10px] font-black text-slate-800 uppercase">
                                            <i class="fas fa-map-marker-alt text-red-500 mr-1"></i> AREA: {{ $area }}
                                        </td>
                                    </tr>
                                    @foreach ($lokasis as $data)
                                        <tr wire:key="row-{{ $data->id }}" class="hover:bg-blue-50/30">
                                            <td class="px-3 py-2 text-[10px] text-slate-500 border-r border-slate-100">{{ ++$globalIndex }}</td>
                                            <td class="px-3 py-2 border-r border-slate-100">
                                                <p class="text-[11px] font-bold text-slate-800">{{ $data->lokasi }}</p>
                                                <p class="text-[9px] text-slate-500">{{ \Carbon\Carbon::parse($data->tanggal_pemantauan)->format('d/m/y') }} | {{ $data->departemen->nama_departemen ?? '-' }}</p>
                                            </td>
                                            
                                            <td class="px-2 py-2 text-[10px] font-mono text-center border-r border-slate-100 @if ($this->checkNabStatus($data, 'cahaya', 'nab_cahaya')) bg-red-50 text-red-700 font-bold @endif">{{ $data->data_pemantauan['cahaya'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-[10px] font-mono text-center border-r border-slate-100 @if ($this->checkNabStatus($data, 'bising', 'nab_bising')) bg-red-50 text-red-700 font-bold @endif">{{ $data->data_pemantauan['bising'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-[10px] font-mono text-center border-r border-slate-100 @if ($this->checkNabStatus($data, 'debu', 'nab_debu')) bg-red-50 text-red-700 font-bold @endif">{{ $data->data_pemantauan['debu'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-[10px] font-mono text-center border-r border-slate-100">{{ $data->data_pemantauan['suhu_basah'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-[10px] font-mono text-center border-r border-slate-100">{{ $data->data_pemantauan['suhu_kering'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-[10px] font-mono text-center border-r border-slate-100">{{ $data->data_pemantauan['suhu_radiasi'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-[10px] font-mono text-center border-r border-slate-100 @if ($this->checkNabStatus($data, 'isbb_indoor', 'nab_suhu')) bg-red-50 text-red-700 font-bold @endif">{{ $data->data_pemantauan['isbb_indoor'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-[10px] font-mono text-center border-r border-slate-100 @if ($this->checkNabStatus($data, 'isbb_outdoor', 'nab_suhu')) bg-red-50 text-red-700 font-bold @endif">{{ $data->data_pemantauan['isbb_outdoor'] ?? '-' }}</td>
                                            <td class="px-2 py-2 text-[10px] font-mono text-center border-r border-slate-100">{{ $data->data_pemantauan['rh'] ?? '-' }}</td>
                                            
                                            <td class="px-3 py-2 text-center">
                                                <button wire:click="edit({{ $data->id }})" class="p-1.5 bg-blue-50 text-blue-600 rounded hover:bg-blue-600 hover:text-white mr-1"><i class="fas fa-pen text-[10px]"></i></button>
                                                <button onclick="confirm('Hapus data?') || event.stopImmediatePropagation()" wire:click="delete({{ $data->id }})" class="p-1.5 bg-red-50 text-red-600 rounded hover:bg-red-600 hover:text-white"><i class="fas fa-trash text-[10px]"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($paginator->hasPages()) <div class="mt-3">{{ $paginator->links() }}</div> @endif
                @else
                    <div class="bg-slate-50 py-8 text-center rounded-xl border border-slate-100 text-xs font-bold text-slate-500">Tidak ada data.</div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODALS DIKECILKAN UKURANNYA --}}
    @if ($isAddingNewLocation || $isEditing)
    <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm overflow-y-auto flex items-center justify-center z-[100] p-3">
        <div class="bg-white w-full max-w-4xl mx-auto rounded-xl shadow-2xl overflow-hidden"> 
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50 flex justify-between">
                <h3 class="text-sm font-black text-slate-800"><i class="fas {{ $isEditing ? 'fa-edit text-blue-500' : 'fa-plus-circle text-emerald-500' }} mr-2"></i> {{ $isEditing ? "Edit Lokasi" : 'Tambah Lokasi' }}</h3>
                <button wire:click="{{ $isEditing ? 'cancelEdit' : 'cancelAddLocation' }}" class="text-slate-400 hover:text-red-500"><i class="fas fa-times"></i></button>
            </div>

            <div class="p-4 max-h-[70vh] overflow-y-auto custom-scrollbar">
                <form wire:submit.prevent="{{ $isEditing ? 'update' : 'saveNewLocation' }}" class="space-y-4">
                    
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3"> 
                        @if($isEditing)
                            <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Departemen</label><select wire:model.live="editingData.departemens_id" class="w-full rounded-lg border-slate-200 text-xs p-2"><option value="">Pilih</option>@foreach ($departments as $dept) <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option> @endforeach</select></div>
                            <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Unit Kerja</label><select wire:model.defer="editingData.unit_kerjas_id" class="w-full rounded-lg border-slate-200 text-xs p-2">@foreach ($availableUnitsEdit as $unit) <option value="{{ $unit['id'] }}">{{ $unit['nama_unit_kerja'] }}</option> @endforeach</select></div>
                            <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Area</label><input type="text" wire:model.defer="editingData.area" class="w-full rounded-lg border-slate-200 text-xs p-2"></div>
                            <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Lokasi</label><input type="text" wire:model.defer="editingData.lokasi" class="w-full rounded-lg border-slate-200 text-xs p-2"></div>
                        @else
                            <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Departemen</label><input type="text" value="{{ $departments->find($newLocationData['departemens_id'])->nama_departemen ?? '-' }}" disabled class="w-full bg-slate-100 rounded-lg border-slate-200 text-xs p-2"></div>
                            <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Unit Kerja</label><input type="text" value="{{ $unitKerjas->find($newLocationData['unit_kerjas_id'])->nama_unit_kerja ?? '-' }}" disabled class="w-full bg-slate-100 rounded-lg border-slate-200 text-xs p-2"></div>
                            <div><label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Tgl Pemantauan</label><input type="text" value="{{ $newLocationData['tanggal_pemantauan'] ?? '' }}" disabled class="w-full bg-slate-100 rounded-lg border-slate-200 text-xs p-2"></div>
                            <div><label class="block text-[9px] font-bold text-red-500 uppercase mb-1">Nama Lokasi Baru*</label><input type="text" wire:model.defer="newLocationData.lokasi" class="w-full rounded-lg border-red-200 focus:border-red-500 text-xs p-2"></div>
                        @endif
                    </div>

                    <div class="bg-amber-50 p-3 rounded-lg border border-amber-100">
                        <h4 class="text-[9px] font-bold text-amber-700 uppercase mb-2">Batas NAB</h4>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach (['nab_cahaya' => 'Cahaya', 'nab_bising' => 'Bising', 'nab_debu' => 'Debu', 'nab_suhu' => 'Suhu'] as $key => $label)
                                <div><label class="block text-[8px] font-bold text-amber-800 mb-0.5">{{ $label }}</label><input type="{{ $key == 'nab_debu' ? 'text' : 'number' }}" step="0.01" wire:model.defer="{{ $isEditing ? 'editingData.'.$key : 'newLocationData.'.$key }}" class="w-full rounded border-amber-200 text-[10px] p-1.5"></div>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-3 md:grid-cols-5 gap-2">
                        @foreach (['cahaya'=>'Cahaya', 'bising'=>'Bising', 'debu'=>'Debu', 'suhu_basah'=>'S.Basah', 'suhu_kering'=>'S.Kering', 'suhu_radiasi'=>'S.Radiasi', 'isbb_indoor'=>'ISBB In', 'isbb_outdoor'=>'ISBB Out', 'rh'=>'RH%'] as $key => $label)
                            <div><label class="block text-[8px] font-bold text-slate-600 mb-0.5">{{ $label }}</label><input type="number" step="0.01" wire:model.defer="{{ $isEditing ? 'editingData.data_pemantauan.'.$key : 'newLocationData.pemantauan.'.$key }}" class="w-full rounded border-slate-200 text-[10px] p-1.5"></div>
                        @endforeach
                    </div>
                </form>
            </div>

            <div class="px-4 py-3 border-t border-slate-100 bg-slate-50 flex justify-end gap-2">
                <button type="button" wire:click="{{ $isEditing ? 'cancelEdit' : 'cancelAddLocation' }}" class="px-4 py-2 border border-slate-200 text-xs font-bold rounded-lg bg-white">Batal</button>
                <button type="button" wire:click="{{ $isEditing ? 'update' : 'saveNewLocation' }}" class="px-6 py-2 text-xs font-bold rounded-lg text-white bg-slate-800">Simpan</button>
            </div>
        </div>
    </div>
    @endif
    
    <style>.hide-scrollbar::-webkit-scrollbar{display:none;}</style>
</div>