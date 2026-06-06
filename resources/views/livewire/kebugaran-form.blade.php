<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <div class="px-6 py-5 border-b border-slate-100 bg-red-50/50 flex items-center justify-between">
        <h3 class="text-lg font-black text-red-800"><i class="fas fa-heartbeat text-red-500 mr-2"></i> Indeks Kebugaran Pasien</h3>
    </div>

    <div class="p-6 md:p-8">
        {{-- Notifikasi --}}
        @if (session()->has('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-xl flex items-center shadow-sm mb-6">
                <i class="fas fa-check-circle text-xl mr-3"></i><span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl flex items-center shadow-sm mb-6">
                <i class="fas fa-exclamation-triangle text-xl mr-3"></i><span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <form wire:submit.prevent="calculateAndSaveKebugaran">
            
            {{-- DATA DASAR (READONLY) --}}
            <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl mb-8 flex flex-col md:flex-row gap-6">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Usia Pasien (Tahun)</label>
                    <input type="text" value="{{ $umur }}" readonly class="block w-full rounded-xl border-slate-200 bg-slate-100/50 font-bold text-slate-600 text-sm shadow-sm cursor-not-allowed">
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Berat Badan (Kg)</label>
                    <input type="text" value="{{ $bb }}" readonly class="block w-full rounded-xl border-slate-200 bg-slate-100/50 font-bold text-slate-600 text-sm shadow-sm cursor-not-allowed">
                </div>
            </div>

            {{-- INPUT INDIKATOR --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Durasi Uji (Menit)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fas fa-stopwatch text-slate-400"></i></div>
                        <input type="number" wire:model.defer="durasi_menit" min="1" required placeholder="Contoh: 6"
                            class="block w-full pl-10 rounded-xl border-slate-300 bg-white text-sm font-bold focus:border-red-500 focus:ring-red-500 transition-colors shadow-sm py-3">
                    </div>
                    @error('durasi_menit') <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Beban Latihan</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fas fa-dumbbell text-slate-400"></i></div>
                        <input type="text" wire:model.defer="beban_latihan" required placeholder="Level 1-5"
                            class="block w-full pl-10 rounded-xl border-slate-300 bg-white text-sm font-bold focus:border-red-500 focus:ring-red-500 transition-colors shadow-sm py-3">
                    </div>
                    @error('beban_latihan') <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Denyut Nadi (bpm)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fas fa-heart text-red-400"></i></div>
                        <input type="number" wire:model.defer="denyut_nadi" min="1" required placeholder="Contoh: 120"
                            class="block w-full pl-10 rounded-xl border-slate-300 bg-white text-sm font-bold focus:border-red-500 focus:ring-red-500 transition-colors shadow-sm py-3">
                    </div>
                    @error('denyut_nadi') <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">VO2 Max (L/mnt)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fas fa-lungs text-blue-400"></i></div>
                        <input type="number" step="0.01" wire:model.defer="vo2_max" min="0.01" required placeholder="Contoh: 3.3"
                            class="block w-full pl-10 rounded-xl border-slate-300 bg-white text-sm font-bold focus:border-red-500 focus:ring-red-500 transition-colors shadow-sm py-3">
                    </div>
                    @error('vo2_max') <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
            
            <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-red-600 text-white rounded-xl font-black tracking-wide shadow-lg shadow-red-600/30 hover:bg-red-700 hover:-translate-y-0.5 transition-all duration-200" wire:loading.attr="disabled" wire:target="calculateAndSaveKebugaran">
                <span wire:loading.remove wire:target="calculateAndSaveKebugaran"><i class="fas fa-calculator mr-2"></i> Hitung & Simpan Kebugaran</span>
                <span wire:loading wire:target="calculateAndSaveKebugaran"><i class="fas fa-circle-notch fa-spin mr-2"></i> Kalkulasi Data Sistem...</span>
            </button>
        </form>

        {{-- HASIL PERHITUNGAN (BANNER) --}}
        @if($hasilKebugaran !== null)
            <div class="mt-10 bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
                {{-- Efek Dekorasi Lingkaran Latar --}}
                <div class="absolute top-0 right-0 -mr-8 -mt-8 w-40 h-40 bg-white/5 rounded-full blur-2xl"></div>
                <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-32 h-32 bg-red-500/20 rounded-full blur-2xl"></div>
                
                <h4 class="text-sm font-black text-slate-400 tracking-widest uppercase mb-6 relative z-10">Summary Indeks Kebugaran</h4>
                
                <div class="flex flex-col lg:flex-row items-center justify-between gap-8 relative z-10">
                    
                    <div class="flex items-center gap-8 w-full lg:w-auto">
                        <div class="text-center">
                            <p class="text-xs font-bold text-slate-400 uppercase mb-1">Skor Indeks</p>
                            <p class="text-5xl font-black text-white">{{ number_format($hasilKebugaran, 2) }}</p>
                        </div>
                        <div class="h-16 w-px bg-slate-700 hidden sm:block"></div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase mb-1">Kategori Klinis</p>
                            <span class="inline-block px-5 py-2 rounded-xl text-lg font-black tracking-wide
                                @if(stripos($keterangan, 'Baik') !== false) bg-emerald-500/20 text-emerald-400 border border-emerald-500/50
                                @elseif(stripos($keterangan, 'Sedang') !== false) bg-amber-500/20 text-amber-400 border border-amber-500/50
                                @else bg-red-500/20 text-red-400 border border-red-500/50 @endif">
                                {{ $keterangan }}
                            </span>
                        </div>
                    </div>

                    @if($kebugaranDataId && isset($kebugaranResult->file_path))
                        <div class="w-full lg:w-auto">
                            {{-- Menggunakan asset() untuk mengakses file di folder public/storage --}}
                            <a href="{{ asset('storage/' . $kebugaranResult->file_path) }}" target="_blank"
                                class="flex items-center justify-center px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-xl font-bold transition-all backdrop-blur-sm group">
                                
                                {{-- Ikon diganti dari cloud-download ke file-pdf agar lebih tepat --}}
                                <i class="fas fa-file-pdf text-lg mr-3 group-hover:text-red-400 transition-colors"></i> 
                                
                                Akses Laporan PDF 
                            </a>
                        </div>
                    @endif

                </div>
            </div>
        @endif
    </div>
</div>