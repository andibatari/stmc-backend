@section('title', 'Profil & Riwayat MCU')

<div class="w-full max-w-7xl mx-auto"> 
    
    {{-- Header Back & Title --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 lg:mb-8 gap-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-black text-slate-800 tracking-tight">Profil Pasien</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Pusat data identitas dan histori medical check-up.</p>
        </div>
        <a href="{{ route('karyawan.index') }}" class="inline-flex items-center justify-center bg-white border border-slate-200 text-slate-600 font-bold py-2.5 px-5 rounded-xl hover:bg-slate-50 hover:-translate-x-1 transition-all text-sm shadow-sm">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="flex flex-col lg:flex-row gap-6"> 
        
        {{-- KOLOM KIRI (PROFIL & TANGGUNGAN) --}}
        <div class="w-full lg:w-[380px] shrink-0 space-y-6">
            
            {{-- KARTU 1: IDENTITAS PASIEN AKTIF --}}
            <div class="bg-white p-6 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-br from-red-600 to-red-900 opacity-90 rounded-t-[2rem]"></div>
                
                <div class="relative mt-6 mb-4">
                    <div class="w-28 h-28 mx-auto bg-white rounded-full p-1 shadow-xl ring-4 ring-white relative z-10">
                        @if($activeUser->foto_profil)
                            <img src="{{ Storage::disk('s3')->url($activeUser->foto_profil) }}" alt="Profil" class="w-full h-full object-cover rounded-full">
                        @else
                            <div class="w-full h-full bg-slate-100 flex items-center justify-center rounded-full text-slate-300">
                                <i class="fas fa-user text-4xl"></i>
                            </div>
                        @endif
                    </div>
                </div>

                <h2 class="text-xl font-black text-slate-800">{{ $activeUser->nama_lengkap ?? $activeUser->nama_karyawan }}</h2>
                <p class="text-xs font-bold text-red-600 mt-1 uppercase tracking-widest">{{ $activeUser->unitKerja->nama_unit_kerja ?? 'PASIEN UMUM / KELUARGA' }}</p>

                <div class="mt-6 flex flex-col gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-100 text-left">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400">ID / SAP</span>
                        <span class="text-sm font-mono font-black text-slate-700">{{ $activeUser->no_sap ?? $activeUser->no_sap ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400">NIK (KTP)</span>
                        <span class="text-sm font-mono font-black text-slate-700">{{ $activeUser->nik_pasien ?? $activeUser->nik_karyawan }}</span>
                    </div>
                </div>
            </div>

            {{-- KARTU 2: DAFTAR KELUARGA / TANGGUNGAN --}}
            <div class="bg-white p-6 rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100">
                <div class="flex justify-between items-center mb-5 border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest flex items-center">
                        <i class="fas fa-users text-slate-400 mr-2"></i> Data Keluarga
                    </h3>
                    <a href="{{ route('karyawan.add.keluarga', ['karyawan_id' => $karyawan->id]) }}" title="Tambah Anggota" class="w-8 h-8 bg-emerald-50 hover:bg-emerald-500 text-emerald-600 hover:text-white rounded-lg flex items-center justify-center transition-colors">
                        <i class="fas fa-plus text-xs"></i>
                    </a>
                </div>
                
                <div class="flex flex-col gap-3"> 
                    <button wire:click="selectKaryawan" class="w-full flex items-center justify-between p-3 rounded-xl border-2 transition-all font-bold text-sm @if($activeUser->id === $karyawan->id) border-red-500 bg-red-50 text-red-700 shadow-sm @else border-transparent bg-slate-50 hover:bg-slate-100 text-slate-600 @endif">
                        <div class="flex items-center"><i class="fas fa-user-tie w-6 text-left opacity-70"></i> Data Karyawan (Utama)</div>
                        @if($activeUser->id === $karyawan->id) <i class="fas fa-check-circle text-red-500"></i> @endif
                    </button>
                    
                    @if ($pesertaIstri)
                    <button wire:click="selectIstri" class="w-full flex items-center justify-between p-3 rounded-xl border-2 transition-all font-bold text-sm @if($activeUser->id === $pesertaIstri->id) border-red-500 bg-red-50 text-red-700 shadow-sm @else border-transparent bg-slate-50 hover:bg-slate-100 text-slate-600 @endif">
                        <div class="flex items-center"><i class="fas fa-female w-6 text-left opacity-70"></i> Istri</div>
                        @if($activeUser->id === $pesertaIstri->id) <i class="fas fa-check-circle text-red-500"></i> @endif
                    </button>
                    @endif

                    @if ($pesertaSuami)
                    <button wire:click="selectSuami" class="w-full flex items-center justify-between p-3 rounded-xl border-2 transition-all font-bold text-sm @if($activeUser->id === $pesertaSuami->id) border-red-500 bg-red-50 text-red-700 shadow-sm @else border-transparent bg-slate-50 hover:bg-slate-100 text-slate-600 @endif">
                        <div class="flex items-center"><i class="fas fa-male w-6 text-left opacity-70"></i> Suami</div>
                        @if($activeUser->id === $pesertaSuami->id) <i class="fas fa-check-circle text-red-500"></i> @endif
                    </button>
                    @endif

                    @if(isset($pesertaAnak) && $pesertaAnak->count() > 0)
                        @foreach ($pesertaAnak as $index => $anak)
                        <button wire:click="selectAnak({{ $anak->id }})" class="w-full flex items-center justify-between p-3 rounded-xl border-2 transition-all font-bold text-sm @if($activeUser->id === $anak->id) border-red-500 bg-red-50 text-red-700 shadow-sm @else border-transparent bg-slate-50 hover:bg-slate-100 text-slate-600 @endif">
                            <div class="flex items-center"><i class="fas fa-child w-6 text-left opacity-70"></i> Anak Ke-{{ $index + 1 }}</div>
                            @if($activeUser->id === $anak->id) <i class="fas fa-check-circle text-red-500"></i> @endif
                        </button>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN (DATA DIRI & RIWAYAT) --}}
        <div class="w-full flex-1"> 
            <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden min-h-[500px]">
                
                {{-- Modern Tab Navigation --}}
                <div class="flex p-2 m-4 bg-slate-100 rounded-2xl w-max">
                    <button wire:click="changeTab('data')" class="px-6 py-2.5 rounded-xl font-bold text-sm transition-all duration-200 @if($activeTab === 'data') bg-white text-slate-800 shadow-sm @else text-slate-500 hover:text-slate-700 @endif">
                        <i class="fas fa-id-card mr-2 opacity-70"></i> Data Lengkap
                    </button>
                    <button wire:click="changeTab('riwayat')" class="px-6 py-2.5 rounded-xl font-bold text-sm transition-all duration-200 @if($activeTab === 'riwayat') bg-white text-slate-800 shadow-sm @else text-slate-500 hover:text-slate-700 @endif">
                        <i class="fas fa-file-medical-alt mr-2 opacity-70"></i> Histori MCU
                    </button>
                </div>

                <div class="p-6 md:p-8 pt-2">
                    {{-- TAB 1: DATA LENGKAP --}}
                    @if ($activeTab === 'data')
                        <div class="animate-fade-in">
                        @if ($activeUser)
                            @include('livewire.partials.user-data', ['user' => $activeUser, 'karyawan' => $karyawan])
                        @else
                            <div class="py-12 text-center text-slate-400 font-medium bg-slate-50 rounded-2xl border border-slate-100">Data profil tidak ditemukan.</div>
                        @endif
                        </div>
                    @endif
                    
                    {{-- TAB 2: RIWAYAT MCU --}}
                    @if ($activeTab === 'riwayat')
                        <div class="animate-fade-in">
                        @if ($activeUser)
                            
                            {{-- Filter Tahun --}}
                            <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                                <h3 class="font-black text-lg text-slate-800">Daftar Kunjungan Medical Check-Up</h3>
                                <div class="flex items-center gap-3">
                                    <label class="text-xs font-bold text-slate-500 uppercase">Tahun:</label>
                                    <select wire:model.live="selectedYear" class="block rounded-xl border border-slate-200 bg-white shadow-sm text-sm font-bold p-2 focus:border-red-500 focus:ring-red-500 cursor-pointer">
                                        <option value="">Semua Riwayat</option>
                                        @for ($year = date('Y'); $year >= 2020; $year--)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            {{-- Tampilan Desktop (Tabel) --}}
                            <div class="hidden md:block">
                                @include('livewire.partials.riwayat-mcu-table', ['user' => $activeUser, 'riwayatMcu' => $activeUser->jadwalMcu])
                            </div>
                            
                            {{-- Tampilan Mobile (Card View) --}}
                            <div class="md:hidden space-y-4">
                                @if($filteredRecords->count() > 0)
                                    @foreach($filteredRecords as $index => $riwayat)
                                        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-4 hover:shadow-md transition-shadow">
                                            <div class="flex justify-between items-start mb-3 border-b border-slate-100 pb-3">
                                                <div>
                                                    <span class="text-[10px] font-black uppercase text-slate-400">Tanggal Periksa</span>
                                                    <p class="font-bold text-slate-800 text-sm mt-0.5">{{ \Carbon\Carbon::parse($riwayat->tanggal_mcu)->format('d F Y') }}</p>
                                                </div>
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border @if($riwayat->status === 'Scheduled') bg-amber-50 text-amber-600 border-amber-200 @elseif($riwayat->status === 'Finished') bg-emerald-50 text-emerald-600 border-emerald-200 @else bg-slate-50 text-slate-600 border-slate-200 @endif">
                                                    {{ $riwayat->status ?? 'N/A' }}
                                                </span>
                                            </div>
                                            <div class="mb-4">
                                                <span class="text-[10px] font-black uppercase text-slate-400">Dokter PIC</span>
                                                <p class="font-medium text-slate-600 text-sm mt-0.5 truncate"><i class="fas fa-user-md mr-1.5 opacity-50"></i>{{ $riwayat->dokter->nama_lengkap ?? 'Belum Ditentukan' }}</p>
                                            </div>
                                            <a href="{{ route('qr-patient-detail', $riwayat->id) }}" class="block w-full py-2.5 text-center text-xs font-bold text-white bg-slate-800 rounded-xl hover:bg-slate-700 transition-colors">Buka Hasil Lab / Detail</a>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="py-10 bg-slate-50 text-center rounded-2xl border border-slate-100">
                                        <i class="fas fa-folder-open text-3xl text-slate-300 mb-2"></i>
                                        <p class="text-sm font-bold text-slate-400">Tidak ada riwayat untuk tahun ini.</p>
                                    </div>
                                @endif
                            </div>

                        @else
                            <div class="py-12 text-center text-slate-400 font-medium bg-slate-50 rounded-2xl border border-slate-100">Pilih anggota keluarga terlebih dahulu.</div>
                        @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .animate-fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</div>