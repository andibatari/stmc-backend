<div class="p-4 bg-white rounded-lg shadow-md border border-red-100">
    <h3 class="text-xl font-bold text-red-700 mb-6">Input Hasil Pemeriksaan Kebugaran</h3>

    {{-- Notifikasi --}}
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <form wire:submit.prevent="calculateAndSaveKebugaran">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            
            {{-- Data Pasien (Readonly) --}}
            <div class="col-span-1 lg:col-span-3 border-b pb-4 mb-4">
                <h4 class="text-lg font-semibold text-gray-700">Data Dasar Pasien</h4>
                <div class="grid grid-cols-2 gap-4 mt-2">
                    <div>
                        <label for="umur" class="block text-sm font-medium text-gray-500">Umur (tahun)</label>
                        <input type="text" id="umur" value="{{ $umur }}" readonly
                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 text-gray-700 sm:text-sm shadow-sm cursor-not-allowed">
                    </div>
                    <div>
                        <label for="bb" class="block text-sm font-medium text-gray-500">Berat Badan (kg)</label>
                        <input type="text" id="bb" value="{{ $bb }}" readonly
                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 text-gray-700 sm:text-sm shadow-sm cursor-not-allowed">
                    </div>
                </div>
            </div>

            {{-- Input Hasil Pemeriksaan --}}
            <div class="col-span-1">
                <label for="durasi_menit" class="block text-sm font-medium text-gray-700">Lama Pemeriksaan (Menit)</label>
                <input type="number" id="durasi_menit" wire:model.defer="durasi_menit" min="1"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                    placeholder="Contoh: 6" required>
                @error('durasi_menit') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="col-span-1">
                <label for="beban_latihan" class="block text-sm font-medium text-gray-700">Beban Latihan</label>
                <input type="text" id="beban_latihan" wire:model.defer="beban_latihan" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                    placeholder="Contoh: 3 atau Level 3" required>
                @error('beban_latihan') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            
            <div class="col-span-1">
                <label for="denyut_nadi" class="block text-sm font-medium text-gray-700">Jumlah Denyut Nadi (x/menit)</label>
                <input type="number" id="denyut_nadi" wire:model.defer="denyut_nadi" min="1"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                    placeholder="Contoh: 131" required>
                @error('denyut_nadi') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="col-span-1">
                <label for="vo2_max" class="block text-sm font-medium text-gray-700">Kebutuhan VO2 Maksimal (Liter/menit)</label>
                <input type="number" step="0.01" id="vo2_max" wire:model.defer="vo2_max" min="0.01"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                    placeholder="Contoh: 3.3" required>
                @error('vo2_max') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            
            {{-- Tombol untuk Menghitung/Menyimpan --}}
            <div class="col-span-1 lg:col-span-3 pt-4">
                <button type="submit"
                        class="w-full justify-center rounded-md border border-transparent bg-red-600 py-2 px-4 text-sm font-medium text-white shadow-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200"
                        wire:loading.attr="disabled"
                        wire:target="calculateAndSaveKebugaran">
                    <span wire:loading.remove wire:target="calculateAndSaveKebugaran">Hitung & Simpan Indeks Kebugaran</span>
                    <span wire:loading wire:target="calculateAndSaveKebugaran">Memproses Perhitungan dan Penyimpanan...</span>
                </button>
            </div>
        </div>
    </form>

    {{-- Hasil Perhitungan --}}
    @if($hasilKebugaran !== null)
    <hr class="my-8 border-red-300">
    <div class="bg-red-50 p-6 rounded-xl shadow-inner border border-red-200">
        <h4 class="text-2xl font-extrabold text-red-800 mb-4">HASIL INDEKS KEBUGARAN</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
            <div class="flex items-center p-2 bg-white rounded-md shadow-sm">
                <span class="font-medium w-48">Indeks Kebugaran Jasmani:</span>
                <span class="font-bold text-xl text-red-600">{{ number_format($hasilKebugaran, 2) }}</span>
            </div>
            <div class="flex items-center p-2 bg-white rounded-md shadow-sm">
                <span class="font-medium w-48">Kategori Kebugaran:</span>
                <span class="font-bold text-xl text-red-600">{{ $keterangan }}</span>
            </div>
        </div>

        @if(isset($kebugaranResult) && $kebugaranResult->file_path)
            <div class="mt-6 text-center">
                <a href="{{ route('pdf.kebugaran.view', ['id' => $jadwalPoliId]) }}" target="_blank"
                   class="inline-flex items-center justify-center px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-2a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2z"></path></svg>
                    Lihat Laporan Kebugaran PDF
                </a>
            </div>
        @else
            <div class="mt-6 text-center text-gray-500 text-sm">
                *Silakan klik "Hitung & Simpan Indeks Kebugaran" untuk membuat laporan PDF.
            </div>
        @endif
    </div>
    @endif
</div>
