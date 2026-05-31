<div class="w-full max-w-7xl mx-auto">
    <div class="flex flex-col lg:flex-row gap-6">
        
        {{-- Kolom Kiri: Ringkasan --}}
        <div class="w-full lg:w-[350px] shrink-0 space-y-6">
            {{-- KARTU PROFIL --}}
            <div class="bg-white p-6 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-br from-blue-600 to-indigo-800 opacity-90 rounded-t-[2rem]"></div>
                
                <div class="relative mt-6 mb-4">
                    <div class="w-24 h-24 mx-auto bg-slate-100 rounded-full p-1 shadow-xl ring-4 ring-white flex items-center justify-center text-slate-300">
                        <i class="fas fa-user text-4xl"></i>
                    </div>
                </div>

                <h2 class="text-xl font-black text-slate-800">{{ $pesertaMcu->nama_lengkap }}</h2>
                <p class="text-xs font-bold text-blue-600 mt-1 uppercase tracking-widest">{{ $pesertaMcu->tipe_anggota ?? 'Pasien Umum' }}</p>
                <p class="text-xs font-medium text-slate-500 mt-1">{{ $pesertaMcu->perusahaan_asal ?? 'Umum' }}</p>

                <div class="mt-6 flex flex-col gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-100 text-left">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400">NIK (KTP)</span>
                        <span class="text-sm font-mono font-black text-slate-700">{{ $pesertaMcu->nik_pasien ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400">SAP / NIP</span>
                        <span class="text-sm font-mono font-black text-slate-700">{{ $pesertaMcu->no_sap ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- KARTU KARYAWAN UTAMA --}}
            @if ($pesertaMcu->karyawan_id)
            <div class="bg-white p-6 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-4 border-b border-slate-100 pb-3 flex items-center">
                    <i class="fas fa-user-tie text-slate-400 mr-2"></i> Karyawan Utama
                </h3>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Nama Lengkap</p>
                    <p class="font-bold text-slate-800 text-sm mb-3">{{ $pesertaMcu->karyawan->nama_karyawan ?? 'N/A' }}</p>
                    
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Unit / Departemen</p>
                    <p class="font-bold text-slate-800 text-sm mb-3">{{ $pesertaMcu->karyawan->unitKerja->nama_unit_kerja ?? 'N/A' }}</p>
                    
                    <a href="{{ route('karyawan.show', ['karyawan' => $pesertaMcu->karyawan_id]) }}" class="block w-full py-2.5 text-center text-xs font-bold text-white bg-slate-800 rounded-xl hover:bg-slate-700 transition-colors mt-2">Buka Profil Karyawan</a>
                </div>
            </div>
            @endif
        </div>

        {{-- Kolom Kanan: TABS & KONTEN --}}
        <div class="w-full flex-1">
            <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden min-h-[500px]">
                
                <div class="flex p-2 m-4 bg-slate-100 rounded-2xl w-max">
                    <button wire:click="changeTab('data')" class="px-6 py-2.5 rounded-xl font-bold text-sm transition-all duration-200 @if($activeTab === 'data') bg-white text-slate-800 shadow-sm @else text-slate-500 hover:text-slate-700 @endif">
                        <i class="fas fa-id-card mr-2 opacity-70"></i> Data Lengkap
                    </button>
                    <button wire:click="changeTab('riwayat')" class="px-6 py-2.5 rounded-xl font-bold text-sm transition-all duration-200 @if($activeTab === 'riwayat') bg-white text-slate-800 shadow-sm @else text-slate-500 hover:text-slate-700 @endif">
                        <i class="fas fa-notes-medical mr-2 opacity-70"></i> Histori MCU
                    </button>
                </div>

                <div class="p-6 md:p-8 pt-2">
                    @if ($activeTab === 'data')
                        @if ($pesertaMcu)
                            <div class="animate-fade-in">
                                @include('livewire.partials.keluarga-data-view', ['pesertaMcu' => $pesertaMcu])
                            </div>
                        @endif
                    @endif
                    
                    @if ($activeTab === 'riwayat')
                        <div class="animate-fade-in">
                            <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                                <h3 class="font-black text-lg text-slate-800">Daftar Kunjungan Medical Check-Up</h3>
                                <div class="flex items-center gap-3">
                                    <label class="text-xs font-bold text-slate-500 uppercase">Tahun:</label>
                                    <select wire:model.live="selectedYear" class="block rounded-xl border border-slate-200 bg-white shadow-sm text-sm font-bold p-2 focus:border-red-500 focus:ring-red-500 cursor-pointer">
                                        <option value="">Semua Riwayat</option>
                                        @for ($year = date('Y'); $year >= 2020; $year--) <option value="{{ $year }}">{{ $year }}</option> @endfor
                                    </select>
                                </div>
                            </div>

                            {{-- Desktop Table --}}
                            <div class="hidden md:block overflow-x-auto border border-slate-100 rounded-2xl">
                                <table class="min-w-full text-sm bg-white border-collapse text-left">
                                    <thead class="bg-slate-50 border-b border-slate-100">
                                        <tr>
                                            <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">No</th>
                                            <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal MCU</th>
                                            <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Dokter PIC</th>
                                            <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                            <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Hasil</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50">
                                        @forelse ($filteredRecords as $index => $jadwalMcu)
                                        <tr class="hover:bg-slate-50/50">
                                            <td class="py-4 px-4 font-medium text-slate-500">{{ $index + 1 }}</td>
                                            <td class="py-4 px-4 font-bold text-slate-700">{{ \Carbon\Carbon::parse($jadwalMcu->tanggal_mcu)->format('d M Y') }}</td>
                                            <td class="py-4 px-4 font-medium text-slate-600">{{ $jadwalMcu->dokter->nama_lengkap ?? 'N/A' }}</td>
                                            <td class="py-4 px-4">
                                                <span class="px-3 py-1 rounded-full text-[10px] font-bold border @if($jadwalMcu->status === 'Scheduled') bg-amber-50 text-amber-600 border-amber-200 @elseif($jadwalMcu->status === 'Finished') bg-emerald-50 text-emerald-600 border-emerald-200 @else bg-slate-50 text-slate-600 border-slate-200 @endif">{{ $jadwalMcu->status ?? 'N/A' }}</span>
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <a href="{{ route('qr-patient-detail', $jadwalMcu->id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 px-3 py-1.5 rounded-lg">Buka Laporan</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="py-8 text-center text-slate-400">Tidak ada riwayat.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            {{-- Mobile Card --}}
                            <div class="md:hidden space-y-4">
                                @foreach($filteredRecords as $index => $riwayat)
                                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-4">
                                        <div class="flex justify-between items-start mb-3 border-b border-slate-100 pb-3">
                                            <div>
                                                <span class="text-[10px] font-black uppercase text-slate-400">Tanggal Periksa</span>
                                                <p class="font-bold text-slate-800 text-sm">{{ \Carbon\Carbon::parse($riwayat->tanggal_mcu)->format('d F Y') }}</p>
                                            </div>
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border">{{ $riwayat->status ?? 'N/A' }}</span>
                                        </div>
                                        <div class="mb-4">
                                            <span class="text-[10px] font-black uppercase text-slate-400">Dokter</span>
                                            <p class="font-medium text-slate-600 text-sm truncate">{{ $riwayat->dokter->nama_lengkap ?? 'N/A' }}</p>
                                        </div>
                                        <a href="{{ route('qr-patient-detail', $riwayat->id) }}" class="block w-full py-2.5 text-center text-xs font-bold text-white bg-slate-800 rounded-xl">Buka Hasil Lab</a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <style> .animate-fade-in { animation: fadeIn 0.3s ease-in-out; } @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } } </style>
</div>