<div class="space-y-6">
    {{-- Notifikasi --}}
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    
    <form wire:submit.prevent="simpanHasil" class="space-y-6">
        
        <div class="p-4 border rounded-lg bg-white shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-3">Dokter Pemeriksa</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700">Pilih Dokter</label>
                <select wire:model="dokterId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">-- Pilih Dokter --</option>
                    @isset($listDokter)
                        @foreach ($listDokter as $dokter)
                            <option value="{{ $dokter->id }}">{{ $dokter->nama_lengkap ?? $dokter->name }}</option>
                        @endforeach
                    @endisset
                </select>
                @error('dokterId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>
        {{-- 1. Pemeriksaan Ekstra Oral --}}
        <div class="p-4 border rounded-lg bg-white shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-3">1. PEMERIKSAAN EKSTRA ORAL</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kelenjar Submandibular</label>
                    <input type="text" wire:model.defer="dataForm.ekstraOral.kelenjar_submandibular" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kelenjar Leher</label>
                    <input type="text" wire:model.defer="dataForm.ekstraOral.kelenjar_leher" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
            </div>
        </div>

        {{-- 2. Pemeriksaan Intra Oral --}}
        <div class="p-4 border rounded-lg bg-white shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-3">2. PEMERIKSAAN INTRA ORAL</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @php
                    $intraOralOptions = [
                        'oklusi' => ['Normal', 'Cross Bite', 'Deep Bite'],
                        'torus_palatinus' => ['Tidak ada', 'Kecil', 'Sedang', 'Besar', 'Multiple'],
                        'torus_mandibularis' => ['Tidak ada', 'Sisi Kiri', 'Sisi Kanan', 'Kedua Sisi'],
                        'palatum' => ['Dalam/Sedang/Rendah', 'Tinggi', 'Normal'],
                        'diastema' => ['Tidak Ada', 'Ada'],
                        'gigi_anomali' => ['Tidak Ada', 'Ada'],
                        'ginggiva' => ['Normal/Gingivitis', 'Radang'],
                        'karang_gigi' => ['Tak ada', 'Ada'],
                    ];
                @endphp
                
                @foreach ($intraOralOptions as $key => $options)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $key) }}</label>
                        <select wire:model.defer="dataForm.intraOral.{{ $key }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                            @foreach ($options as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Lain - Lain</label>
                    <input type="text" wire:model.defer="dataForm.intraOral.lain_lain" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
            </div>
        </div>

        {{-- Peta Gigi Interaktif --}}
        <div class="my-6 p-4 border rounded-lg bg-white shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-3">2.1. Peta Gigi (Klik untuk mengubah status klinis)</h3>
            <p class="text-xs text-gray-500 mb-4">Urutan Klik: Normal &rarr; Caries (Berlubang) &rarr; Missing (Hilang) &rarr; Tambal (Restorasi) &rarr; Normal</p>
            
            <div class="flex justify-center flex-col items-center">
                {{-- RENDER SVG PETA GIGI --}}
                @include('livewire.components.dental-chart-svg')

                {{-- Keterangan Simbol --}}
                <div class="mt-4 flex flex-wrap justify-center space-x-6 text-sm">
                    <div class="flex items-center space-x-1">
                        <span class="inline-flex items-center justify-center bg-gray-200 text-gray-700 w-6 h-6 border-2 border-gray-400 rounded-md"></span>
                        <span class="font-semibold">Normal</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <span class="inline-flex items-center justify-center bg-red-500 text-white w-6 h-6 border-2 border-red-700 rounded-md relative">⚫</span>
                        <span class="font-semibold text-red-600">Caries (Berlubang)</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <span class="inline-flex items-center justify-center bg-gray-400 text-black w-6 h-6 border-2 border-gray-600 rounded-md relative font-black text-lg">X</span>
                        <span class="font-semibold text-gray-700">Missing (Hilang)</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <span class="inline-flex items-center justify-center bg-green-500 text-white w-6 h-6 border-2 border-green-700 rounded-md relative font-bold text-md">T</span>
                        <span class="font-semibold text-green-700">Tambal (Restorasi)</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Keterangan Hasil Pemeriksaan --}}
        <div class="p-4 border rounded-lg bg-white shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-3">3. KETERANGAN HASIL PEMERIKSAAN</h3>
            <textarea wire:model.defer="keterangan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
        </div>

        {{-- 4. Kesimpulan --}}
        <div class="p-4 border rounded-lg bg-white shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 mb-3">4. KESIMPULAN</h3>
            <input type="text" wire:model.defer="kesimpulan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex justify-end space-x-4 pt-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md shadow-md transition-all duration-200">
                <span wire:loading.remove wire:target="simpanHasil">Simpan Hasil Pemeriksaan</span>
                <span wire:loading wire:target="simpanHasil">Menyimpan...</span>
            </button>
            
            {{-- Tombol Lihat/Unduh PDF akan muncul otomatis setelah file_path terisi --}}
            @if ($poliGigiResult && $poliGigiResult->file_path)
                <a href="{{ route('pdf.view', ['id' => $poliGigiResult->jadwal_poli_id]) }}" target="_blank"
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md shadow-md transition-all duration-200">
                    Lihat Laporan PDF
                </a>
            @endif
        </div>
    </form>
</div>
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('open-pdf-tab', ({ url }) => { 
            window.open(url, '_blank');
        });
    });
</script>
