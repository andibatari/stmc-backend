<div class="px-2 md:px-6 py-6 min-h-screen">
    <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden max-w-7xl mx-auto">
        
        {{-- Header Gradasi Minimalis --}}
        <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h2 class="text-xl lg:text-2xl font-black text-slate-800 flex items-center tracking-tight">
                <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center mr-3 shadow-sm"><i class="fa-solid fa-file-export text-lg"></i></div>
                Generator Laporan Kolektif
            </h2>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5 mb-8">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Mulai Tanggal</label>
                    <input type="date" wire:model.live="date_start" class="w-full border border-slate-200 bg-white rounded-xl focus:border-red-500 focus:ring-red-500 shadow-sm p-3 text-sm font-medium transition-colors">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Sampai Tanggal</label>
                    <input type="date" wire:model.live="date_end" class="w-full border border-slate-200 bg-white rounded-xl focus:border-red-500 focus:ring-red-500 shadow-sm p-3 text-sm font-medium transition-colors">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Filter Departemen</label>
                    <select wire:model.live="departemens_id" class="w-full border border-slate-200 bg-white rounded-xl focus:border-red-500 focus:ring-red-500 shadow-sm p-3 text-sm font-medium transition-colors cursor-pointer">
                        <option value="">Semua Departemen (Karyawan)</option>
                        @foreach($listDepartemen as $dept) <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Kategori Peserta</label>
                    <select wire:model.live="tipe_anggota" {{ $departemens_id ? 'disabled' : '' }} class="w-full border border-slate-200 rounded-xl focus:border-red-500 focus:ring-red-500 shadow-sm p-3 text-sm font-medium transition-colors cursor-pointer {{ $departemens_id ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white' }}">
                        <option value="">Semua Kategori</option>
                        @foreach($listKategori as $kat) <option value="{{ $kat }}">{{ $kat }}</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Status Akhir MCU</label>
                    <select wire:model.live="status_kehadiran" class="w-full border border-slate-200 bg-white rounded-xl focus:border-red-500 focus:ring-red-500 shadow-sm p-3 text-sm font-medium transition-colors cursor-pointer">
                        <option value="">Semua Status</option>
                        @foreach($statusLabels as $value => $label) <option value="{{ $value }}">{{ $label }}</option> @endforeach
                    </select>
                </div>
            </div>

            {{-- Info Notice --}}
            <div class="flex items-start bg-amber-50 border border-amber-100 p-4 rounded-2xl mb-8">
                <i class="fa-solid fa-circle-info text-amber-500 mt-0.5 mr-3 text-lg"></i>
                <p class="text-xs font-medium text-amber-800 leading-relaxed">
                    <strong>Penting:</strong> Memilih "Filter Departemen" secara otomatis memfilter data <strong class="font-black">Khusus Karyawan PTST</strong>. Kosongkan pilihan departemen jika Anda ingin mengekspor data keluarga atau pasien umum (Non-PTST).
                </p>
            </div>

            {{-- Big Result Card --}}
            <div class="bg-slate-800 rounded-3xl p-8 flex flex-col md:flex-row items-center justify-between text-white shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -mr-20 -mt-20 pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-40 h-40 bg-red-500 opacity-20 rounded-full blur-3xl -ml-10 -mb-10 pointer-events-none"></div>
                
                <div class="mb-6 md:mb-0 relative z-10 text-center md:text-left">
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">Total Data Terekapitulasi</p>
                    <h3 class="text-5xl lg:text-6xl font-black text-white flex items-baseline justify-center md:justify-start">
                        {{ number_format($total_preview) }} 
                        <span class="text-lg font-bold text-slate-400 ml-3">Dokumen MCU</span>
                    </h3>
                </div>
                
                <div class="relative z-10 w-full md:w-auto">
                    <button wire:click="exportExcel" @if($total_preview == 0) disabled @endif
                        class="w-full md:w-auto disabled:opacity-50 disabled:cursor-not-allowed bg-emerald-500 hover:bg-emerald-400 text-emerald-950 font-black py-4 px-8 rounded-2xl flex items-center justify-center shadow-[0_10px_20px_rgba(16,185,129,0.3)] hover:shadow-[0_15px_30px_rgba(16,185,129,0.4)] hover:-translate-y-1 transform active:scale-95 transition-all text-sm tracking-wide">
                        <i class="fa-solid fa-file-excel mr-3 text-xl"></i> Unduh Laporan Excel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>