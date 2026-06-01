
<div class="px-2 md:px-6 py-6 min-h-screen" x-data="{ activeTab: 'rekapitulasi' }">
    @section('title', 'Pusat Laporan & Ekspor Data')
    {{-- TOMBOL NAVIGASI TAB --}}
    <div class="max-w-7xl mx-auto mb-6 flex space-x-2 bg-white p-2 rounded-2xl shadow-sm border border-slate-100">
        <button @click="activeTab = 'rekapitulasi'" 
            :class="activeTab === 'rekapitulasi' ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-slate-500 hover:bg-slate-50 font-medium'"
            class="flex-1 py-3 px-4 rounded-xl transition-all duration-200 flex items-center justify-center">
            <i class="fa-solid fa-file-excel mr-2" :class="activeTab === 'rekapitulasi' ? 'text-emerald-500' : 'text-slate-400'"></i>
            Panel Rekapitulasi (Excel)
        </button>
        
        <button @click="activeTab = 'laporan_pdf'" 
            :class="activeTab === 'laporan_pdf' ? 'bg-red-50 text-red-700 font-bold shadow-sm' : 'text-slate-500 hover:bg-slate-50 font-medium'"
            class="flex-1 py-3 px-4 rounded-xl transition-all duration-200 flex items-center justify-center">
            <i class="fa-solid fa-file-pdf mr-2" :class="activeTab === 'laporan_pdf' ? 'text-red-500' : 'text-slate-400'"></i>
            Daftar Laporan Medis (PDF)
        </button>
    </div>

    {{-- KONTEN TAB --}}
    <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden max-w-7xl mx-auto min-h-[600px]">
        
        {{-- ==========================================
             TAB 1: PANEL EKSPOR REKAPITULASI (EXCEL)
             ========================================== --}}
        <div x-show="activeTab === 'rekapitulasi'" x-transition.opacity.duration.300ms>
            <div class="px-8 py-6 border-b border-slate-100 bg-slate-50">
                <h2 class="text-xl font-black text-slate-800 tracking-tight">Eksport Data Rekapitulasi (.xlsx)</h2>
                <p class="text-xs text-slate-500 mt-1">Gunakan filter di bawah ini untuk menarik data massal ke format Excel.</p>
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
                            <option value="">Semua Departemen</option>
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
                    <div class="absolute bottom-0 left-0 w-40 h-40 bg-emerald-500 opacity-20 rounded-full blur-3xl -ml-10 -mb-10 pointer-events-none"></div>
                    
                    <div class="mb-6 md:mb-0 relative z-10 text-center md:text-left">
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">Total Data Terekapitulasi</p>
                        <h3 class="text-5xl lg:text-6xl font-black text-white flex items-baseline justify-center md:justify-start">
                            {{ number_format($total_preview) }} 
                            <span class="text-lg font-bold text-slate-400 ml-3">Dokumen MCU</span>
                        </h3>
                    </div>
                    
                    <div class="relative z-10 w-full md:w-auto">
                        <button wire:click="exportExcel" @if($total_preview == 0) disabled @endif
                            class="w-full md:w-auto disabled:opacity-50 disabled:cursor-not-allowed bg-emerald-500 hover:bg-emerald-400 text-emerald-950 font-black py-4 px-8 rounded-2xl flex items-center justify-center shadow-[0_10px_20px_rgba(16,185,129,0.3)] hover:shadow-[0_15px_30px_rgba(16,185,129,0.4)] transition-all text-sm tracking-wide">
                            <i class="fa-solid fa-download mr-3 text-xl"></i> Download Laporan (.xlsx)
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==========================================
             TAB 2: DAFTAR LAPORAN MEDIS PASIEN (PDF)
             ========================================== --}}
        <div x-show="activeTab === 'laporan_pdf'" style="display: none;" x-transition.opacity.duration.300ms>
            <div class="px-8 py-6 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-black text-slate-800 tracking-tight">Daftar Laporan Medis Individu</h2>
                    <p class="text-xs text-slate-500 mt-1">Cari dan unduh dokumen hasil Medical Check Up pasien dalam format PDF.</p>
                </div>
            </div>
            
            <div class="p-8">
                {{-- Baris Filter & Pencarian Tabel --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="md:col-span-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-slate-400"></i>
                        </div>
                        <input type="text" wire:model.live.debounce.500ms="searchTable" placeholder="Cari Nama Pasien / No. SAP..." 
                            class="w-full pl-10 border border-slate-200 rounded-xl focus:border-red-500 focus:ring-red-500 shadow-sm py-2.5 text-sm">
                    </div>
                    <div>
                        <select wire:model.live="tableDept" class="w-full border border-slate-200 rounded-xl focus:border-red-500 focus:ring-red-500 shadow-sm py-2.5 text-sm">
                            <option value="">Semua Departemen</option>
                            @foreach($listDepartemen as $dept) <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        {{-- Dropdown Unit Kerja akan disabled jika Departemen belum dipilih --}}
                        <select wire:model.live="tableUnit" 
                            class="w-full border border-slate-200 rounded-xl focus:border-red-500 focus:ring-red-500 shadow-sm py-2.5 text-sm transition-colors {{ !$tableDept ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white' }}" 
                            {{ !$tableDept ? 'disabled' : '' }}>
                            
                            <option value="">Semua Unit Kerja</option>
                            
                            @isset($listUnitKerja)
                                @foreach($listUnitKerja as $unit) 
                                    <option value="{{ $unit->id }}">{{ $unit->nama_unit_kerja }}</option> 
                                @endforeach
                            @endisset
                        </select>
                    </div>
                </div>

                {{-- Tabel Data --}}
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider">
                                <th class="p-4 font-bold border-b border-slate-200">No</th>
                                <th class="p-4 font-bold border-b border-slate-200">Tgl MCU</th>
                                <th class="p-4 font-bold border-b border-slate-200">No. SAP / NIK</th>
                                <th class="p-4 font-bold border-b border-slate-200">Nama Pasien</th>
                                <th class="p-4 font-bold border-b border-slate-200">Departemen</th>
                                <th class="p-4 font-bold border-b border-slate-200">Status</th>
                                <th class="p-4 font-bold border-b border-slate-200 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($jadwalTable as $index => $jadwal)
                                <tr class="hover:bg-slate-50 transition-colors border-b border-slate-100">
                                    <td class="p-4 text-slate-500">{{ $jadwalTable->firstItem() + $index }}</td>
                                    <td class="p-4 font-medium text-slate-800">{{ \Carbon\Carbon::parse($jadwal->tanggal_mcu)->format('d/m/Y') }}</td>
                                    <td class="p-4 text-slate-600">{{ $jadwal->karyawan->no_sap ?? $jadwal->pesertaMcu->nik_pasien ?? '-' }}</td>
                                    <td class="p-4 font-bold text-slate-800">{{ $jadwal->karyawan->nama_karyawan ?? $jadwal->pesertaMcu->nama_lengkap ?? $jadwal->nama_pasien }}</td>
                                    <td class="p-4 text-slate-600">{{ $jadwal->karyawan->departemen->nama_departemen ?? 'Non-PTST' }}</td>
                                    <td class="p-4">
                                        @if($jadwal->status == 'Finished')
                                            <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold">Selesai</span>
                                        @else
                                            <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-xs font-bold">Proses</span>
                                        @endif
                                    </td>
                                    <td class="p-4 flex items-center justify-center gap-2">
                                        {{-- Tombol Biru: Lihat Detail Profil Web --}}
                                        <a href="{{ route('qr-patient-detail', ['jadwal' => $jadwal->id]) }}" 
                                            class="bg-blue-50 hover:bg-blue-500 text-blue-600 hover:text-white border border-blue-200 py-1.5 px-3 rounded-lg text-xs font-bold transition-colors">
                                            <i class="fa-solid fa-eye"></i> Detail
                                        </a>
                                        {{-- Tombol Merah: Unduh PDF Gabungan --}}
                                        <a href="{{ route('download.mcu.summary', $jadwal->id) }}" target="_blank"
                                            class="bg-red-50 border border-red-200 text-red-600 hover:bg-red-600 hover:text-white hover:border-red-600 py-1.5 px-3 rounded-lg text-xs font-bold transition-all shadow-sm flex items-center">
                                            <i class="fa-solid fa-file-pdf mr-1.5"></i> Unduh PDF
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-slate-500 italic">Tidak ada data pasien yang sesuai pencarian.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $jadwalTable->links() }}
                </div>

            </div>
        </div>

    </div>
</div>