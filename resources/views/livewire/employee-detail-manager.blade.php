@section('title', 'Detail Karyawan')

{{-- OUTER CONTAINER: Padding vertikal atas (pt-2) & horizontal minimal (px-0) untuk lebar maksimal --}}
<div class="pt-2 pb-4 px-0 md:pt-8 md:pb-8 md:px-4 lg:max-w-6xl lg:mx-auto"> 
    
    {{-- LAYOUT UTAMA: Tambahkan px-2 di sini untuk memberikan sedikit ruang di tepi layar --}}
    <div class="flex flex-col lg:flex-row gap-4 md:gap-6 px-2 sm:px-4"> 
        
        {{-- KOLOM KIRI (PROFIL DAN KELUARGA) --}}
        <div class="w-full lg:w-1/3">
            
            {{-- KARTU 1: DATA PROFIL KARYAWAN --}}
            <div class="p-3 bg-white rounded-xl shadow-xl border border-gray-100 mb-4">
                <div class="flex flex-col items-center text-center">
                    {{-- Foto Profil --}}
                    <div class="w-24 h-24 sm:w-28 sm:h-28 bg-gray-200 flex items-center justify-center rounded-full shadow-lg border-4 border-red-100 mb-3 overflow-hidden">
                        @if($activeUser->foto_profil)
                            <img src="{{ Storage::disk('s3')->url($activeUser->foto_profil) }}" 
                                alt="Profil" class="w-full h-full object-cover">
                        @else
                            {{-- Ikon Default jika foto tidak ada --}}
                            <svg class="w-12 h-12 sm:w-14 sm:h-14 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                    </div>
                    {{-- Nama Dinamis --}}
                    <h2 class="text-base sm:text-lg font-bold text-gray-800">{{ $activeUser->nama_lengkap ?? $activeUser->nama_karyawan }}</h2>
                    <p class="text-xs sm:text-sm text-gray-500">{{ $activeUser->unitKerja->nama_unit_kerja ?? 'N/A' }}</p>
                </div>
                <div class="mt-4 text-center border-t pt-4">
                    <p class="text-gray-500 font-semibold text-xs">No SAP: <span class="text-gray-900 font-normal">{{ $activeUser->no_sap ?? $activeUser->no_sap }}</span></p>
                    <p class="text-gray-500 font-semibold text-xs">NIK: <span class="text-gray-900 font-normal">{{ $activeUser->nik_pasien ?? $activeUser->nik_karyawan }}</span></p>
                </div>
            </div>

            {{-- KARTU 2: DATA KELUARGA / NAVIGASI PESERTA --}}
            <div class="p-3 bg-white rounded-xl shadow-xl border border-gray-100">
                <h3 class="text-base font-bold text-gray-800 mb-3 flex justify-between items-center border-b pb-2">
                    Data Keluarga
                    <a href="{{ route('karyawan.add.keluarga', ['karyawan_id' => $karyawan->id]) }}" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-2 rounded-full shadow-md transition duration-200 ease-in-out text-xs">
                        <i class="fas fa-plus h-3 w-3"></i>
                    </a>
                </h3>
                
                <div class="flex flex-wrap gap-2"> 
                    <button wire:click="selectKaryawan" @class(['inline-flex items-center justify-center font-bold py-1.5 px-2 rounded-lg shadow-md transition duration-200 ease-in-out text-xs flex-grow', 'bg-red-600 hover:bg-red-700 text-white' => $activeUser->id === $karyawan->id, 'bg-gray-200 hover:bg-gray-300 text-gray-700' => $activeUser->id !== $karyawan->id,])>
                        <i class="fas fa-user-tie h-3 w-3 mr-1"></i> Karyawan
                    </button>
                    
                    @if ($pesertaIstri)
                    <button wire:click="selectIstri" @class(['inline-flex items-center justify-center font-bold py-1.5 px-2 rounded-lg shadow-md transition duration-200 ease-in-out text-xs flex-grow', 'bg-red-600 hover:bg-red-700 text-white' => $activeUser->id === $pesertaIstri->id, 'bg-gray-200 hover:bg-gray-300 text-gray-700' => $activeUser->id !== $pesertaIstri->id,])>
                        <i class="fas fa-heart h-3 w-3 mr-1"></i> Istri
                    </button>
                    @endif

                    @if ($pesertaSuami)
                    <button wire:click="selectSuami" @class(['inline-flex items-center justify-center font-bold py-1.5 px-2 rounded-lg shadow-md transition duration-200 ease-in-out text-xs flex-grow', 'bg-red-600 hover:bg-red-700 text-white' => $activeUser->id === $pesertaSuami->id, 'bg-gray-200 hover:bg-gray-300 text-gray-700' => $activeUser->id !== $pesertaSuami->id,])>
                        <i class="fas fa-heart h-3 w-3 mr-1"></i> Suami
                    </button>
                    @endif

                    @if(isset($pesertaAnak) && $pesertaAnak->count() > 0)
                        @foreach ($pesertaAnak as $index => $anak)
                        <button wire:click="selectAnak({{ $anak->id }})" @class(['inline-flex items-center justify-center font-bold py-1.5 px-2 rounded-lg shadow-md transition duration-200 ease-in-out text-xs flex-grow', 'bg-red-600 hover:bg-red-700 text-white' => $activeUser->id === $anak->id, 'bg-gray-200 hover:bg-gray-300 text-gray-700' => $activeUser->id !== $anak->id,])>
                            <i class="fas fa-child h-3 w-3 mr-1"></i> Anak {{ $index + 1 }}
                        </button>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN (DATA & RIWAYAT MCU) --}}
        <div class="w-full lg:w-2/3"> 
            <div class="bg-white rounded-xl shadow-xl border border-gray-100">
                
                <div class="bg-gray-50 rounded-xl shadow-md border border-gray-200">
                    
                    {{-- Navigasi Tabs --}}
                    <div class="flex border-b border-gray-200">
                        <button wire:click="changeTab('data')" @class(['py-3 px-4 md:py-4 md:px-6 font-semibold text-sm transition-colors duration-200', 'bg-white border-b-2 border-red-500 text-red-600' => $activeTab === 'data', 'text-gray-600 hover:text-red-600' => $activeTab !== 'data',])>
                            Data
                        </button>
                        <button wire:click="changeTab('riwayat')" @class(['py-3 px-4 md:py-4 md:px-6 font-semibold text-sm transition-colors duration-200', 'bg-white border-b-2 border-red-500 text-red-600' => $activeTab === 'riwayat', 'text-gray-600 hover:text-red-600' => $activeTab !== 'riwayat',])>
                            Riwayat MCU
                        </button>
                    </div>

                    {{-- Konten Tab --}}
                    <div class="p-3 sm:p-4 md:p-6">
                        @if ($activeTab === 'data')
                            @if ($activeUser)
                                @include('livewire.partials.user-data', ['user' => $activeUser, 'karyawan' => $karyawan])
                            @else
                                <div class="text-center text-gray-500 text-sm">Data tidak ditemukan.</div>
                            @endif
                        @endif
                        
                        @if ($activeTab === 'riwayat')
                            @if ($activeUser)
                                
                                {{-- === FILTER TAHUN BARU === --}}
                                <div class="mb-4 flex flex-col md:flex-row md:items-center md:space-x-3">
                                    <label for="filter-year" class="block text-sm font-semibold text-gray-700 mb-1 md:mb-0">Filter Tahun:</label>
                                    
                                    {{-- Mengikat (binding) ke property Livewire 'selectedYear' --}}
                                    <select 
                                        wire:model.live="selectedYear" 
                                        id="filter-year" 
                                        class="mt-1 block w-full md:w-auto rounded-lg border-gray-300 shadow-sm text-sm p-2 focus:border-red-500 focus:ring-red-500"
                                    >
                                        {{-- Opsi Default --}}
                                        <option value="">Semua Tahun</option>
                                        
                                        @php
                                            // Menghasilkan opsi tahun secara dinamis (Anda harus menyediakan daftar tahun yang ada di Livewire PHP)
                                            $currentYear = date('Y');
                                            $startYear = 2020; 
                                        @endphp
                                        
                                        {{-- Menghasilkan opsi tahun --}}
                                        @for ($year = $currentYear; $year >= $startYear; $year--)
                                            {{-- Nilai yang di-filter harus berupa integer tahun --}}
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endfor
                                    </select>
                                </div>
                                {{-- ========================== --}}


                                {{-- BLOK DESKTOP: Tabel Tradisional (Muncul jika md ke atas) --}}
                                <div class="hidden md:block">
                                    {{-- ASUMSI: $activeUser->jadwalMcu di sini sudah DIFILTER oleh Livewire PHP --}}
                                    @include('livewire.partials.riwayat-mcu-table', ['user' => $activeUser, 'riwayatMcu' => $activeUser->jadwalMcu])
                                </div>
                                
                                {{-- BLOK MOBILE: Card View (Muncul jika di bawah md) --}}
                                <div class="md:hidden space-y-3">
                                    <h4 class="text-sm font-bold text-gray-700 border-b pb-2">Riwayat MCU</h4>
                                    
                                    @php
                                        // Variabel ini harusnya sudah terfilter oleh Livewire PHP berdasarkan $selectedYear
                                        $mobileRiwayat = $filteredRecords;
                                    @endphp
                                    
                                    @if($mobileRiwayat->count() > 0)
                                        @foreach($mobileRiwayat as $index => $riwayat)
                                            <div class="border border-gray-200 bg-white p-3 rounded-lg shadow-sm space-y-1 text-xs">
                                                <div class="flex justify-between border-b pb-1">
                                                    <span class="font-semibold text-gray-600">No:</span>
                                                    <span class="font-bold text-red-600">{{ $index + 1 }}</span>
                                                </div>
                                                <div class="flex justify-between border-b pb-1">
                                                    <span class="font-semibold text-gray-600">Tanggal:</span>
                                                    <span>{{ \Carbon\Carbon::parse($riwayat->tanggal_mcu)->format('d F Y') }}</span>
                                                </div>
                                                <div class="flex justify-between border-b pb-1">
                                                    <span class="font-semibold text-gray-600">Dokter:</span>
                                                    <span class="truncate max-w-[50%]">{{ $riwayat->dokter->nama_lengkap ?? 'N/A' }}</span>
                                                </div>
                                                <div class="flex justify-between items-center pt-1">
                                                    <span class="font-semibold text-gray-600">Status:</span>
                                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold 
                                                        @if($riwayat->status === 'Scheduled') bg-yellow-100 text-yellow-800
                                                        @else bg-green-100 text-green-800 @endif">
                                                        {{ $riwayat->status ?? 'N/A' }}
                                                    </span>
                                                </div>
                                                <div class="text-right pt-2 border-t mt-2">
                                                    <a href="{{ route('qr-patient-detail', $riwayat->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-xs">Lihat Detail &raquo;</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        {{-- Pesan tidak ada riwayat --}}
                                        <div class="text-center text-gray-500 text-sm p-4 bg-white rounded-lg">Tidak ada riwayat MCU.</div>
                                    @endif
                                </div>
                            @else
                                <div class="text-center text-gray-500 text-sm">Riwayat tidak ditemukan.</div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>