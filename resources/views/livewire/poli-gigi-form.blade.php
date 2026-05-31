<div class="space-y-6">
    {{-- Notifikasi --}}
    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl flex items-center shadow-sm">
            <i class="fas fa-check-circle text-xl mr-3"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl flex items-center shadow-sm">
            <i class="fas fa-exclamation-circle text-xl mr-3"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif
    
    <form wire:submit.prevent="simpanHasil" class="space-y-6">
        
        {{-- DOKTER PEMERIKSA --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-800"><i class="fas fa-user-md text-blue-500 mr-2"></i>Dokter Pemeriksa</h3>
            </div>
            <div class="p-6">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Dokter</label>
                <select wire:model="dokterId" class="block w-full md:w-1/2 rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors">
                    <option value="">-- Pilih Dokter Gigi --</option>
                    @isset($listDokter)
                        @foreach ($listDokter as $dokter)
                            <option value="{{ $dokter->id }}">{{ $dokter->nama_lengkap ?? $dokter->name }}</option>
                        @endforeach
                    @endisset
                </select>
                @error('dokterId') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- 1. EKSTRA ORAL --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-800">1. Pemeriksaan Ekstra Oral</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kelenjar Submandibular</label>
                    <input type="text" wire:model.defer="dataForm.ekstraOral.kelenjar_submandibular" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors" placeholder="Normal / Pembesaran...">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kelenjar Leher</label>
                    <input type="text" wire:model.defer="dataForm.ekstraOral.kelenjar_leher" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors" placeholder="Normal / Pembesaran...">
                </div>
            </div>
        </div>

        {{-- 2. INTRA ORAL --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-800">2. Pemeriksaan Intra Oral</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
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
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ str_replace('_', ' ', $key) }}</label>
                            <select wire:model.defer="dataForm.intraOral.{{ $key }}" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-medium focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors cursor-pointer">
                                @foreach ($options as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                    
                    <div class="col-span-2 md:col-span-3 lg:col-span-4">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Lain - Lain (Opsional)</label>
                        <input type="text" wire:model.defer="dataForm.intraOral.lain_lain" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors" placeholder="Tambahkan catatan jika ada...">
                    </div>
                </div>
            </div>
        </div>

        {{-- PETA GIGI --}}
        <div class="bg-blue-50 rounded-2xl border border-blue-100 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-blue-200 bg-blue-100/50 flex flex-col sm:flex-row justify-between sm:items-center">
                <h3 class="text-base font-bold text-blue-900">2.1. Peta Gigi Interaktif</h3>
                <p class="text-xs font-semibold text-blue-600 bg-blue-100 px-3 py-1 rounded-full mt-2 sm:mt-0">Klik pada gigi untuk mengubah status</p>
            </div>
            
            <div class="p-6 flex flex-col items-center">
                {{-- RENDER SVG PETA GIGI --}}
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 mb-6">
                    @include('livewire.components.dental-chart-svg')
                </div>

                {{-- Keterangan Simbol --}}
                <div class="flex flex-wrap justify-center gap-4 text-xs font-bold">
                    <div class="flex items-center bg-white px-3 py-2 rounded-xl shadow-sm border border-slate-100">
                        <span class="w-5 h-5 bg-slate-100 border border-slate-300 rounded mr-2"></span> <span class="text-slate-600">Normal</span>
                    </div>
                    <div class="flex items-center bg-white px-3 py-2 rounded-xl shadow-sm border border-slate-100">
                        <span class="flex items-center justify-center w-5 h-5 bg-red-500 border border-red-700 text-white rounded mr-2 text-[10px]">⚫</span> <span class="text-red-600">Caries</span>
                    </div>
                    <div class="flex items-center bg-white px-3 py-2 rounded-xl shadow-sm border border-slate-100">
                        <span class="flex items-center justify-center w-5 h-5 bg-slate-400 border border-slate-600 text-black rounded mr-2 text-[12px]">X</span> <span class="text-slate-700">Missing</span>
                    </div>
                    <div class="flex items-center bg-white px-3 py-2 rounded-xl shadow-sm border border-slate-100">
                        <span class="flex items-center justify-center w-5 h-5 bg-green-500 border border-green-700 text-white rounded mr-2 text-[10px]">T</span> <span class="text-green-700">Tambal</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3 & 4. KESIMPULAN --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-base font-bold text-slate-800">3. Keterangan Pemeriksaan</h3>
                </div>
                <div class="p-6">
                    <textarea wire:model.defer="keterangan" rows="3" class="block w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 transition-colors placeholder-slate-300 resize-none" placeholder="Tuliskan keterangan detail hasil periksa..."></textarea>
                </div>
            </div>

            <div class="bg-amber-50 rounded-2xl border border-amber-200 overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-amber-200/50 bg-amber-100/50">
                    <h3 class="text-base font-bold text-amber-900">4. Kesimpulan Akhir</h3>
                </div>
                <div class="p-6">
                    <input type="text" wire:model.defer="kesimpulan" class="block w-full rounded-xl border-amber-300 bg-white text-sm font-bold text-amber-900 focus:border-amber-500 focus:ring-amber-500 transition-colors placeholder-amber-200" placeholder="Contoh: OHIS Baik, Karies Aktif...">
                </div>
            </div>
        </div>

        {{-- TOMBOL AKSI --}}
        <div class="flex flex-col sm:flex-row justify-end gap-4 pt-4 border-t border-slate-200">
            @if ($poliGigiResult && $poliGigiResult->file_path)
                <a href="{{ Storage::disk('gcs')->url($poliGigiResult->file_path) }}" target="_blank"
                    class="inline-flex items-center justify-center px-6 py-3 bg-white border-2 border-emerald-500 text-emerald-600 font-bold rounded-xl shadow-sm hover:bg-emerald-50 transition-all duration-200">
                    <i class="fas fa-file-pdf mr-2"></i> Laporan PDF
                </a>
            @endif
            
            <button type="submit" class="inline-flex items-center justify-center px-8 py-3 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                <span wire:loading.remove wire:target="simpanHasil"><i class="fas fa-save mr-2"></i> Simpan Form Gigi</span>
                <span wire:loading wire:target="simpanHasil"><i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...</span>
            </button>
        </div>
    </form>
</div>