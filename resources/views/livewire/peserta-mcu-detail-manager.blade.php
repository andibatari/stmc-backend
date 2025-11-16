<div class="container mx-auto p-8">
    <div class="flex flex-col lg:flex-row gap-8">
        {{-- Kolom Kiri: Ringkasan & Navigasi --}}
        <div class="w-full lg:w-1/3">
            <div class="p-6 bg-white rounded-xl shadow-2xl border border-gray-100">
                <div class="flex flex-col items-center text-center">
                    {{-- Foto Profil --}}
                    <div class="w-32 h-32 bg-gray-200 flex items-center justify-center rounded-full shadow-lg border-4 border-white mb-4">
                        <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                    </div>
                    {{-- Nama Pasien --}}
                    <h2 class="text-xl font-bold text-gray-800">{{ $pesertaMcu->nama_lengkap }}</h2>
                    {{-- Detail Pasien --}}
                    <p class="text-sm text-gray-500">{{ $pesertaMcu->perusahaan_asal ?? 'N/A' }}</p>
                </div>
                <div class="mt-6 text-center">
                    {{-- <p class="text-gray-500 font-semibold">Hubungan: <span class="text-gray-900 font-normal">{{ $pesertaMcu->tipe_anggota ?? 'N/A' }}</span></p> --}}
                    <p class="text-gray-500 font-semibold">No SAP: <span class="text-gray-900 font-normal">{{ $pesertaMcu->no_sap ?? 'N/A' }}</span></p>
                </div>
            </div>

            <div class="mt-8">
                @if ($pesertaMcu->karyawan_id)
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex justify-between items-center">
                        Data Karyawan Utama
                    </h3>
                    <div class="p-6 bg-white rounded-xl shadow-lg border border-gray-100">
                        <p class="text-gray-500 font-semibold">Nama: <span class="text-gray-900 font-normal">{{ $pesertaMcu->karyawan->nama_karyawan ?? 'N/A' }}</span></p>
                        <p class="text-gray-500 font-semibold">Unit Kerja: <span class="text-gray-900 font-normal">{{ $pesertaMcu->karyawan->unitKerja->nama_unit_kerja ?? 'N/A' }}</span></p>
                        <p class="text-gray-500 font-semibold">No SAP: <span class="text-gray-900 font-normal">{{ $pesertaMcu->karyawan->no_sap ?? 'N/A' }}</span></p>
                        <a href="{{ route('karyawan.show', ['karyawan' => $pesertaMcu->karyawan_id]) }}" class="mt-4 inline-block text-sm text-blue-600 hover:text-blue-800 transition-colors duration-200">Lihat Detail Karyawan</a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Kolom Kanan: Detail & Riwayat --}}
        <div class="w-full lg:w-2/3">
            <div class="p-6 bg-white rounded-xl shadow-2xl border border-gray-100">
                <div class="bg-gray-50 rounded-xl shadow-md border border-gray-200">
                    <div class="flex border-b border-gray-200">
                        <button wire:click="changeTab('data')" @class([
                            'py-4 px-6 font-semibold text-sm transition-colors duration-200',
                            'bg-white border-b-2 border-red-500 text-red-600' => $activeTab === 'data',
                            'text-gray-600 hover:text-red-600' => $activeTab !== 'data',
                        ])>
                            Data
                        </button>
                        <button wire:click="changeTab('riwayat')" @class([
                            'py-4 px-6 font-semibold text-sm transition-colors duration-200',
                            'bg-white border-b-2 border-red-500 text-red-600' => $activeTab === 'riwayat',
                            'text-gray-600 hover:text-red-600' => $activeTab !== 'riwayat',
                        ])>
                            Riwayat MCU
                        </button>
                    </div>

                    <div class="p-6">
                        @if ($activeTab === 'data')
                            @if ($pesertaMcu)
                                {{-- Menggunakan nama file yang lebih spesifik untuk detail pasien --}}
                                @include('livewire.partials.keluarga-data-view', ['pesertaMcu' => $pesertaMcu])
                            @else
                                <div class="text-center text-gray-500">Data tidak ditemukan.</div>
                            @endif
                        @endif
                        @if ($activeTab === 'riwayat')
                            @if ($pesertaMcu)
                                @include('livewire.partials.riwayat-mcu-table', ['user' => $pesertaMcu])
                            @else
                                <div class="text-center text-gray-500">Riwayat tidak ditemukan.</div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>