<form wire:submit.prevent="save">
    <div class="mb-4">
        <label for="karyawan_search" class="block text-sm font-medium text-gray-700 mb-1">Cari Karyawan (Nama/NIK/No. SAP)</label>
        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
            <input 
                type="text" 
                id="karyawan_search" 
                wire:model.live.debounce.300ms="search" 
                class="block w-full px-4 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 placeholder-gray-400"
                placeholder="Ketik nama, NIK, atau No. SAP karyawan..."
                x-ref="search"
                @focus="open = true"
            >
            @error('karyawan_id') 
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p> 
            @enderror

            @if ($results->isNotEmpty())
                <div x-show="open" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-y-auto">
                    @foreach ($results as $karyawan)
                        <div 
                            wire:click="selectKaryawan({{ $karyawan->id }}, '{{ $karyawan->nama }}')"
                            class="px-4 py-2 cursor-pointer hover:bg-gray-100 transition-colors duration-150"
                        >
                            <p class="font-medium text-gray-900">{{ $karyawan->nama_karyawan }}</p>
                            <p class="text-sm text-gray-500">NIK: {{ $karyawan->nik_karyawan }} | No. SAP: {{ $karyawan->no_sap }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @if ($selectedKaryawan)
    <div class="bg-gray-100 rounded-md p-4 mb-6 border border-gray-200">
        <h4 class="text-lg font-bold text-gray-800 mb-4">Detail Karyawan:</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-x-6 gap-y-4 text-sm">
            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">NO SAP</p>
                <p class="font-semibold text-gray-900">{{ $selectedKaryawan->no_sap}}</p>
            </div>

            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">NAMA KARYAWAN</p>
                <p class="font-semibold text-gray-900">{{ $selectedKaryawan->nama_karyawan}}</p>
            </div>

            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">NIK KARYAWAN</p>
                <p class="font-semibold text-gray-900">{{ $selectedKaryawan->nik_karyawan}}</p>
            </div>

            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">PEKERJAAN</p>
                <p class="font-semibold text-gray-900">{{ $selectedKaryawan->pekerjaan}}</p>
            </div>

            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">TEMPAT LAHIR</p>
                <p class="font-semibold text-gray-900">{{ $selectedKaryawan->tempat_lahir}}</p>
            </div>

            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">TANGGAL LAHIR</p>
                <p class="font-semibold text-gray-900">{{ $selectedKaryawan->tanggal_lahir}}</p>
            </div>

            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">UMUR</p>
                <p class="font-semibold text-gray-900">{{ $selectedKaryawan->umur }}</p>
            </div>

            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">JENIS KELAMIN</p>
                <p class="font-semibold text-gray-900">{{ $selectedKaryawan->jenis_kelamin }}</p>
            </div>

            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">GOLONGAN DARAH</p>
                <p class="font-semibold text-gray-900">{{ $selectedKaryawan->golongan_darah }}</p>
            </div>

            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">AGAMA</p>
                <p class="font-semibold text-gray-900">{{ $selectedKaryawan->agama }}</p>
            </div>

            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">STATUS PERNIKAHAN</p>
                <p class="font-semibold text-gray-900">{{ $selectedKaryawan->status_pernikahan }}</p>
            </div>

            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">HUBUNGAN</p>
                <p class="font-semibold text-gray-900">{{ $selectedKaryawan->hubungan }}</p>
            </div>

            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">KEBANGSAAN</p>
                <p class="font-semibold text-gray-900">{{ $selectedKaryawan->kebangsaan }}</p>
            </div>

            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">JABATAN</p>
                <p class="font-semibold text-gray-900">{{ $selectedKaryawan->jabatan }}</p>
            </div>

            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">ESELON</p>
                <p class="font-semibold text-gray-900">{{ $selectedKaryawan->eselon }}</p>
            </div>

            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">PENDIDIKAN</p>
                <p class="font-semibold text-gray-900">{{ $selectedKaryawan->pendidikan }}</p>
            </div>

            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">DEPARTEMEN</p>
                <p class="font-semibold text-gray-900">
                    {{ $selectedKaryawan->departemen->nama_departemen ?? 'N/A' }}
                </p>
            </div>

            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">UNIT KERJA</p>
                <p class="font-semibold text-gray-900">
                    {{ $selectedKaryawan->unitKerja->nama_unit_kerja ?? 'N/A' }}
                </p>
            </div>

            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">PROVINSI</p>
                <p class="font-semibold text-gray-900">
                    {{ $selectedKaryawan->provinsi->nama_provinsi ?? 'N/A' }}
                </p>
            </div>

            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">KABUPATEN</p>
                <p class="font-semibold text-gray-900">
                    {{ $selectedKaryawan->kabupaten->nama_kabupaten ?? 'N/A' }}
                </p>
            </div>

            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">KECAMATAN</p>
                <p class="font-semibold text-gray-900">
                    {{ $selectedKaryawan->kecamatan->nama_kecamatan ?? 'N/A' }}
                </p>
            </div>

            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">Nama Suami/Istri</p>
                <p class="font-semibold text-gray-900">{{ $selectedKaryawan->suami_istri }}</p>
            </div>

            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">PEKERJAAN SUAMI/ISTRI</p>
                <p class="font-semibold text-gray-900">{{ $selectedKaryawan->pekerjaan_suami_istri }}</p>
            </div>

            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">ALAMAT</p>
                <p class="font-semibold text-gray-900">{{ $selectedKaryawan->alamat }}</p>
            </div>

            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">NOMOR HP</p>
                <p class="font-semibold text-gray-900">{{ $selectedKaryawan->no_hp }}</p>
            </div>

            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                <p class="text-xs text-gray-500">EMAIL</p>
                <p class="font-semibold text-gray-900">{{ $selectedKaryawan->email }}</p>
            </div>

            
        </div>
    </div>
    @endif

    <div class="mb-6">
        <label for="tanggal_mcu" class="block text-sm font-medium text-gray-700 mb-1">Tanggal MCU</label>
        <input 
            type="date" 
            name="tanggal_mcu" 
            id="tanggal_mcu" 
            wire:model="tanggal_mcu" 
            class="form-input block w-full px-4 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" 
            required
        >
        @error('tanggal_mcu') 
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p> 
        @enderror
    </div>
    
    <input type="hidden" wire:model="karyawan_id">

    <div class="flex justify-end mt-8">
        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-md shadow-lg transition duration-150 ease-in-out">
            <span wire:loading.remove>Simpan Jadwal</span>
            <span wire:loading>Menyimpan...</span>
        </button>
    </div>
</form>