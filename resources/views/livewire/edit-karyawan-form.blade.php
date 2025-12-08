@section('title', 'Edit Data Karyawan')

<div class="py-2 px-2 sm:px-4 md:py-8 md:px-6 lg:max-w-6xl lg:mx-auto">
    
    <div class="mb-4">
        <a href="javascript:history.back()" class="inline-flex items-center text-sm font-semibold text-gray-600 hover:text-red-600 transition duration-150">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-2xl p-4 sm:p-6 md:p-8 border border-gray-100">
        <h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-6 border-b pb-3">
            Edit Data Karyawan 📝
        </h1>

        <form wire:submit.prevent="updateKaryawan" class="space-y-6">
            
            {{-- Bagian 1: Informasi Dasar Karyawan --}}
            <h2 class="text-lg font-semibold text-red-600 border-b pb-1 mb-3">
                Informasi Dasar
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                
                {{-- No SAP --}}
                <div class="space-y-1">
                    <label for="no_sap" class="block text-xs font-medium text-gray-700">No SAP</label>
                    <input type="text" wire:model.live="no_sap" id="no_sap" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" required>
                    @error('no_sap') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                {{-- NIK Karyawan --}}
                <div class="space-y-1">
                    <label for="nik_karyawan" class="block text-xs font-medium text-gray-700">NIK Karyawan</label>
                    <input type="text" wire:model.live="nik_karyawan" id="nik_karyawan" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" required>
                    @error('nik_karyawan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Nama Karyawan --}}
                <div class="space-y-1 md:col-span-2">
                    <label for="nama_karyawan" class="block text-xs font-medium text-gray-700">Nama Karyawan</label>
                    <input type="text" wire:model.live="nama_karyawan" id="nama_karyawan" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" required>
                    @error('nama_karyawan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Pekerjaan --}}
                <div class="space-y-1">
                    <label for="pekerjaan" class="block text-xs font-medium text-gray-700">Pekerjaan</label>
                    <input type="text" wire:model.live="pekerjaan" id="pekerjaan" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                    @error('pekerjaan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Hubungan --}}
                <div class="space-y-1">
                    <label for="hubungan" class="block text-xs font-medium text-gray-700">Hubungan</label>
                    <input type="text" wire:model.live="hubungan" id="hubungan" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                    @error('hubungan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <hr class="my-4">

            {{-- Bagian 2: Data Pribadi & Fisik --}}
            <h2 class="text-lg font-semibold text-red-600 border-b pb-1 mb-3">
                Data Pribadi & Fisik
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                
                {{-- Tempat Lahir --}}
                <div class="space-y-1">
                    <label for="tempat_lahir" class="block text-xs font-medium text-gray-700">Tempat Lahir</label>
                    <input type="text" wire:model.live="tempat_lahir" id="tempat_lahir" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                    @error('tempat_lahir') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Tanggal Lahir --}}
                <div class="space-y-1">
                    <label for="tanggal_lahir" class="block text-xs font-medium text-gray-700">Tanggal Lahir</label>
                    <input type="date" wire:model.live="tanggal_lahir" id="tanggal_lahir" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                    @error('tanggal_lahir') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Umur (Readonly) --}}
                <div class="space-y-1">
                    <label for="umur" class="block text-xs font-medium text-gray-700">Umur</label>
                    <input type="text" wire:model="umur" id="umur" class="block w-full px-3 py-2 text-sm rounded-lg border-gray-300 bg-gray-100 shadow-sm" readonly>
                    @error('umur') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Jenis Kelamin (Select) --}}
                <div class="space-y-1">
                    <label for="jenis_kelamin" class="block text-xs font-medium text-gray-700">Jenis Kelamin</label>
                    <select wire:model.live="jenis_kelamin" id="jenis_kelamin" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                        <option value="">Pilih</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                    @error('jenis_kelamin') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                {{-- Tinggi Badan --}}
                <div class="space-y-1">
                    <label for="tinggi_badan" class="block text-xs font-medium text-gray-700">Tinggi Badan (cm)</label>
                    <input type="number" wire:model.live="tinggi_badan" id="tinggi_badan" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                    @error('tinggi_badan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Berat Badan --}}
                <div class="space-y-1">
                    <label for="berat_badan" class="block text-xs font-medium text-gray-700">Berat Badan (kg)</label>
                    <input type="number" wire:model.live="berat_badan" id="berat_badan" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                    @error('berat_badan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                {{-- Golongan Darah --}}
                <div class="space-y-1">
                    <label for="golongan_darah" class="block text-xs font-medium text-gray-700">Golongan Darah</label>
                    <select wire:model.live="golongan_darah" id="golongan_darah" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                        <option value="">Pilih Golongan Darah</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="AB">AB</option>
                        <option value="O">O</option>
                    </select>
                    @error('golongan_darah') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                {{-- Agama --}}
                <div class="space-y-1">
                    <label for="agama" class="block text-xs font-medium text-gray-700">Agama</label>
                    <select wire:model.live="agama" id="agama" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                        <option value="">Pilih Agama</option>
                        <option value="Islam">Islam</option>
                        <option value="Kristen">Kristen</option>
                        <option value="Katolik">Katolik</option>
                        <option value="Hindu">Hindu</option>
                        <option value="Buddha">Buddha</option>
                        <option value="Konghucu">Konghucu</option>
                    </select>
                    @error('agama') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Status Pernikahan --}}
                <div class="space-y-1">
                    <label for="status_pernikahan" class="block text-xs font-medium text-gray-700">Status Pernikahan</label>
                    <select wire:model.live="status_pernikahan" id="status_pernikahan" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                        <option value="">Pilih Status</option>
                        <option value="Menikah">Menikah</option>
                        <option value="Belum Menikah">Belum Menikah</option>
                        <option value="Cerai Hidup">Cerai Hidup</option>
                        <option value="Cerai Mati">Cerai Mati</option>
                    </select>
                    @error('status_pernikahan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                {{-- Kebangsaan --}}
                <div class="space-y-1">
                    <label for="kebangsaan" class="block text-xs font-medium text-gray-700">Kebangsaan</label>
                    <select wire:model.live="kebangsaan" id="kebangsaan" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                        <option value="">Pilih Kebangsaan</option>
                        <option value="WNI">WNI</option>
                        <option value="WNA">WNA</option>
                    </select>
                    @error('kebangsaan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
            </div>
            
            <hr class="my-4">

            {{-- Bagian 3: Organisasi & Lokasi --}}
            <h2 class="text-lg font-semibold text-red-600 border-b pb-1 mb-3">
                Organisasi & Lokasi
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                
                {{-- Departemen (Select) --}}
                <div class="space-y-1">
                    <label for="departemens_id" class="block text-xs font-medium text-gray-700">Departemen</label>
                    <select wire:model.live="departemens_id" id="departemens_id" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                        <option value="">Pilih Departemen</option>
                        @foreach($departemens as $departemen)
                            <option value="{{ $departemen->id }}">{{ $departemen->nama_departemen }}</option>
                        @endforeach
                    </select>
                    @error('departemens_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Unit Kerja (Select) --}}
                <div class="space-y-1">
                    <label for="unit_kerjas_id" class="block text-xs font-medium text-gray-700">Unit Kerja</label>
                    <select wire:model.live="unit_kerjas_id" id="unit_kerjas_id" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                        <option value="">Pilih Unit Kerja</option>
                        @foreach($unitKerjas as $unitKerja)
                            <option value="{{ $unitKerja->id }}">{{ $unitKerja->nama_unit_kerja }}</option>
                        @endforeach
                    </select>
                    @error('unit_kerjas_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                {{-- Jabatan (Select) --}}
                <div class="space-y-1">
                    <label for="jabatan" class="block text-xs font-medium text-gray-700">Jabatan</label>
                    <select wire:model.live="jabatan" id="jabatan" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                        <option value="">Pilih Jabatan</option>
                        @foreach(['Senior Manager', 'Manager', 'Associate', 'Supervisor', 'Staff'] as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>
                    @error('jabatan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                {{-- Eselon (Select) --}}
                <div class="space-y-1">
                    <label for="eselon" class="block text-xs font-medium text-gray-700">Eselon</label>
                    <select wire:model.live="eselon" id="eselon" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                        <option value="">Pilih Eselon</option>
                        <option value="I">I</option>
                        <option value="II">II</option>
                        <option value="III">III</option>
                        <option value="IV">IV</option>
                        <option value="V">V</option>
                    </select>
                    @error('eselon') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Pendidikan (Select) --}}
                <div class="space-y-1">
                    <label for="pendidikan" class="block text-xs font-medium text-gray-700">Pendidikan</label>
                    <select wire:model.live="pendidikan" id="pendidikan" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                        <option value="">Pilih Pendidikan</option>
                        <option value="SD">SD</option>
                        <option value="SMP">SMP</option>
                        <option value="SMA/SMK">SMA/SMK</option>
                        <option value="D1">D1</option>
                        <option value="D2">D2</option>
                        <option value="D3">D3</option>
                        <option value="D4">D4</option>
                        <option value="S1">S1</option>
                        <option value="S2">S2</option>
                        <option value="S3">S3</option>
                    </select>
                    @error('pendidikan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Provinsi (Select) --}}
                <div class="space-y-1">
                    <label for="provinsi_id" class="block text-xs font-medium text-gray-700">Provinsi</label>
                    <select wire:model.live="provinsi_id" id="provinsi_id" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                        <option value="">Pilih Provinsi</option>
                        @foreach($provinsis as $provinsi)
                            <option value="{{ $provinsi->id }}">{{ $provinsi->nama_provinsi }}</option>
                        @endforeach
                    </select>
                    @error('provinsi_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                {{-- Kabupaten (Select) --}}
                <div class="space-y-1">
                    <label for="kabupaten_id" class="block text-xs font-medium text-gray-700">Kabupaten</label>
                    <select wire:model.live="kabupaten_id" id="kabupaten_id" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                        <option value="">Pilih Kabupaten</option>
                        @foreach($kabupatens as $kabupaten)
                            <option value="{{ $kabupaten->id }}">{{ $kabupaten->nama_kabupaten }}</option>
                        @endforeach
                    </select>
                    @error('kabupaten_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                {{-- Kecamatan (Select) --}}
                <div class="space-y-1">
                    <label for="kecamatan_id" class="block text-xs font-medium text-gray-700">Kecamatan</label>
                    <select wire:model.live="kecamatan_id" id="kecamatan_id" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                        <option value="">Pilih Kecamatan</option>
                        @foreach($kecamatans as $kecamatan)
                            <option value="{{ $kecamatan->id }}">{{ $kecamatan->nama_kecamatan }}</option>
                        @endforeach
                    </select>
                    @error('kecamatan_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
            </div>

            <hr class="my-4">

            {{-- Bagian 4: Data Pasangan, Kontak & Security --}}
            <h2 class="text-lg font-semibold text-red-600 border-b pb-1 mb-3">
                Data Kontak & Login
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">

                {{-- Nama Suami/Istri --}}
                <div class="space-y-1">
                    <label for="suami_istri" class="block text-xs font-medium text-gray-700">Nama Suami/Istri</label>
                    <input type="text" wire:model.live="suami_istri" id="suami_istri" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                    @error('suami_istri') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                {{-- Pekerjaan Suami/Istri --}}
                <div class="space-y-1">
                    <label for="pekerjaan_suami_istri" class="block text-xs font-medium text-gray-700">Pekerjaan Suami/Istri</label>
                    <input type="text" wire:model.live="pekerjaan_suami_istri" id="pekerjaan_suami_istri" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                    @error('pekerjaan_suami_istri') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                {{-- Alamat --}}
                <div class="space-y-1 md:col-span-2">
                    <label for="alamat" class="block text-xs font-medium text-gray-700">Alamat Lengkap</label>
                    <input type="text" wire:model.live="alamat" id="alamat" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                    @error('alamat') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Nomor HP --}}
                <div class="space-y-1">
                    <label for="no_hp" class="block text-xs font-medium text-gray-700">Nomor HP</label>
                    <input type="text" wire:model.live="no_hp" id="no_hp" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                    @error('no_hp') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                {{-- Email --}}
                <div class="space-y-1">
                    <label for="email" class="block text-xs font-medium text-gray-700">Email</label>
                    <input type="email" wire:model.live="email" id="email" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                    @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                {{-- Password --}}
                <div class="space-y-1">
                    <label for="password" class="block text-xs font-medium text-gray-700">Password</label>
                    <input type="password" wire:model.live="password" id="password" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="Kosongkan jika tidak ingin diubah">
                    @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end mt-8 border-t pt-4">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-md shadow-lg transition duration-150 ease-in-out">
                    <i class="fas fa-save mr-2"></i> Update Karyawan
                </button>
            </div>
        </form>
    </div>
</div>