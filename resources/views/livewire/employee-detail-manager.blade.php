<div class="container mx-auto p-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <div class="w-full lg:w-1/3">
            <div class="p-6 bg-white rounded-xl shadow-2xl border border-gray-100">
                <div class="flex flex-col items-center text-center">
                    {{-- Foto Profil --}}
                    <div class="w-32 h-32 bg-gray-200 flex items-center justify-center rounded-full shadow-lg border-4 border-white mb-4">
                        <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                    </div>
                    {{-- Nama Dinamis --}}
                    <h2 class="text-xl font-bold text-gray-800">{{ $activeUser->nama_lengkap ?? $activeUser->nama_karyawan }}</h2>
                    {{-- Detail Dinamis --}}
                    <p class="text-sm text-gray-500">{{ $activeUser->unitKerja->nama_unit_kerja ?? 'N/A' }}</p>
                </div>
                <div class="mt-6 text-center">
                    <p class="text-gray-500 font-semibold">No SAP: <span class="text-gray-900 font-normal">{{ $activeUser->no_sap ?? $activeUser->no_sap }}</span></p>
                </div>
            </div>

            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex justify-between items-center">
                    Data Keluarga
                    <a href="{{ route('karyawan.add.keluarga', ['karyawan_id' => $karyawan->id]) }}" class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white font-bold py-1 px-3 rounded-full shadow-lg transition duration-200 ease-in-out text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Tombol Karyawan --}}
                    <button wire:click="selectKaryawan" @class([
                        'inline-flex items-center justify-center text-white font-bold py-2 px-4 rounded-full shadow-lg transition duration-200 ease-in-out text-sm text-center',
                        'bg-red-600 hover:bg-red-700' => $activeUser->id === $karyawan->id,
                        'bg-gray-500 hover:bg-gray-600' => $activeUser->id !== $karyawan->id,
                    ])>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                        Karyawan
                    </button>
                    {{-- Tombol Istri (jika ada) --}}
                    @if ($pesertaIstri)
                    <button wire:click="selectIstri" @class([
                        'inline-flex items-center justify-center text-white font-bold py-2 px-4 rounded-full shadow-lg transition duration-200 ease-in-out text-sm text-center',
                        'bg-red-600 hover:bg-red-700' => $activeUser->id === $pesertaIstri->id,
                        'bg-gray-500 hover:bg-gray-600' => $activeUser->id !== $pesertaIstri->id,
                    ])>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                        </svg>
                        Istri
                    </button>
                    @endif

                    {{-- Tombol Suami (jika ada) --}}
                    @if ($pesertaSuami)
                    <button wire:click="selectSuami" @class([
                        'inline-flex items-center justify-center text-white font-bold py-2 px-4 rounded-full shadow-lg transition duration-200 ease-in-out text-sm text-center',
                        'bg-red-600 hover:bg-red-700' => $activeUser->id === $pesertaSuami->id,
                        'bg-gray-500 hover:bg-gray-600' => $activeUser->id !== $pesertaSuami->id,
                    ])>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                        </svg>
                        Suami
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="w-full lg:w-6/3">
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
                            @if ($activeUser)
                                @include('livewire.partials.user-data', ['user' => $activeUser, 'karyawan' => $karyawan])
                            @else
                                <div class="text-center text-gray-500">Data tidak ditemukan.</div>
                            @endif
                        @endif
                        @if ($activeTab === 'riwayat')
                            @if ($activeUser)
                                @include('livewire.partials.riwayat-mcu-table', ['user' => $activeUser])
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