@section('title', 'Pemantauan Lingkungan')
<meta http-equiv="refresh" content="10">
{{-- ROOT ELEMENT LIVEWIRE: Semua elemen WAJIB berada di dalam div ini --}}
<div>
    <div class="px-2 md:px-4 py-4 min-h-screen">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 lg:mb-8 gap-4">
            <div>
                <h1 class="text-2xl lg:text-3xl font-black text-slate-800">Pemantauan Lingkungan</h1>
                <p class="text-sm font-medium text-slate-500 mt-1">Kelola data ambang batas (NAB) lingkungan kerja.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <button wire:click="downloadExcel" class="inline-flex items-center justify-center bg-white border-2 border-emerald-500 text-emerald-600 font-bold py-2.5 px-5 rounded-xl hover:bg-emerald-50 transition-all text-sm shadow-sm">
                    <i class="fas fa-file-excel mr-2"></i> Ekspor Excel
                </button>
                <a href="{{ route('pemantauan.create') }}" class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-lg shadow-red-600/30 hover:-translate-y-0.5 transition-all text-sm">
                    <i class="fas fa-plus mr-2"></i> Tambah Data Baru
                </a>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl flex items-center shadow-sm mb-6 animate-fade-in">
                <i class="fas fa-check-circle text-xl mr-3"></i>
                <span class="font-bold text-sm">{{ session('message') }}</span>
            </div>
        @endif

        {{-- KONTEN UTAMA --}}
        <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
            <div class="p-6 md:p-8">
                
                {{-- SUMMARY CARDS (DASHBOARD MINI) --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
                    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 flex items-center shadow-sm">
                        <div class="w-12 h-12 rounded-full bg-blue-500 text-white flex items-center justify-center text-xl shadow-inner mr-4"><i class="fas fa-clipboard-list"></i></div>
                        <div>
                            <p class="text-[10px] font-bold text-blue-600 uppercase tracking-wider mb-0.5">Total Pemantauan</p>
                            <h3 class="text-2xl font-black text-slate-800">{{ $totalData }} <span class="text-xs font-medium text-slate-500">Data</span></h3>
                        </div>
                    </div>
                    <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-5 flex items-center shadow-sm">
                        <div class="w-12 h-12 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xl shadow-inner mr-4"><i class="fas fa-shield-alt"></i></div>
                        <div>
                            <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider mb-0.5">Aman (Di Bawah NAB)</p>
                            <h3 class="text-2xl font-black text-slate-800">{{ $lokasiAman }} <span class="text-xs font-medium text-slate-500">Lokasi</span></h3>
                        </div>
                    </div>
                    <div class="bg-red-50 border border-red-100 rounded-2xl p-5 flex items-center shadow-sm">
                        <div class="w-12 h-12 rounded-full bg-red-500 text-white flex items-center justify-center text-xl shadow-inner mr-4"><i class="fas fa-exclamation-triangle"></i></div>
                        <div>
                            <p class="text-[10px] font-bold text-red-600 uppercase tracking-wider mb-0.5">Bahaya (Di Atas NAB)</p>
                            <h3 class="text-2xl font-black text-slate-800">{{ $lokasiBahaya }} <span class="text-xs font-medium text-slate-500">Lokasi</span></h3>
                        </div>
                    </div>
                </div>

                {{-- FILTER DATA --}}
                <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 mb-8">
                    <h3 class="text-sm font-bold text-slate-700 mb-4 border-b border-slate-200 pb-3 flex items-center">
                        <i class="fas fa-filter text-blue-500 mr-2"></i> Filter Data Lokasi
                    </h3>

                    {{-- FILTER PENCARIAN & TANGGAL (BARU) --}}
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-4 pb-4 border-b border-slate-200 border-dashed">
                        <div class="md:col-span-6 lg:col-span-6">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Cari Lokasi Spesifik</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fas fa-search text-slate-400"></i></div>
                                <input type="text" wire:model.live.debounce.500ms="searchQuery" placeholder="Ketik nama lokasi..." class="block w-full pl-10 rounded-xl border border-slate-200 bg-white text-xs font-medium focus:border-red-500 focus:ring-red-500 p-2.5">
                            </div>
                        </div>
                        <div class="md:col-span-3 lg:col-span-3">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Dari Tanggal</label>
                            <input type="date" wire:model.live="startDate" class="block w-full rounded-xl border border-slate-200 bg-white text-xs font-medium focus:border-red-500 focus:ring-red-500 p-2.5">
                        </div>
                        <div class="md:col-span-3 lg:col-span-3">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Sampai Tanggal</label>
                            <input type="date" wire:model.live="endDate" class="block w-full rounded-xl border border-slate-200 bg-white text-xs font-medium focus:border-red-500 focus:ring-red-500 p-2.5">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Departemen</label>
                            <select wire:model.live="filterDepartemen" class="block w-full rounded-xl border border-slate-200 bg-white text-sm font-medium focus:border-red-500 focus:ring-red-500 p-2.5 transition-colors cursor-pointer">
                                <option value="">Semua Departemen</option>
                                @foreach ($departments as $dept) <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option> @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Unit Kerja</label>
                            <select wire:model.live="filterUnitKerja" class="block w-full rounded-xl border border-slate-200 bg-white text-sm font-medium focus:border-red-500 focus:ring-red-500 p-2.5 transition-colors cursor-pointer disabled:bg-slate-100" @if(empty($filterDepartemen)) disabled @endif>
                                <option value="">Semua Unit Kerja</option>
                                @foreach ($filteredUnits as $unit) <option value="{{ $unit->id }}">{{ $unit->nama_unit_kerja }}</option> @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Area</label>
                            <select wire:model.live="filterArea" class="block w-full rounded-xl border border-slate-200 bg-white text-sm font-medium focus:border-red-500 focus:ring-red-500 p-2.5 transition-colors cursor-pointer">
                                <option value="">Semua Area</option>
                                @foreach ($uniqueAreas as $area) <option value="{{ $area }}">{{ $area }}</option> @endforeach
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button wire:click="resetFilters" class="w-full inline-flex justify-center items-center px-4 py-2.5 border-2 border-slate-200 text-sm font-bold rounded-xl text-slate-600 bg-white hover:bg-slate-100 transition-colors duration-200">
                                <i class="fas fa-undo mr-2"></i> Reset Filter
                            </button>
                        </div>
                    </div>

                    {{-- FILTER NAB --}}
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 mt-6 pt-4 border-t border-slate-200 flex items-center">
                        <i class="fas fa-exclamation-triangle text-amber-500 mr-2"></i> Filter Indikator NAB
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4"> 
                        @foreach (['Cahaya' => 'filterNabCahaya', 'Bising' => 'filterNabBising', 'Debu' => 'filterNabDebu', 'ISBB' => 'filterNabSuhuIsbb'] as $label => $model)
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ $label }}</label>
                            <select wire:model.live="{{ $model }}" class="block w-full rounded-xl border border-slate-200 bg-white text-xs font-semibold focus:border-red-500 focus:ring-red-500 p-2 transition-colors cursor-pointer">
                                <option value="">Semua Status</option>
                                <option value="below">✅ Aman (Di Bawah NAB)</option>
                                <option value="above">❌ Bahaya (Di Atas NAB)</option>
                            </select>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-2xl flex items-start md:items-center shadow-sm mb-6 text-xs font-bold md:hidden">
                    <i class="fas fa-mobile-alt text-lg mr-3 mt-0.5 md:mt-0"></i>
                    <span>Putar HP Anda ke posisi horizontal (landscape) untuk melihat tabel dengan lengkap.</span>
                </div>

                {{-- TABEL DATA --}}
                @if ($pemantauanLingkunganGrouped->count() > 0)
                    <div class="overflow-x-auto border border-slate-100 rounded-2xl">
                        <table class="min-w-full divide-y divide-slate-100 bg-white text-left border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th rowspan="2" class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">No</th>
                                    <th rowspan="2" class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Area & Lokasi</th>
                                    <th rowspan="2" class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                                    <th rowspan="2" class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Dept / Unit Kerja</th>
                                    <th colspan="9" class="px-4 py-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-center border-l border-r border-slate-200 hidden xl:table-cell bg-slate-100/50">Indikator Pengukuran</th>
                                    <th rowspan="2" class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider hidden md:table-cell">Kesimpulan</th>
                                    <th rowspan="2" class="px-4 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                                </tr>
                                <tr class="bg-slate-50/80">
                                    <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase hidden xl:table-cell border-l border-slate-200">Cahaya</th>
                                    <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase hidden xl:table-cell">Bising</th>
                                    <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase hidden xl:table-cell">Debu</th>
                                    <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase hidden xl:table-cell">S. Basah</th>
                                    <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase hidden xl:table-cell">S. Kering</th>
                                    <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase hidden xl:table-cell">S. Radiasi</th>
                                    <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase hidden xl:table-cell">ISBB In</th>
                                    <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase hidden xl:table-cell">ISBB Out</th>
                                    <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase hidden xl:table-cell border-r border-slate-200">RH %</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @php $globalIndex = 0; @endphp
                                @foreach ($pemantauanLingkunganGrouped as $area => $lokasis)
                                    <tr class="bg-slate-100 border-t border-b border-slate-200">
                                        <td colspan="15" class="px-4 py-2 text-left text-xs font-black text-slate-700 uppercase tracking-wider">
                                            <i class="fas fa-map-marker-alt mr-2 text-red-500"></i> AREA: {{ $area }}
                                        </td>
                                    </tr>
                                    @foreach ($lokasis as $data)
                                        <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                            <td class="px-4 py-3 text-sm font-medium text-slate-500">{{ ++$globalIndex }}</td>
                                            <td class="px-4 py-3 text-sm font-bold text-slate-800">{{ $data->lokasi }}</td>
                                            <td class="px-4 py-3 text-sm text-slate-600 font-semibold whitespace-nowrap">{{ \Carbon\Carbon::parse($data->tanggal_pemantauan)->format('d M Y') }}</td>
                                            
                                            <td class="px-4 py-3 text-xs text-slate-500 hidden lg:table-cell">
                                                <span class="block font-bold text-slate-700">{{ $data->departemen->nama_departemen ?? 'N/A' }}</span>
                                                {{ $data->unitKerja->nama_unit_kerja ?? 'N/A' }}
                                            </td>
                                            
                                            {{-- Data Pengukuran --}}
                                            <td class="px-3 py-3 text-xs font-mono text-center hidden xl:table-cell @if ($this->checkNabStatus($data, 'cahaya', 'nab_cahaya')) bg-red-50 text-red-700 font-bold border border-red-200 @endif">{{ $data->data_pemantauan['cahaya'] ?? '-' }}</td>
                                            <td class="px-3 py-3 text-xs font-mono text-center hidden xl:table-cell @if ($this->checkNabStatus($data, 'bising', 'nab_bising')) bg-red-50 text-red-700 font-bold border border-red-200 @endif">{{ $data->data_pemantauan['bising'] ?? '-' }}</td>
                                            <td class="px-3 py-3 text-xs font-mono text-center hidden xl:table-cell @if ($this->checkNabStatus($data, 'debu', 'nab_debu')) bg-red-50 text-red-700 font-bold border border-red-200 @endif">{{ $data->data_pemantauan['debu'] ?? '-' }}</td>
                                            <td class="px-3 py-3 text-xs font-mono text-center hidden xl:table-cell">{{ $data->data_pemantauan['suhu_basah'] ?? '-' }}</td>
                                            <td class="px-3 py-3 text-xs font-mono text-center hidden xl:table-cell">{{ $data->data_pemantauan['suhu_kering'] ?? '-' }}</td>
                                            <td class="px-3 py-3 text-xs font-mono text-center hidden xl:table-cell">{{ $data->data_pemantauan['suhu_radiasi'] ?? '-' }}</td>
                                            <td class="px-3 py-3 text-xs font-mono text-center hidden xl:table-cell @if ($this->checkNabStatus($data, 'isbb_indoor', 'nab_suhu')) bg-red-50 text-red-700 font-bold border border-red-200 @endif">{{ $data->data_pemantauan['isbb_indoor'] ?? '-' }}</td>
                                            <td class="px-3 py-3 text-xs font-mono text-center hidden xl:table-cell @if ($this->checkNabStatus($data, 'isbb_outdoor', 'nab_suhu')) bg-red-50 text-red-700 font-bold border border-red-200 @endif">{{ $data->data_pemantauan['isbb_outdoor'] ?? '-' }}</td>
                                            <td class="px-3 py-3 text-xs font-mono text-center hidden xl:table-cell">{{ $data->data_pemantauan['rh'] ?? '-' }}</td>
                                            
                                            <td class="px-4 py-3 text-xs text-slate-500 hidden md:table-cell max-w-[150px] truncate" title="{{ $data->kesimpulan }}">{{ $data->kesimpulan ?? '-' }}</td>
                                            
                                            <td class="px-4 py-3 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button wire:click="edit({{ $data->id }})" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all" title="Edit"><i class="fas fa-pen text-xs"></i></button>
                                                    <button onclick="confirm('Yakin ingin menghapus data ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $data->id }})" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all" title="Hapus"><i class="fas fa-trash text-xs"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- NAVIGASI PAGINASI --}}
                    @if ($paginator->hasPages())
                        <div class="mt-6">
                            {{ $paginator->links() }}
                        </div>
                    @endif
                @else
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl py-12 flex flex-col items-center justify-center">
                        <i class="fas fa-box-open text-4xl text-slate-300 mb-4"></i>
                        <p class="text-slate-500 font-bold text-sm">Tidak ada data pemantauan yang sesuai dengan filter.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODALS (EDIT & TAMBAH AREA) DI DALAM ROOT --}}
    @if ($isAddingNewLocation || $isEditing)
    <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm overflow-y-auto h-full w-full flex items-center justify-center z-[100] p-4">
        <div class="relative bg-white w-full max-w-lg lg:max-w-5xl mx-auto rounded-[2rem] shadow-2xl overflow-hidden animate-fade-in"> 
            {{-- Header Modal --}}
            <div class="px-8 py-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h3 class="text-lg font-black text-slate-800 flex items-center">
                    <i class="fas {{ $isEditing ? 'fa-edit text-blue-500' : 'fa-plus-circle text-emerald-500' }} mr-3"></i> 
                    {{ $isEditing ? "Edit Data Lokasi: {$editingData['lokasi']}" : 'Tambah Lokasi Baru' }}
                </h3>
                <button wire:click="{{ $isEditing ? 'cancelEdit' : 'cancelAddLocation' }}" class="w-8 h-8 bg-white border border-slate-200 rounded-full text-slate-400 hover:text-red-500 hover:border-red-200 flex items-center justify-center transition-colors"><i class="fas fa-times"></i></button>
            </div>

            {{-- Body Modal --}}
            <div class="p-8 max-h-[75vh] overflow-y-auto custom-scrollbar">
                <form wire:submit.prevent="{{ $isEditing ? 'update' : 'saveNewLocation' }}" class="space-y-8">
                    
                    {{-- Info Dasar --}}
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">A. Informasi Lokasi</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4"> 
                            @if($isEditing)
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Departemen</label>
                                    <select wire:model.live="editingData.departemens_id" class="block w-full rounded-xl border border-slate-200 bg-slate-50 text-sm focus:border-blue-500 focus:ring-blue-500 p-2.5">
                                        <option value="">Pilih Departemen</option>
                                        @foreach ($departments as $dept) <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option> @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Unit Kerja</label>
                                    <select wire:model.defer="editingData.unit_kerjas_id" class="block w-full rounded-xl border border-slate-200 bg-slate-50 text-sm focus:border-blue-500 focus:ring-blue-500 p-2.5">
                                        @foreach ($availableUnitsEdit as $unit) <option value="{{ $unit['id'] }}">{{ $unit['nama_unit_kerja'] }}</option> @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Area</label>
                                    <input type="text" wire:model.defer="editingData.area" class="block w-full rounded-xl border border-slate-200 bg-white text-sm focus:border-blue-500 focus:ring-blue-500 p-2.5">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Lokasi</label>
                                    <input type="text" wire:model.defer="editingData.lokasi" class="block w-full rounded-xl border border-slate-200 bg-white text-sm focus:border-blue-500 focus:ring-blue-500 p-2.5">
                                </div>
                                <div class="md:col-span-2 lg:col-span-4">
                                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Tanggal Pemantauan</label>
                                    <input type="date" wire:model.defer="editingData.tanggal_pemantauan" class="block w-full lg:w-1/4 rounded-xl border border-slate-200 bg-white text-sm font-semibold focus:border-blue-500 focus:ring-blue-500 p-2.5">
                                </div>
                            @else
                                {{-- Add Form Readonly fields --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Departemen</label>
                                    <input type="text" value="{{ $departments->find($newLocationData['departemens_id'])->nama_departemen ?? 'N/A' }}" disabled class="block w-full rounded-xl border border-slate-200 bg-slate-100 text-slate-500 font-semibold text-sm p-2.5 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Unit Kerja</label>
                                    <input type="text" value="{{ $unitKerjas->find($newLocationData['unit_kerjas_id'])->nama_unit_kerja ?? 'N/A' }}" disabled class="block w-full rounded-xl border border-slate-200 bg-slate-100 text-slate-500 font-semibold text-sm p-2.5 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Tanggal Pemantauan</label>
                                    <input type="text" value="{{ $newLocationData['tanggal_pemantauan'] ?? '' }}" disabled class="block w-full rounded-xl border border-slate-200 bg-slate-100 text-slate-500 font-semibold text-sm p-2.5 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-red-500 mb-1.5">Nama Lokasi Baru *</label>
                                    <input type="text" wire:model.defer="newLocationData.lokasi" placeholder="Cth: Ruang Genset" class="block w-full rounded-xl border border-slate-200 bg-white text-sm font-bold focus:border-red-500 focus:ring-red-500 p-2.5 shadow-sm">
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="h-px bg-slate-100 w-full"></div>

                    {{-- Batas NAB --}}
                    <div class="bg-amber-50 rounded-2xl p-5 border border-amber-100">
                        <h4 class="text-xs font-bold text-amber-700 uppercase tracking-widest mb-4 flex items-center"><i class="fas fa-ruler-horizontal mr-2"></i> B. Batas NAB Acuan</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach (['nab_cahaya' => 'Cahaya (Lux)', 'nab_bising' => 'Bising (dB)', 'nab_debu' => 'Debu (mg/Nm3)', 'nab_suhu' => 'Suhu (°C)'] as $key => $label)
                                <div>
                                    <label class="block text-xs font-bold text-amber-800 mb-1.5">{{ $label }}</label>
                                    <input type="{{ $key == 'nab_debu' ? 'text' : 'number' }}" step="0.01" wire:model.defer="{{ $isEditing ? 'editingData.'.$key : 'newLocationData.'.$key }}" class="block w-full rounded-xl border border-amber-200 bg-white text-sm font-mono focus:border-amber-500 focus:ring-amber-500 p-2.5">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Data Pengukuran --}}
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">C. Data Pengukuran Aktual</h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                            @foreach ([
                                'cahaya' => 'Cahaya (Lux)', 'bising' => 'Bising (dB)', 'debu' => 'Debu (mg/Nm3)',
                                'suhu_basah' => 'Suhu Basah (°C)', 'suhu_kering' => 'Suhu Kering (°C)', 'suhu_radiasi' => 'Suhu Radiasi (°C)',
                                'isbb_indoor' => 'ISBB Indoor (°C)', 'isbb_outdoor' => 'ISBB Outdoor (°C)', 'rh' => 'RH (%)',
                            ] as $key => $label)
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1.5">{{ $label }}</label>
                                    <input type="number" step="0.01" wire:model.defer="{{ $isEditing ? 'editingData.data_pemantauan.'.$key : 'newLocationData.pemantauan.'.$key }}" class="block w-full rounded-xl border border-slate-200 bg-white text-sm font-mono focus:border-blue-500 focus:ring-blue-500 p-2.5">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Kesimpulan --}}
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">D. Catatan & Kesimpulan</h4>
                        @if($isEditing)
                            <textarea wire:model="editingData.kesimpulan" rows="3" placeholder="Tambahkan catatan khusus di sini..." class="block w-full rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 p-3.5 resize-none"></textarea>
                        @else
                            <textarea wire:model="newLocationData.kesimpulan" rows="3" placeholder="Tambahkan catatan khusus di sini..." class="block w-full rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 p-3.5 resize-none"></textarea>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Footer Modal --}}
            <div class="px-8 py-5 border-t border-slate-100 bg-slate-50 flex items-center justify-between gap-4">
                @if ($isEditing)
                    <button type="button" wire:click="startAddLocation" class="hidden md:inline-flex items-center px-4 py-2.5 bg-emerald-50 text-emerald-700 font-bold text-sm rounded-xl border border-emerald-200 hover:bg-emerald-100 transition-colors">
                        <i class="fas fa-plus mr-2"></i> Tambah Lokasi Baru
                    </button>
                @else
                    <div></div> {{-- Spacer --}}
                @endif
                
                <div class="flex gap-3 w-full md:w-auto">
                    <button type="button" wire:click="{{ $isEditing ? 'cancelEdit' : 'cancelAddLocation' }}" class="flex-1 md:flex-none px-6 py-2.5 border-2 border-slate-200 text-sm font-bold rounded-xl text-slate-600 bg-white hover:bg-slate-50 transition-colors">Batal</button>
                    <button type="button" wire:click="{{ $isEditing ? 'update' : 'saveNewLocation' }}" class="flex-1 md:flex-none px-8 py-2.5 text-sm font-bold rounded-xl text-white bg-slate-800 hover:bg-slate-700 shadow-lg hover:-translate-y-0.5 transition-all">
                        {{ $isEditing ? 'Simpan Perubahan' : 'Simpan Lokasi' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- KODE STYLE DI DALAM DIV ROOT --}}
    <style>
        .animate-fade-in { animation: fadeIn 0.2s ease-out; }
        .animate-slide-up { animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    </style>
</div>