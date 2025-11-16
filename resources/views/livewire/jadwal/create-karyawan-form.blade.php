<form wire:submit.prevent="save">
    <div class="space-y-6">

        {{-- Bagian Pencarian Karyawan --}}
        <div class="p-6 bg-white rounded-xl shadow-lg border border-gray-100">
            <h4 class="text-xl font-bold text-gray-800 mb-4">Pilih Karyawan</h4>
            <div class="mb-4">
                <label for="karyawan_search" class="block text-sm font-medium text-gray-700 mb-1">Cari Karyawan (Nama/NIK/No. SAP)</label>
                <div class="relative">
                    <input 
                        type="text" 
                        id="karyawan_search" 
                        wire:model.live.debounce.300ms="search" 
                        class="block w-full px-4 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 placeholder-gray-400"
                        placeholder="Ketik nama, NIK, atau No. SAP karyawan..."
                    >
                    @error('karyawan_id') 
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p> 
                    @enderror

                    @if (!empty($results))
                        <div class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-y-auto">
                            @foreach ($results as $item)
                                <div
                                    wire:click="selectPatient({{ $item['id'] }}, '{{ $item['search_type'] }}')"
                                    class="px-4 py-2 cursor-pointer hover:bg-gray-100 transition-colors duration-150"
                                >
                                    <p class="font-medium text-gray-900">{{ $item['search_name'] }}</p>
                                    <p class="text-sm text-gray-500">
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

        {{-- Bagian Detail Pasien (diperluas) --}}
        @if ($selectedKaryawan)
            <div class="p-6 bg-white rounded-xl shadow-lg border border-gray-100">
                <h4 class="text-xl font-bold text-gray-800 mb-4">
                    Detail {{ $patientType === 'keluarga' ? 'Keluarga dari ' . ($selectedKaryawan->nama_karyawan ?? 'Karyawan') : ($patientType === 'peserta_mcu' ? 'Peserta MCU' : 'Karyawan') }}
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-4 text-sm">
                    {{-- Detail Karyawan --}}
                    @if ($patientType === 'karyawan')
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">NO SAP</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->no_sap ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">NIK KARYAWAN</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->nik_karyawan ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">NAMA KARYAWAN</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->nama_karyawan ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">PEKERJAAN</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->pekerjaan ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">PENDIDIKAN</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->pendidikan ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">KEBANGSAAN</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->kebangsaan ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">TEMPAT LAHIR</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->tempat_lahir ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">TANGGAL LAHIR</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->tanggal_lahir ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">UMUR</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->umur ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">JENIS KELAMIN</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->jenis_kelamin ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">GOLONGAN DARAH</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->golongan_darah ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">AGAMA</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->agama ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">JABATAN</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->jabatan ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">ALAMAT</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->alamat ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">NOMOR HP</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->no_hp ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">EMAIL</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->email ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">DEPARTEMEN</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->departemen->nama_departemen ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">UNIT KERJA</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->unitKerja->nama_unit_kerja ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">PROVINSI</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->provinsi->nama_provinsi ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">KABUPATEN</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->kabupaten->nama_kabupaten ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">KECAMATAN</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->kecamatan->nama_kecamatan ?? 'N/A' }}</p>
                        </div>
                        {{-- Tambahkan tinggi dan berat badan --}}
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">TINGGI BADAN (cm)</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->tinggi_badan ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">BERAT BADAN (kg)</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->berat_badan ?? 'N/A' }}</p>
                        </div>
                    @elseif ($patientType === 'peserta_mcu')
                    {{-- Detail Peserta MCU --}}
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">NO SAP</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->no_sap ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">TIPE ANGGOTA</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->tipe_anggota ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">NIK</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->nik_pasien ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">NAMA LENGKAP</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->nama_lengkap ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">JENIS KELAMIN</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->jenis_kelamin ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">TEMPAT LAHIR</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->tempat_lahir ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">TANGGAL LAHIR</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->tanggal_lahir ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">UMUR</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->umur ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">GOLONGAN DARAH</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->golongan_darah ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">PEKERJAAN</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->pekerjaan ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">PERUSAHAAN ASAL</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->perusahaan_asal ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">AGAMA</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->agama ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">ALAMAT</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->alamat ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">NOMOR HP</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->no_hp ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">EMAIL</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->email ?? 'N/A' }}</p>
                        </div>
                        {{-- Tambahkan tinggi dan berat badan --}}
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">TINGGI BADAN (cm)</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->tinggi_badan ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-md shadow-sm border border-gray-200">
                            <p class="text-xs text-gray-500">BERAT BADAN (kg)</p>
                            <p class="font-semibold text-gray-900">{{ $selectedKaryawan->berat_badan ?? 'N/A' }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Bagian Tanggal Pendaftaran & Dokter --}}
        <div class="p-6 bg-white rounded-xl shadow-lg border border-gray-100">
            <h4 class="text-xl font-bold text-gray-800 mb-4">Jadwal MCU</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="mb-6">
                    <label for="tanggal_mcu" class="block text-sm font-medium text-gray-700 mb-1">Tanggal MCU</label>
                    <input 
                        type="date" 
                        name="tanggal_mcu" 
                        id="tanggal_mcu" 
                        wire:model="tanggal_mcu" 
                        class="block w-full px-4 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" 
                        required
                    >
                    @error('tanggal_mcu') 
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p> 
                    @enderror
                </div>
                <div class="mb-6">
                    <label for="dokter_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Dokter</label>
                    <select 
                        id="dokter_id" 
                        wire:model="dokter_id" 
                        class="block w-full px-4 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                        required
                    >
                        <option value="">-- Pilih Dokter --</option>
                        @foreach ($daftarDokter as $dokter)
                            <option value="{{ $dokter->id }}">{{ $dokter->nama_lengkap }} - {{ $dokter->spesialisasi }}</option>
                        @endforeach
                    </select>
                    @error('dokter_id') 
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p> 
                    @enderror
                </div>
                {{-- Tambahan: Pilih Paket MCU --}}
                <div class="mb-6">
                    <label for="paket_mcus_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Paket MCU</label>
                    <select 
                        id="paket_mcus_id" 
                        wire:model="paket_mcus_id" 
                        class="block w-full px-4 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                        required
                    >
                        <option value="">-- Pilih Paket --</option>
                        @foreach ($daftarPaket as $paket)
                            <option value="{{ $paket->id }}">{{ $paket->nama_paket }}</option>
                        @endforeach
                    </select>
                    @error('paket_mcus_id') 
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p> 
                    @enderror
                </div>
            </div>
        </div>

        <div class="flex justify-end mt-8">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-md shadow-lg transition duration-150 ease-in-out">
                <span wire:loading.remove>Simpan Jadwal</span>
                <span wire:loading>Menyimpan...</span>
            </button>
        </div>
    </div>
</form>

{{-- Hapus semua kode SweetAlert2. --}}