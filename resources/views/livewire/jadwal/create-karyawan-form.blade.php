<form wire:submit.prevent="save">
    <div class="space-y-4"> {{-- 🎯 Jarak antar bagian dikurangi: space-y-4 --}}

        {{-- Bagian Pencarian Karyawan --}}
        <div class="p-4 bg-white rounded-xl shadow-lg border border-gray-100"> {{-- 🎯 Padding card dikurangi: p-4 --}}
            <h4 class="text-base font-bold text-gray-800 mb-3">Pilih Pasien</h4> {{-- 🎯 Ukuran judul dikurangi: text-base --}}
            <div class="mb-4">
                <label for="karyawan_search" class="block text-xs font-medium text-gray-700 mb-1">Cari Pasien (Nama/NIK/No. SAP)</label>
                <div class="relative">
                    <input 
                        type="text" 
                        id="karyawan_search" 
                        wire:model.live.debounce.300ms="search" 
                        class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 placeholder-gray-400" {{-- 🎯 Padding input dan font disesuaikan --}}
                        placeholder="Ketik nama, NIK, atau No. SAP..."
                    >
                    @error('karyawan_id') 
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p> {{-- 🎯 Ukuran error message dikurangi --}}
                    @enderror

                    @if (!empty($results))
                        <div class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-y-auto">
                            @foreach ($results as $item)
                                <div
                                    wire:click="selectPatient({{ $item['id'] }}, '{{ $item['search_type'] }}')"
                                    class="px-3 py-2 cursor-pointer hover:bg-gray-100 transition-colors duration-150" {{-- 🎯 Padding list item dikurangi --}}
                                >
                                    <p class="font-medium text-sm text-gray-900">{{ $item['search_name'] }}</p>
                                    <p class="text-xs text-gray-500">
                                        NIK: {{ $item['search_nik'] }} | No. SAP: {{ $item['no_sap'] ?? 'N/A' }}
                                        @if ($item['search_type'] == 'peserta_mcu')
                                            | <span class="text-xs font-semibold text-blue-500">Data MCU Sebelumnya</span>
                                        @endif
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Bagian Detail Pasien (hanya tampil jika pasien dipilih) --}}
        @if ($selectedKaryawan)
            <div class="p-4 bg-white rounded-xl shadow-lg border border-gray-100"> {{-- 🎯 Padding card dikurangi --}}
                <h4 class="text-base font-bold text-gray-800 mb-3">Detail Pasien</h4> {{-- 🎯 Ukuran judul dikurangi --}}
                {{-- 🎯 Grid 1 kolom di mobile, 2 kolom di tablet, 4 kolom di desktop --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 text-xs"> 
                    {{-- Detail Card Item --}}
                    @php
                        $details = [];
                        if ($patientType === 'karyawan') {
                            $details = [
                                'NO SAP' => $selectedKaryawan->no_sap,
                                'NAMA' => $selectedKaryawan->nama_karyawan,
                                'UNIT KERJA' => $selectedKaryawan->unitKerja->nama_unit_kerja ?? 'N/A',
                                'JABATAN' => $selectedKaryawan->jabatan ?? 'N/A',
                                'NO HP' => $selectedKaryawan->no_hp ?? 'N/A',
                            ];
                        } elseif ($patientType === 'peserta_mcu') {
                             $details = [
                                'NAMA' => $selectedKaryawan->nama_lengkap,
                                'NIK' => $selectedKaryawan->nik_pasien ?? 'N/A',
                                'PERUSAHAAN' => $selectedKaryawan->perusahaan_asal ?? 'N/A',
                                'TIPE' => $selectedKaryawan->tipe_anggota ?? 'N/A',
                                'NO HP' => $selectedKaryawan->no_hp ?? 'N/A',
                            ];
                        }
                    @endphp

                    @foreach ($details as $label => $value)
                        <div class="bg-gray-50 p-2 rounded-md border border-gray-200"> {{-- 🎯 Padding card detail dikurangi --}}
                            <p class="text-xs text-gray-500">{{ $label }}</p>
                            <p class="font-semibold text-gray-900">{{ $value ?? 'N/A' }}</p>
                        </div>
                    @endforeach

                    {{-- Link Lihat Detail (Opsional) --}}
                    @if ($patientType === 'karyawan')
                        <a href="{{ route('karyawan.show', $selectedKaryawan->id) }}" class="col-span-full text-center mt-2 text-xs text-red-600 hover:text-red-800 font-semibold">
                            Lihat Detail Profil Lengkap
                        </a>
                    @endif
                </div>
            </div>
        @endif

        {{-- Bagian Jadwal MCU --}}
        <div class="p-4 bg-white rounded-xl shadow-lg border border-gray-100"> {{-- 🎯 Padding card dikurangi --}}
            <h4 class="text-base font-bold text-gray-800 mb-3">Jadwal MCU</h4> {{-- 🎯 Ukuran judul dikurangi --}}
            {{-- 🎯 Grid 1 kolom di mobile --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4"> 
                <div class="mb-0"> {{-- 🎯 mb-6 diubah jadi mb-0/mb-2 --}}
                    <label for="tanggal_mcu" class="block text-xs font-medium text-gray-700 mb-1">Tanggal MCU</label>
                    <input 
                        type="date" 
                        name="tanggal_mcu" 
                        id="tanggal_mcu" 
                        wire:model="tanggal_mcu" 
                        class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" {{-- 🎯 Padding input dan font disesuaikan --}}
                        required
                    >
                    @error('tanggal_mcu') 
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p> 
                    @enderror
                </div>
                <div class="mb-0">
                    <label for="dokter_id" class="block text-xs font-medium text-gray-700 mb-1">Pilih Dokter</label>
                    <select 
                        id="dokter_id" 
                        wire:model="dokter_id" 
                        class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                        required
                    >
                        <option value="">-- Pilih Dokter --</option>
                        @foreach ($daftarDokter as $dokter)
                            <option value="{{ $dokter->id }}">{{ $dokter->nama_lengkap }} - {{ $dokter->spesialisasi }}</option>
                        @endforeach
                    </select>
                    @error('dokter_id') 
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p> 
                    @enderror
                </div>
                {{-- Pilih Paket MCU --}}
                <div class="mb-0">
                    <label for="paket_mcus_id" class="block text-xs font-medium text-gray-700 mb-1">Pilih Paket MCU</label>
                    <select 
                        id="paket_mcus_id" 
                        wire:model="paket_mcus_id" 
                        class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                        required
                    >
                        <option value="">-- Pilih Paket --</option>
                        @foreach ($daftarPaket as $paket)
                            <option value="{{ $paket->id }}">{{ $paket->nama_paket }}</option>
                        @endforeach
                    </select>
                    @error('paket_mcus_id') 
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p> 
                    @enderror
                </div>
            </div>
        </div>

        {{-- Tombol Simpan --}}
        <div class="flex justify-center mt-4"> {{-- 🎯 Tombol rata tengah di mobile --}}
            <button type="submit" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-md shadow-lg transition duration-150 ease-in-out text-base" wire:loading.attr="disabled">
                <span wire:loading.remove>Simpan Jadwal</span>
                <span wire:loading>Menyimpan...</span>
            </button>
        </div>
    </div>
</form>