<div class="py-4 px-2 sm:px-4 md:py-8 md:px-6 lg:max-w-6xl lg:mx-auto"> {{-- Outer Container --}}
    <div class="flex flex-col lg:flex-row gap-4 md:gap-6">
        
        {{-- Kolom Kiri: Ringkasan & Navigasi --}}
        <div class="w-full lg:w-1/3">
            {{-- KARTU PROFIL --}}
            <div class="p-4 bg-white rounded-xl shadow-xl border border-gray-100 mb-4">
                <div class="flex flex-col items-center text-center">
                    {{-- Foto Profil --}}
                    <div class="w-24 h-24 bg-gray-200 flex items-center justify-center rounded-full shadow-lg border-4 border-white mb-3">
                        <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                    </div>
                    {{-- Nama Pasien --}}
                    <h2 class="text-lg font-bold text-gray-800">{{ $pesertaMcu->nama_lengkap }}</h2>
                    {{-- Detail Pasien --}}
                    <p class="text-sm text-gray-500">{{ $pesertaMcu->perusahaan_asal ?? 'N/A' }}</p>
                    <p class="text-xs font-semibold text-red-600">({{ $pesertaMcu->tipe_anggota ?? 'Pasien Umum' }})</p>
                </div>
                <div class="mt-4 text-center border-t pt-4">
                    <p class="text-gray-500 font-semibold text-xs">NIK: <span class="text-gray-900 font-normal">{{ $pesertaMcu->nik_pasien ?? 'N/A' }}</span></p>
                    <p class="text-gray-500 font-semibold text-xs">No SAP: <span class="text-gray-900 font-normal">{{ $pesertaMcu->no_sap ?? 'N/A' }}</span></p>
                </div>
            </div>

            {{-- KARTU KARYAWAN UTAMA (Hanya muncul jika ini anggota keluarga) --}}
            <div class="mt-4">
                @if ($pesertaMcu->karyawan_id)
                    <h3 class="text-base font-semibold text-gray-800 mb-3 border-b pb-2">Data Karyawan Utama</h3>
                    <div class="p-4 bg-white rounded-xl shadow-lg border border-gray-100 text-sm space-y-1">
                        <p class="text-gray-600 font-medium">Nama: <span class="text-gray-900 font-normal">{{ $pesertaMcu->karyawan->nama_karyawan ?? 'N/A' }}</span></p>
                        <p class="text-gray-600 font-medium">Unit Kerja: <span class="text-gray-900 font-normal">{{ $pesertaMcu->karyawan->unitKerja->nama_unit_kerja ?? 'N/A' }}</span></p>
                        <p class="text-gray-600 font-medium">No SAP: <span class="text-gray-900 font-normal">{{ $pesertaMcu->karyawan->no_sap ?? 'N/A' }}</span></p>
                        <a href="{{ route('karyawan.show', ['karyawan' => $pesertaMcu->karyawan_id]) }}" class="mt-3 inline-block text-xs text-red-600 hover:text-red-800 transition-colors duration-200 font-semibold">Lihat Detail Karyawan &raquo;</a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Kolom Kanan: Detail & Riwayat --}}
        <div class="w-full lg:w-2/3">
            <div class="p-4 bg-white rounded-xl shadow-2xl border border-gray-100">
                <div class="bg-gray-50 rounded-xl shadow-md border border-gray-200">
                    
                    {{-- Navigasi Tabs --}}
                    <div class="flex border-b border-gray-200">
                        <button wire:click="changeTab('data')" @class(['py-3 px-4 font-semibold text-sm transition-colors duration-200', 'bg-white border-b-2 border-red-500 text-red-600' => $activeTab === 'data', 'text-gray-600 hover:text-red-600' => $activeTab !== 'data',])>
                            Data
                        </button>
                        <button wire:click="changeTab('riwayat')" @class(['py-3 px-4 font-semibold text-sm transition-colors duration-200', 'bg-white border-b-2 border-red-500 text-red-600' => $activeTab === 'riwayat', 'text-gray-600 hover:text-red-600' => $activeTab !== 'riwayat',])>
                            Riwayat MCU
                        </button>
                    </div>

                    <div class="p-4 sm:p-6">
                        @if ($activeTab === 'data')
                            @if ($pesertaMcu)
                                {{-- ASUMSI: FILE INI BERISI CARD VIEW DETAIL DATA PASIEN --}}
                                @include('livewire.partials.keluarga-data-view', ['pesertaMcu' => $pesertaMcu])
                            @else
                                <div class="text-center text-gray-500 text-sm">Data tidak ditemukan.</div>
                            @endif
                        @endif
                        
                        @if ($activeTab === 'riwayat')
                            @if ($pesertaMcu)
                                {{-- === FILTER TAHUN === --}}
                                <div class="mb-4 flex flex-col md:flex-row md:items-center md:space-x-3">
                                    <label for="filter-year" class="block text-sm font-semibold text-gray-700 mb-1 md:mb-0">Filter Tahun:</label>

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
                                {{-- ====================== --}}
                                
                                <div class="space-y-4">
                                    <h4 class="text-base font-bold text-gray-800 border-b pb-2">Daftar Pemeriksaan</h4>

                                    {{-- BLOK DESKTOP: Tabel Tradisional --}}
                                    <div class="hidden md:block overflow-x-auto">
                                        {{-- RIWAYAT MCU - TABEL --}}
                                        <table class="min-w-full text-sm bg-white border border-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">No</th>
                                                    <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">Tanggal MCU</th>
                                                    <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">Dokter</th>
                                                    <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">Status</th>
                                                    <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200">
                                                @if ($filteredRecords->count() > 0)
                                                    @foreach ($filteredRecords as $index => $jadwalMcu)
                                                    <tr>
                                                        <td class="py-3 px-4 text-sm font-medium text-gray-900">{{ $index + 1 }}</td>
                                                        <td class="py-3 px-4 text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($jadwalMcu->tanggal_mcu)->format('Y-m-d') }}</td>
                                                        <td class="py-3 px-4 text-sm font-medium text-gray-900">{{ $jadwalMcu->dokter->nama_lengkap ?? 'N/A' }}</td>
                                                        <td class="py-3 px-4 text-sm font-medium text-gray-900">
                                                            <span class="px-2 py-0.5 rounded-full text-xs font-bold 
                                                                @if($jadwalMcu->status === 'Scheduled') bg-yellow-100 text-yellow-800
                                                                @else bg-green-100 text-green-800 @endif">
                                                                {{ $jadwalMcu->status ?? 'N/A' }}
                                                            </span>
                                                        </td>
                                                        <td class="py-3 px-4 text-sm font-medium text-gray-900 text-center">
                                                            <a href="{{ route('qr-patient-detail', $jadwalMcu->id) }}" class="text-red-600 hover:text-red-800">Lihat</a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="5" class="py-4 text-center text-gray-500">
                                                            Tidak ada riwayat MCU.
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    {{-- BLOK MOBILE: Card View --}}
                                    <div class="md:hidden space-y-3">
                                        @if($filteredRecords->count() > 0)
                                            @foreach($filteredRecords as $index => $riwayat)
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
                                            <div class="text-center text-gray-500 text-sm p-4 bg-white rounded-lg">Tidak ada riwayat MCU.</div>
                                        @endif
                                    </div>
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