@section('title', 'Tambah Data')

<div class="container mx-auto max-w-6xl px-4 py-8">
{{-- Tombol kembali --}}
<a href="{{ route('pemantauan.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition-colors duration-200 mb-6">
<svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
Kembali
</a>

@if (session()->has('message'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
        <span class="block sm:inline">{{ session('message') }}</span>
    </div>
@endif

<form wire:submit.prevent="simpanPemantauan">
    <div class="bg-white rounded-xl shadow-md p-6 space-y-8">
        {{-- Bagian Informasi Dasar --}}
        <div class="border-b border-gray-200 pb-6">
            <h2 class="text-xl font-bold text-gray-800">Informasi Pemantauan</h2>
            <p class="mt-1 text-sm text-gray-500">Isi detail dasar pemantauan lingkungan dan batasan ambang.</p>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Searchable Departemen dan Unit Kerja --}}
                {{-- Komponen ini akan memancarkan (emit) event yang akan ditangkap oleh parent component ini --}}
                <div class="lg:col-span-2">
                    @livewire('searchable-departemen', [
                        'initialDepartemenId' => $departemens_id, 
                        'initialUnitKerjaId' => $unit_kerjas_id
                    ], key('departemen-unit-picker'))
                    @error('departemens_id') <span class="text-red-500 text-sm mt-1 block">Departemen wajib diisi.</span> @enderror
                    @error('unit_kerjas_id') <span class="text-red-500 text-sm mt-1 block">Unit Kerja wajib diisi.</span> @enderror
                </div>
                
                {{-- Area --}}
                <div>
                    <label for="area" class="block text-sm font-medium text-gray-700">Area <span class="text-red-500">*</span></label>
                    <input type="text" id="area" wire:model.defer="area" placeholder="Contoh: Crusher 4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('area') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Baris kedua untuk Tanggal dan NAB (Sekarang Editable) --}}
            <div class="mt-6 border-t pt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                {{-- Tanggal Pemantauan --}}
                <div>
                    <label for="tanggal_pemantauan" class="block text-sm font-medium text-gray-700">Tanggal Pemantauan</label>
                    <input type="date" id="tanggal_pemantauan" wire:model.defer="tanggal_pemantauan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('tanggal_pemantauan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                {{-- NAB Cahaya (Editable) --}}
                <div>
                    <label for="nab_cahaya" class="block text-sm font-medium text-gray-700">NAB Cahaya (Lux)</label>
                    <input type="number" step="0.01" id="nab_cahaya" wire:model.defer="nabCahaya" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('nabCahaya') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                {{-- NAB Bising (Editable) --}}
                <div>
                    <label for="nab_bising" class="block text-sm font-medium text-gray-700">NAB Bising (dB)</label>
                    <input type="number" step="0.01" id="nab_bising" wire:model.defer="nabBising" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('nabBising') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                {{-- NAB Debu (Editable) --}}
                <div>
                    <label for="nab_debu" class="block text-sm font-medium text-gray-700">NAB Debu (mg/Nm3)</label>
                    <input type="text" id="nab_debu" wire:model.defer="nabDebu" placeholder="Contoh: 10 mg/Nm3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('nabDebu') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- NAB Suhu (Editable - Kolom Baru) --}}
                <div>
                    <label for="nab_suhu" class="block text-sm font-medium text-gray-700">NAB Suhu </label>
                    <input type="number" step="0.01" id="nab_suhu" wire:model.defer="nabSuhu" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('nabSuhu') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- KRITIS: Perulangan untuk setiap lokasi --}}
        @foreach ($lokasiData as $index => $lokasiItem)
        <div class="border-b border-gray-200 pb-6 pt-4 last:border-b-0 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800">Lokasi #{{ $index + 1 }}</h3>
                @if(count($lokasiData) > 1)
                    <button type="button" wire:click="removeLokasi({{ $index }})" class="text-red-600 hover:text-red-800 text-sm font-semibold">
                        Hapus Lokasi
                    </button>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="lokasi-{{ $index }}" class="block text-sm font-medium text-gray-700">Nama Lokasi <span class="text-red-500">*</span></label>
                    <input type="text" id="lokasi-{{ $index }}" wire:model.defer="lokasiData.{{ $index }}.lokasi" placeholder="Contoh: Lantai 1 / Ruang Kontrol" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error("lokasiData.{$index}.lokasi") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="pt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ([
                    'cahaya' => ['label' => 'Cahaya (Lux)', 'placeholder' => 'Contoh: 485'],
                    'bising' => ['label' => 'Bising (dB)', 'placeholder' => 'Contoh: 88.2'],
                    'debu' => ['label' => 'Debu (mg/Nm3)', 'placeholder' => 'Contoh: 6.858'],
                    'suhu_basah' => ['label' => 'Suhu Basah ', 'placeholder' => 'Contoh: 24.1'],
                    'suhu_kering' => ['label' => 'Suhu Kering ', 'placeholder' => 'Contoh: 28.1'],
                    'suhu_radiasi' => ['label' => 'Suhu Radiasi ', 'placeholder' => 'Contoh: 28.7'],
                    'isbb_indoor' => ['label' => 'ISBB Indoor ', 'placeholder' => 'Contoh: 25.4'],
                    'isbb_outdoor' => ['label' => 'ISBB Outdoor ', 'placeholder' => 'Contoh: 25.3'],
                    'rh' => ['label' => 'RH (%)', 'placeholder' => 'Contoh: 67'],
                ] as $key => $field)
                    <div>
                        <label for="{{ $key }}-{{ $index }}" class="block text-sm font-medium text-gray-700">{{ $field['label'] }}</label>
                        <input type="number" step="0.01" id="{{ $key }}-{{ $index }}" wire:model.defer="lokasiData.{{ $index }}.pemantauan.{{ $key }}" placeholder="{{ $field['placeholder'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        @error("lokasiData.{$index}.pemantauan.{$key}") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                @endforeach

                {{-- BAGIAN KESIMPULAN (BARU) --}}
                <div class="border-t border-gray-200 pt-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Kesimpulan dan Catatan</h2>
                    
                    <label for="kesimpulan" class="block text-sm font-medium text-gray-700">
                        Kesimpulan Pemantauan (Opsional)
                    </label>
                    <textarea id="kesimpulan" wire:model.defer="kesimpulan" rows="3" placeholder="Tuliskan ringkasan atau tindakan korektif yang diperlukan." 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    @error('kesimpulan') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        @endforeach

        <div class="flex justify-between items-center pt-6">
            <button type="button" wire:click="addLokasi" class="bg-blue-600 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-700 transition-colors duration-200">
                + Tambah Lokasi
            </button>
            <button type="submit" class="bg-red-600 text-white font-bold py-2 px-6 rounded-md hover:bg-red-700 transition-colors duration-200">
                <span wire:loading.remove wire:target="simpanPemantauan">Simpan Data Pemantauan</span>
                <span wire:loading wire:target="simpanPemantauan">Menyimpan...</span>
            </button>
        </div>
    </div>
</form>


</div>