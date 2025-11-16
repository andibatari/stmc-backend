@section('title', 'Pemantauan Lingkungan')

<div class="container mx-auto px-4">
<div class="flex justify-between items-center mb-6">
<h1 class="text-2xl font-bold text-gray-800">Pemantauan Lingkungan</h1>
<div class="space-x-4">
<button wire:click="downloadExcel"
class="bg-green-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-green-700 transition-colors duration-200">
Unduh Excel
</button>
<a href="{{ route('pemantauan.create') }}" class="bg-red-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-red-700 transition-colors duration-200">
+ Tambah Data
</a>
</div>
</div>

@if (session()->has('message'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('message') }}</span>
    </div>
@endif

{{-- KRITIS: BLOK FILTER DATA (Tampilan Baru) --}}

<div class="bg-white rounded-xl shadow-lg p-6 mb-6 border border-gray-100">
    <div class="flex justify-between items-center mb-4 border-b pb-3">
        <h3 class="text-lg font-extrabold text-gray-800 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707v7l-4 4v-7a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Filter Data Pemantauan
        </h3>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        
        {{-- Filter Departemen --}}
        <div>
            <label for="filter_departemen" class="block text-sm font-semibold text-gray-700 mb-1">Departemen</label>
            <select id="filter_departemen" wire:model.live="filterDepartemen" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 transition duration-150">
                <option value="">-- Semua Departemen --</option>
                @foreach ($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                @endforeach
            </select>
        </div>

        {{-- Filter Unit Kerja --}}
        <div>
            <label for="filter_unit_kerja" class="block text-sm font-semibold text-gray-700 mb-1">Unit Kerja</label>
            <select id="filter_unit_kerja" wire:model.live="filterUnitKerja" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 transition duration-150" @if(empty($filterDepartemen)) disabled @endif>
                <option value="">-- Semua Unit Kerja --</option>
                {{-- Menggunakan $filteredUnits yang sudah dihitung di component render() --}}
                @foreach ($filteredUnits as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->nama_unit_kerja }}</option>
                @endforeach
            </select>
        </div>

        {{-- Filter Area --}}
        <div>
            <label for="filter_area" class="block text-sm font-semibold text-gray-700 mb-1">Area</label>
            <select id="filter_area" wire:model.live="filterArea" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 transition duration-150">
                <option value="">-- Semua Area --</option>
                @foreach ($uniqueAreas as $area)
                    <option value="{{ $area }}">{{ $area }}</option>
                @endforeach
            </select>
        </div>
        
        {{-- Tombol Reset Filter --}}
        <div class="md:col-span-1">
            <button wire:click="resetFilters" 
                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Reset Filter
            </button>
        </div>
    </div>
</div>
{{-- AKHIR BLOK FILTER DATA (Tampilan Baru) --}}

{{-- Modal Tambah Lokasi Baru di Area Ini --}}
@if ($isAddingNewLocation)
<div class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full flex items-center justify-center z-50">
    <div class="relative p-8 bg-white w-full max-w-4xl mx-auto rounded-lg shadow-xl">
        <div class="flex justify-between items-center pb-3 border-b border-gray-200 mb-4">
            <h3 class="text-xl font-bold text-gray-800">Tambah Lokasi Baru di Area: {{ $newLocationData['area'] ?? 'N/A' }}</h3>
            <button wire:click="cancelAddLocation" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form wire:submit.prevent="saveNewLocation">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                {{-- Departemen (Read-Only) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Departemen</label>
                    <input type="text" value="{{ $departments->find($newLocationData['departemens_id'])->nama_departemen ?? 'N/A' }}" disabled class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 cursor-not-allowed">
                </div>
                {{-- Unit Kerja (Read-Only) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Unit Kerja</label>
                    <input type="text" value="{{ $unitKerjas->find($newLocationData['unit_kerjas_id'])->nama_unit_kerja ?? 'N/A' }}" disabled class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 cursor-not-allowed">
                </div>
                {{-- Nama Lokasi Baru --}}
                <div>
                    <label for="new_lokasi" class="block text-sm font-medium text-gray-700">Nama Lokasi Baru <span class="text-red-500">*</span></label>
                    <input type="text" id="new_lokasi" wire:model.defer="newLocationData.lokasi" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('newLocationData.lokasi') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Pemantauan</label>
                    <input type="date" value="{{ $newLocationData['tanggal_pemantauan'] ?? '' }}" disabled class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 cursor-not-allowed">
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Input Data Pengukuran & NAB (Dapat Diedit)</h3>
                
                {{-- NAB yang di-inherit --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 border-b pb-4">
                    <div>
                        <label for="new_nab_cahaya" class="block text-sm font-medium text-gray-700">NAB Cahaya (Lux)</label>
                        <input type="number" step="0.01" id="new_nab_cahaya" wire:model.defer="newLocationData.nab_cahaya" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @error('newLocationData.nab_cahaya') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="new_nab_bising" class="block text-sm font-medium text-gray-700">NAB Bising (dB)</label>
                        <input type="number" step="0.01" id="new_nab_bising" wire:model.defer="newLocationData.nab_bising" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @error('newLocationData.nab_bising') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="new_nab_debu" class="block text-sm font-medium text-gray-700">NAB Debu (mg/Nm$^3$)</label>
                        <input type="text" id="new_nab_debu" wire:model.defer="newLocationData.nab_debu" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @error('newLocationData.nab_debu') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="new_nab_suhu" class="block text-sm font-medium text-gray-700">NAB Suhu ($^\circ$C)</label>
                        <input type="number" step="0.01" id="new_nab_suhu" wire:model.defer="newLocationData.nab_suhu" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @error('newLocationData.nab_suhu') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ([
                        'cahaya' => ['label' => 'Cahaya (Lux)'], 'bising' => ['label' => 'Bising (dB)'], 'debu' => ['label' => 'Debu (mg/Nm$^3$)'],
                        'suhu_basah' => ['label' => 'Suhu Basah ($^\circ$C)'], 'suhu_kering' => ['label' => 'Suhu Kering ($^\circ$C)'], 'suhu_radiasi' => ['label' => 'Suhu Radiasi ($^\circ$C)'],
                        'isbb_indoor' => ['label' => 'ISBB Indoor ($^\circ$C)'], 'isbb_outdoor' => ['label' => 'ISBB Outdoor ($^\circ$C)'], 'rh' => ['label' => 'RH (%)'],
                    ] as $key => $field)
                        <div>
                            <label for="new_{{ $key }}" class="block text-sm font-medium text-gray-700">{{ $field['label'] }}</label>
                            <input type="number" step="0.01" id="new_{{ $key }}" wire:model.defer="newLocationData.pemantauan.{{ $key }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error("newLocationData.pemantauan.{$key}") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6 mt-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Kesimpulan dan Catatan (Opsional)</h3>
                <label for="new_kesimpulan" class="block text-sm font-medium text-gray-700">Kesimpulan</label>
                <textarea id="new_kesimpulan" wire:model.defer="newLocationData.kesimpulan" rows="3" placeholder="Tuliskan ringkasan atau tindakan korektif." 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"></textarea>
                @error('newLocationData.kesimpulan') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="mt-8 flex justify-end gap-4">
                <button type="button" wire:click="cancelAddLocation" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700">
                    Simpan Lokasi Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- KRITIS: Form Edit Data Lokasi --}}
@if ($isEditing)
<div class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full flex items-center justify-center z-50">
    <div class="relative p-8 bg-white w-full max-w-4xl mx-auto rounded-lg shadow-xl">
        <div class="flex justify-between items-center pb-3 border-b border-gray-200 mb-4">
            <h3 class="text-xl font-bold text-gray-800">Edit Data Lokasi: {{ $editingData['lokasi'] }} (Area: {{ $editingData['area'] }})</h3>
            <button wire:click="cancelEdit" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form wire:submit.prevent="update">
            
            {{-- Bagian Informasi Dasar --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                
                {{-- Departemen (Edit Select) --}}
                <div>
                    <label for="edit_departemen" class="block text-sm font-medium text-gray-700">Departemen</label>
                    <select id="edit_departemen" wire:model.live="editingData.departemens_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Pilih Departemen</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                        @endforeach
                    </select>
                    @error('editingData.departemens_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                {{-- Unit Kerja (Edit Select) --}}
                <div>
                    <label for="edit_unit_kerja" class="block text-sm font-medium text-gray-700">Unit Kerja</label>
                    <select id="edit_unit_kerja" wire:model.defer="editingData.unit_kerjas_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" @if(empty($availableUnitsEdit)) disabled @endif>
                        <option value="">Pilih Unit Kerja</option>
                        @foreach ($availableUnitsEdit as $unit)
                            <option value="{{ $unit['id'] }}">{{ $unit['nama_unit_kerja'] }}</option>
                        @endforeach
                    </select>
                    @error('editingData.unit_kerjas_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label for="edit_area" class="block text-sm font-medium text-gray-700">Area</label>
                    <input type="text" id="edit_area" wire:model.defer="editingData.area" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('editingData.area') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label for="edit_lokasi" class="block text-sm font-medium text-gray-700">Lokasi</label>
                    <input type="text" id="edit_lokasi" wire:model.defer="editingData.lokasi" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('editingData.lokasi') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="edit_tanggal_pemantauan" class="block text-sm font-medium text-gray-700">Tanggal Pemantauan</label>
                    <input type="date" id="edit_tanggal_pemantauan" wire:model.defer="editingData.tanggal_pemantauan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('editingData.tanggal_pemantauan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Data Pengukuran & NAB</h3>
                
                {{-- NAB Statis Display & Input (Now Editable) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 border-b pb-4">
                    <div>
                        <label for="edit_nab_cahaya" class="block text-sm font-medium text-gray-700">NAB Cahaya (Lux)</label>
                        <input type="number" step="0.01" id="edit_nab_cahaya" wire:model.defer="editingData.nab_cahaya" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @error('editingData.nab_cahaya') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="edit_nab_bising" class="block text-sm font-medium text-gray-700">NAB Bising (dB)</label>
                        <input type="number" step="0.01" id="edit_nab_bising" wire:model.defer="editingData.nab_bising" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @error('editingData.nab_bising') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="edit_nab_debu" class="block text-sm font-medium text-gray-700">NAB Debu (mg/Nm$^3$)</label>
                        <input type="text" id="edit_nab_debu" wire:model.defer="editingData.nab_debu" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @error('editingData.nab_debu') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="edit_nab_suhu" class="block text-sm font-medium text-gray-700">NAB Suhu ($^\circ$C)</label>
                        <input type="number" step="0.01" id="edit_nab_suhu" wire:model.defer="editingData.nab_suhu" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @error('editingData.nab_suhu') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ([
                        'cahaya' => ['label' => 'Cahaya (Lux)'], 'bising' => ['label' => 'Bising (dB)'], 'debu' => ['label' => 'Debu (mg/Nm$^3$)'],
                        'suhu_basah' => ['label' => 'Suhu Basah ($^\circ$C)'], 'suhu_kering' => ['label' => 'Suhu Kering ($^\circ$C)'], 'suhu_radiasi' => ['label' => 'Suhu Radiasi ($^\circ$C)'],
                        'isbb_indoor' => ['label' => 'ISBB Indoor ($^\circ$C)'], 'isbb_outdoor' => ['label' => 'ISBB Outdoor ($^\circ$C)'], 'rh' => ['label' => 'RH (%)'],
                    ] as $key => $field)
                        <div>
                            <label for="edit_{{ $key }}" class="block text-sm font-medium text-gray-700">{{ $field['label'] }}</label>
                            <input type="number" step="0.01" id="edit_{{ $key }}" wire:model.defer="editingData.data_pemantauan.{{ $key }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                    @endforeach
                </div>
            </div>
            {{-- Tambahkan bagian Kesimpulan di sini --}}
            <div class="border-t border-gray-200 pt-6 mt-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Kesimpulan dan Catatan (Opsional)</h3>
                <label for="edit_kesimpulan" class="block text-sm font-medium text-gray-700">Kesimpulan</label>
                <textarea id="edit_kesimpulan" wire:model.defer="editingData.kesimpulan" rows="3" placeholder="Tuliskan ringkasan atau tindakan korektif yang diperlukan." 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"></textarea>
                @error('editingData.kesimpulan') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="mt-8 flex justify-between gap-4">
                {{-- KRITIS: Tombol Tambah Lokasi Baru --}}
                <button type="button" wire:click="startAddLocation" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                    + Tambah Lokasi di Area {{ $editingData['area'] }}
                </button>
                
                <div>
                    <button type="button" wire:click="cancelEdit" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                        Perbarui
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Tampilan Tabel Index --}}
@if ($pemantauanLingkunganGrouped->count() > 0)
<div class="bg-white rounded-xl shadow-2xl p-6 overflow-x-auto mt-8">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th rowspan="2" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                <th rowspan="2" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Area</th>
                <th rowspan="2" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                
                {{-- HEADER BARU --}}
                <th rowspan="2" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departemen</th>
                <th rowspan="2" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Kerja</th>
                
                <th rowspan="2" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                <th colspan="9" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center border-l border-r border-gray-200">Data Pengukuran</th>
                <th rowspan="2" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kesimpulan</th>
                <th rowspan="2" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cahaya (Lux)</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bising (dB)</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Debu (mg/Nm$^3$)</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Suhu Basah ($^\circ$C)</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Suhu Kering ($^\circ$C)</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Suhu Radiasi ($^\circ$C)</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ISBB Indoor ($^\circ$C)</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ISBB Outdoor ($^\circ$C)</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RH (%)</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @php $globalIndex = 0; @endphp
            @foreach ($pemantauanLingkunganGrouped as $area => $lokasis)
                <tr class="bg-gray-100 border-t border-b border-gray-300">
                    <td colspan="18" class="px-6 py-2 text-left text-sm font-bold text-gray-800 uppercase tracking-wider">
                        Area: {{ $area }}
                    </td>
                </tr>
                @foreach ($lokasis as $data)
                    <tr class="hover:bg-red-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">{{ ++$globalIndex }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ $data->area }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $data->lokasi }}</td>

                        {{-- KRITIS: Tampilkan Departemen & Unit Kerja --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $data->departemen->nama_departemen ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $data->unitKerja->nama_unit_kerja ?? 'N/A' }}</td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($data->tanggal_pemantauan)->format('d M Y') }}</td>
                        
                        {{-- Data Pengukuran dengan Warna NAB --}}
                        <td class="px-6 py-4 whitespace-nowrap @if ($this->checkNabStatus($data, 'cahaya', 'nab_cahaya')) bg-red-200 text-red-700 font-semibold @endif">{{ $data->data_pemantauan['cahaya'] ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap @if ($this->checkNabStatus($data, 'bising', 'nab_bising')) bg-red-200 text-red-700 font-semibold @endif">{{ $data->data_pemantauan['bising'] ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap @if ($this->checkNabStatus($data, 'debu', 'nab_debu')) bg-red-200 text-red-700 font-semibold @endif">{{ $data->data_pemantauan['debu'] ?? 'N/A' }}</td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">{{ $data->data_pemantauan['suhu_basah'] ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $data->data_pemantauan['suhu_kering'] ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $data->data_pemantauan['suhu_radiasi'] ?? 'N/A' }}</td>
                        
                        <td class="px-6 py-4 whitespace-nowrap @if ($this->checkNabStatus($data, 'isbb_indoor', 'nab_suhu')) bg-red-200 text-red-700 font-semibold @endif">{{ $data->data_pemantauan['isbb_indoor'] ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap @if ($this->checkNabStatus($data, 'isbb_outdoor', 'nab_suhu')) bg-red-200 text-red-700 font-semibold @endif">{{ $data->data_pemantauan['isbb_outdoor'] ?? 'N/A' }}</td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">{{ $data->data_pemantauan['rh'] ?? 'N/A' }}</td>
                        {{-- kesimpulan --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $data->kesimpulan}}</td>


                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            <button wire:click="edit({{ $data->id }})" class="text-blue-600 hover:text-blue-900 mx-1">Edit</button>
                            <button onclick="confirm('Apakah Anda yakin ingin menghapus data ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $data->id }})" class="text-red-600 hover:text-red-900 mx-1">Hapus</button>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>
@else
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mt-8" role="alert">
        <span class="block sm:inline">Tidak ada data pemantauan yang ditemukan berdasarkan filter yang diterapkan.</span>
    </div>
@endif


</div>