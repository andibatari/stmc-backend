@section('title', 'Pemantauan Lingkungan')

<div class="container mx-auto px-4 lg:px-6">

    <div class="flex flex-col sm:flex-row justify-between items-start mb-4 gap-3 lg:gap-6">
        <h1 class="text-xl lg:text-2xl font-bold text-gray-800">Pemantauan Lingkungan</h1>
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 w-full sm:w-auto">
            <button wire:click="downloadExcel"
            class="w-full sm:w-auto bg-green-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-green-700 transition-colors duration-200 text-sm">
                Unduh Excel
            </button>
            <a href="{{ route('pemantauan.create') }}" class="w-full sm:w-auto bg-red-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-red-700 transition-colors duration-200 text-sm">
                + Tambah Data
            </a>
        </div>
    </div>

@if (session()->has('message'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 text-sm" role="alert">
        <span class="block sm:inline">{{ session('message') }}</span>
    </div>
@endif

{{-- KRITIS: BLOK FILTER DATA --}}
<div class="bg-white rounded-xl shadow-lg p-4 mb-4 lg:p-6 lg:mb-6 border border-gray-100">
    <div class="flex justify-between items-center mb-3 border-b pb-2">
        <h3 class="text-base font-bold text-gray-800 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707v7l-4 4v-7a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Filter Data Pemantauan
        </h3>
    </div>

    {{-- BARIS FILTER 1: DEPARTEMEN, UNIT, AREA, RESET (Grid 1 Kolom di Mobile) --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end mb-4">
        
        {{-- Filter Departemen --}}
        <div>
            <label for="filter_departemen" class="block text-xs font-semibold text-gray-700 mb-1">Departemen</label>
            <select id="filter_departemen" wire:model.live="filterDepartemen" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 transition duration-150 p-2 text-sm">
                <option value="">-- Semua Departemen --</option>
                @foreach ($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                @endforeach
            </select>
        </div>

        {{-- Filter Unit Kerja --}}
        <div>
            <label for="filter_unit_kerja" class="block text-xs font-semibold text-gray-700 mb-1">Unit Kerja</label>
            <select id="filter_unit_kerja" wire:model.live="filterUnitKerja" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 transition duration-150 p-2 text-sm" @if(empty($filterDepartemen)) disabled @endif>
                <option value="">-- Semua Unit Kerja --</option>
                @foreach ($filteredUnits as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->nama_unit_kerja }}</option>
                @endforeach
            </select>
        </div>

        {{-- Filter Area --}}
        <div>
            <label for="filter_area" class="block text-xs font-semibold text-gray-700 mb-1">Area</label>
            <select id="filter_area" wire:model.live="filterArea" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 transition duration-150 p-2 text-sm">
                <option value="">-- Semua Area --</option>
                @foreach ($uniqueAreas as $area)
                    <option value="{{ $area }}">{{ $area }}</option>
                @endforeach
            </select>
        </div>
        
        {{-- Tombol Reset Filter --}}
        <div class="md:col-span-1">
            <button wire:click="resetFilters" 
                    class="w-full inline-flex justify-center items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-gray-50 hover:bg-gray-100 transition-colors duration-200 mt-2 md:mt-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Reset Filter
            </button>
        </div>
    </div>
    
    <hr class="border-gray-200 mb-4">
    
    {{-- BARIS FILTER 2: STATUS NAB (Grid 2 Kolom di Mobile) --}}
    <div>
        <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            Filter Berdasarkan Status NAB
        </h4>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 items-start"> 
            
            {{-- Filter NAB Cahaya --}}
            <div>
                <label for="filter_nab_cahaya" class="block text-xs font-semibold text-gray-700 mb-1">Cahaya (Lux)</label>
                <select id="filter_nab_cahaya" wire:model.live="filterNabCahaya" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 transition duration-150 p-2 text-sm">
                    <option value="">-- Semua Status --</option>
                    <option value="below">✅ Di Bawah NAB</option>
                    <option value="above">❌ Di Atas NAB</option>
                </select>
            </div>

            {{-- Filter NAB Bising --}}
            <div>
                <label for="filter_nab_bising" class="block text-xs font-semibold text-gray-700 mb-1">Bising (dB)</label>
                <select id="filter_nab_bising" wire:model.live="filterNabBising" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 transition duration-150 p-2 text-sm">
                    <option value="">-- Semua Status --</option>
                    <option value="below">✅ Di Bawah NAB</option>
                    <option value="above">❌ Di Atas NAB</option>
                </select>
            </div>

            {{-- Filter NAB Debu --}}
            <div>
                <label for="filter_nab_debu" class="block text-xs font-semibold text-gray-700 mb-1">Debu (mg/Nm3)</label>
                <select id="filter_nab_debu" wire:model.live="filterNabDebu" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 transition duration-150 p-2 text-sm">
                    <option value="">-- Semua Status --</option>
                    <option value="below">✅ Di Bawah NAB</option>
                    <option value="above">❌ Di Atas NAB</option>
                </select>
            </div>

            {{-- Filter NAB ISBB (Suhu) --}}
            <div>
                <label for="filter_nab_suhu_isbb" class="block text-xs font-semibold text-gray-700 mb-1">ISBB</label>
                <select id="filter_nab_suhu_isbb" wire:model.live="filterNabSuhuIsbb" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 transition duration-150 p-2 text-sm">
                    <option value="">-- Semua Status --</option>
                    <option value="below">✅ Di Bawah NAB</option>
                    <option value="above">❌ Di Atas NAB</option>
                </select>
            </div>
            
        </div>
    </div>
</div>
{{-- AKHIR BLOK FILTER DATA --}}

{{-- Modal Tambah Lokasi Baru di Area Ini (Kontrol ukuran untuk modal) --}}
@if ($isAddingNewLocation)
<div class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full flex items-center justify-center z-50">
    <div class="relative p-6 bg-white w-full max-w-lg lg:max-w-4xl mx-auto rounded-lg shadow-xl"> 
        <div class="flex justify-between items-center pb-3 border-b border-gray-200 mb-4">
            <h3 class="text-lg font-bold text-gray-800">Tambah Lokasi Baru</h3>
            <button wire:click="cancelAddLocation" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>
        <form wire:submit.prevent="saveNewLocation">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4"> {{-- Departemen, Unit Kerja (Read-Only) --}}
                @foreach (['Departemen', 'Unit Kerja', 'Nama Lokasi Baru', 'Tanggal Pemantauan'] as $label)
                <div class="col-span-1">
                    <label class="block text-xs font-medium text-gray-700">{{ $label }} <span class="text-red-500">*</span></label>
                    <input type="text" value="{{ $label == 'Nama Lokasi Baru' ? '' : ($label == 'Tanggal Pemantauan' ? $newLocationData['tanggal_pemantauan'] ?? '' : ($label == 'Departemen' ? $departments->find($newLocationData['departemens_id'])->nama_departemen ?? 'N/A' : ($label == 'Unit Kerja' ? $unitKerjas->find($newLocationData['unit_kerjas_id'])->nama_unit_kerja ?? 'N/A' : '')) ) }}" 
                           @if($label != 'Nama Lokasi Baru') disabled @endif
                           @if($label == 'Nama Lokasi Baru') wire:model.defer="newLocationData.lokasi" @endif
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm p-2 {{ $label != 'Nama Lokasi Baru' ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                    @error('newLocationData.lokasi') @if($label == 'Nama Lokasi Baru') <span class="text-red-500 text-xs">{{ $message }}</span> @endif @enderror
                </div>
                @endforeach
            </div>

            <div class="border-t border-gray-200 pt-4">
                <h3 class="text-base font-bold text-gray-800 mb-3">Input Data Pengukuran & NAB (Dapat Diedit)</h3>
                
                {{-- NAB yang di-inherit --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4 border-b pb-3"> @foreach ([
                        'nab_cahaya' => 'NAB Cahaya (Lux)', 'nab_bising' => 'NAB Bising (dB)', 
                        'nab_debu' => 'NAB Debu (mg/Nm$^3$)', 'nab_suhu' => 'NAB Suhu ($^\circ$C)'
                    ] as $key => $label)
                        <div>
                            <label for="new_{{ $key }}" class="block text-xs font-medium text-gray-700">{{ $label }}</label>
                            <input type="{{ $key == 'nab_debu' ? 'text' : 'number' }}" step="0.01" id="new_{{ $key }}" wire:model.defer="newLocationData.{{ $key }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm p-2">
                            @error('newLocationData.'.$key) <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endforeach
                </div>

                {{-- Data Pemantauan --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3"> @foreach ([
                        'cahaya' => 'Cahaya (Lux)', 'bising' => 'Bising (dB)', 'debu' => 'Debu (mg/Nm$^3$)',
                        'suhu_basah' => 'Suhu Basah ($^\circ$C)', 'suhu_kering' => 'Suhu Kering ($^\circ$C)', 'suhu_radiasi' => 'Suhu Radiasi ($^\circ$C)',
                        'isbb_indoor' => 'ISBB Indoor ($^\circ$C)', 'isbb_outdoor' => 'ISBB Outdoor ($^\circ$C)', 'rh' => 'RH (%)',
                    ] as $key => $label)
                        <div>
                            <label for="new_{{ $key }}" class="block text-xs font-medium text-gray-700">{{ $label }}</label>
                            <input type="number" step="0.01" id="new_{{ $key }}" wire:model.defer="newLocationData.pemantauan.{{ $key }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm p-2">
                            @error("newLocationData.pemantauan.{$key}") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4 mt-4">
                <h3 class="text-base font-bold text-gray-800 mb-3">Kesimpulan dan Catatan (Opsional)</h3>
                <label for="new_kesimpulan" class="block text-xs font-medium text-gray-700">Kesimpulan</label>
                <textarea id="new_kesimpulan" wire:model.defer="newLocationData.kesimpulan" rows="2" placeholder="Tuliskan ringkasan atau tindakan korektif." 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2"></textarea>
                @error('newLocationData.kesimpulan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="mt-4 flex justify-end gap-3">
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

{{-- Form Edit Data Lokasi --}}
@if ($isEditing)
<div class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full flex items-center justify-center z-50">
    <div class="relative p-6 bg-white w-full max-w-lg lg:max-w-4xl mx-auto rounded-lg shadow-xl">
        <div class="flex justify-between items-center pb-3 border-b border-gray-200 mb-4">
            <h3 class="text-lg font-bold text-gray-800">Edit Data Lokasi: {{ $editingData['lokasi'] }} (Area: {{ $editingData['area'] }})</h3>
            <button wire:click="cancelEdit" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>
        <form wire:submit.prevent="update">
            
            {{-- Bagian Informasi Dasar --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 mb-4"> {{-- Departemen (Edit Select) --}}
                <div>
                    <label for="edit_departemen" class="block text-xs font-medium text-gray-700">Departemen</label>
                    <select id="edit_departemen" wire:model.live="editingData.departemens_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm p-2">
                        <option value="">Pilih Departemen</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                        @endforeach
                    </select>
                    @error('editingData.departemens_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                {{-- Unit Kerja (Edit Select) --}}
                <div>
                    <label for="edit_unit_kerja" class="block text-xs font-medium text-gray-700">Unit Kerja</label>
                    <select id="edit_unit_kerja" wire:model.defer="editingData.unit_kerjas_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm p-2" @if(empty($availableUnitsEdit)) disabled @endif>
                        <option value="">Pilih Unit Kerja</option>
                        @foreach ($availableUnitsEdit as $unit)
                            <option value="{{ $unit['id'] }}">{{ $unit['nama_unit_kerja'] }}</option>
                        @endforeach
                    </select>
                    @error('editingData.unit_kerjas_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label for="edit_area" class="block text-xs font-medium text-gray-700">Area</label>
                    <input type="text" id="edit_area" wire:model.defer="editingData.area" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm p-2">
                    @error('editingData.area') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label for="edit_lokasi" class="block text-xs font-medium text-gray-700">Lokasi</label>
                    <input type="text" id="edit_lokasi" wire:model.defer="editingData.lokasi" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm p-2">
                    @error('editingData.lokasi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="edit_tanggal_pemantauan" class="block text-xs font-medium text-gray-700">Tanggal Pemantauan</label>
                    <input type="date" id="edit_tanggal_pemantauan" wire:model.defer="editingData.tanggal_pemantauan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm p-2">
                    @error('editingData.tanggal_pemantauan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <h3 class="text-base font-bold text-gray-800 mb-3">Data Pengukuran & NAB</h3>
                
                {{-- NAB Statis Display & Input (Now Editable) --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4 border-b pb-3">
                    @foreach ([
                        'nab_cahaya' => 'NAB Cahaya (Lux)', 'nab_bising' => 'NAB Bising (dB)', 
                        'nab_debu' => 'NAB Debu (mg/Nm$^3$)', 'nab_suhu' => 'NAB Suhu ($^\circ$C)'
                    ] as $key => $label)
                        <div>
                            <label for="edit_{{ $key }}" class="block text-xs font-medium text-gray-700">{{ $label }}</label>
                            <input type="{{ $key == 'nab_debu' ? 'text' : 'number' }}" step="0.01" id="edit_{{ $key }}" wire:model.defer="editingData.{{ $key }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm p-2">
                            @error('editingData.'.$key) <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach ([
                        'cahaya' => ['label' => 'Cahaya (Lux)'], 'bising' => ['label' => 'Bising (dB)'], 'debu' => ['label' => 'Debu (mg/Nm$^3$)'],
                        'suhu_basah' => ['label' => 'Suhu Basah ($^\circ$C)'], 'suhu_kering' => ['label' => 'Suhu Kering ($^\circ$C)'], 'suhu_radiasi' => ['label' => 'Suhu Radiasi ($^\circ$C)'],
                        'isbb_indoor' => ['label' => 'ISBB Indoor ($^\circ$C)'], 'isbb_outdoor' => ['label' => 'ISBB Outdoor ($^\circ$C)'], 'rh' => ['label' => 'RH (%)'],
                    ] as $key => $field)
                        <div>
                            <label for="edit_{{ $key }}" class="block text-xs font-medium text-gray-700">{{ $field['label'] }}</label>
                            <input type="number" step="0.01" id="edit_{{ $key }}" wire:model.defer="editingData.data_pemantauan.{{ $key }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm p-2">
                            @error("editingData.data_pemantauan.{$key}") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endforeach
                </div>
            </div>
            {{-- Tambahkan bagian Kesimpulan di sini --}}
            <div class="border-t border-gray-200 pt-4 mt-4">
                <h3 class="text-base font-bold text-gray-800 mb-3">Kesimpulan dan Catatan (Opsional)</h3>
                <label for="edit_kesimpulan" class="block text-xs font-medium text-gray-700">Kesimpulan</label>
                <textarea id="edit_kesimpulan" wire:model.defer="editingData.kesimpulan" rows="2" placeholder="Tuliskan ringkasan atau tindakan korektif yang diperlukan." 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2"></textarea>
                @error('editingData.kesimpulan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="mt-4 flex justify-between gap-3">
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
<div class="bg-white rounded-xl shadow-lg p-4 overflow-x-auto mt-4 lg:p-6 lg:mt-8"> <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th rowspan="2" class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider text-left">No</th>
                    <th rowspan="2" class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider text-left">Area</th>
                    <th rowspan="2" class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider text-left hidden sm:table-cell">Lokasi</th>
                    <th rowspan="2" class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider text-left">Tanggal</th>
                    
                    <th rowspan="2" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Departemen</th>
                    <th rowspan="2" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Unit Kerja</th>
                    <th colspan="9" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center border-l border-r border-gray-200 hidden md:table-cell">Data Pengukuran</th>
                    <th rowspan="2" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Kesimpulan</th>
                    
                    <th rowspan="2" class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                </tr>
                <tr>
                    <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Cahaya</th>
                    <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Bising</th>
                    <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Debu</th>
                    <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Suhu Basah</th>
                    <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Suhu Kering</th>
                    <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Suhu Radiasi</th>
                    <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">ISBB Indoor</th>
                    <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">ISBB Outdoor</th>
                    <th class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">RH (%)</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php $globalIndex = 0; @endphp
                @foreach ($pemantauanLingkunganGrouped as $area => $lokasis)
                    <tr class="bg-gray-100 border-t border-b border-gray-300">
                        <td colspan="15" class="px-3 py-1 text-left text-xs font-bold text-gray-800 uppercase tracking-wider lg:text-sm">
                            Area: {{ $area }}
                        </td>
                    </tr>
                    @foreach ($lokasis as $data)
                        <tr class="hover:bg-red-50 transition-colors duration-150">
                            <td class="px-3 py-2 text-xs whitespace-nowrap">{{ ++$globalIndex }}</td>
                            <td class="px-3 py-2 text-xs whitespace-nowrap font-semibold">{{ $data->area }}</td>
                            <td class="px-3 py-2 text-xs whitespace-nowrap hidden sm:table-cell">{{ $data->lokasi }}</td>
                            <td class="px-3 py-2 text-xs whitespace-nowrap text-gray-500">{{ \Carbon\Carbon::parse($data->tanggal_pemantauan)->format('d M Y') }}</td>
                            
                            {{-- Kolom yang Disembunyikan/Khusus Desktop --}}
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600 hidden lg:table-cell">{{ $data->departemen->nama_departemen ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600 hidden lg:table-cell">{{ $data->unitKerja->nama_unit_kerja ?? 'N/A' }}</td>
                            
                            {{-- Data Pengukuran (Hidden di mobile) --}}
                            <td class="px-6 py-4 whitespace-nowrap text-xs hidden md:table-cell @if ($this->checkNabStatus($data, 'cahaya', 'nab_cahaya')) bg-red-200 text-red-700 font-semibold @endif">{{ $data->data_pemantauan['cahaya'] ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs hidden md:table-cell @if ($this->checkNabStatus($data, 'bising', 'nab_bising')) bg-red-200 text-red-700 font-semibold @endif">{{ $data->data_pemantauan['bising'] ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs hidden md:table-cell @if ($this->checkNabStatus($data, 'debu', 'nab_debu')) bg-red-200 text-red-700 font-semibold @endif">{{ $data->data_pemantauan['debu'] ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs hidden md:table-cell">{{ $data->data_pemantauan['suhu_basah'] ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs hidden md:table-cell">{{ $data->data_pemantauan['suhu_kering'] ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs hidden md:table-cell">{{ $data->data_pemantauan['suhu_radiasi'] ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs hidden md:table-cell @if ($this->checkNabStatus($data, 'isbb_indoor', 'nab_suhu')) bg-red-200 text-red-700 font-semibold @endif">{{ $data->data_pemantauan['isbb_indoor'] ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs hidden md:table-cell @if ($this->checkNabStatus($data, 'isbb_outdoor', 'nab_suhu')) bg-red-200 text-red-700 font-semibold @endif">{{ $data->data_pemantauan['isbb_outdoor'] ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs hidden md:table-cell">{{ $data->data_pemantauan['rh'] ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600 hidden md:table-cell">{{ $data->kesimpulan}}</td>
                            
                            {{-- Aksi (Selalu Terlihat) --}}
                            <td class="px-3 py-2 text-xs font-medium text-center">
                                <button wire:click="edit({{ $data->id }})" class="text-blue-600 hover:text-blue-900 mx-1 p-1">Edit</button>
                                <button onclick="confirm('Yakin ingin menghapus data ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $data->id }})" class="text-red-600 hover:text-red-900 mx-1 p-1">Hapus</button>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mt-4 text-sm" role="alert">
        <span class="block sm:inline">Tidak ada data pemantauan yang ditemukan berdasarkan filter yang diterapkan.</span>
    </div>
@endif

{{-- MODAL AREA (Edit/Add Location) --}}
@if ($isAddingNewLocation || $isEditing)
{{-- ... (kode modal di sini) ... --}}
@endif