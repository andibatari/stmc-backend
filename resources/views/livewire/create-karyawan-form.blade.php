<div>
    <form wire:submit.prevent="saveKaryawan">
        
        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">📋 Informasi Dasar</h3>
        
        {{-- Bagian 1: Informasi Dasar Karyawan --}}
        {{-- 🎯 grid-cols-1 secara default, md:grid-cols-2 untuk tablet/desktop kecil --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div>
                <label for="no_sap" class="block text-xs font-medium text-gray-700 mb-1">No SAP</label>
                <input type="text" wire:model.live="no_sap" id="no_sap" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" required>
                @error('no_sap') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label for="nik_karyawan" class="block text-xs font-medium text-gray-700 mb-1">NIK Karyawan</label>
                <input type="text" wire:model.live="nik_karyawan" id="nik_karyawan" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" required>
                @error('nik_karyawan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="nama_karyawan" class="block text-xs font-medium text-gray-700 mb-1">Nama Karyawan</label>
                <input type="text" wire:model.live="nama_karyawan" id="nama_karyawan" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" required>
                @error('nama_karyawan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="pekerjaan" class="block text-xs font-medium text-gray-700 mb-1">Pekerjaan</label>
                <input type="text" wire:model.live="pekerjaan" id="pekerjaan" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                @error('pekerjaan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
        
        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 mt-6">🎂 Data Pribadi & Demografi</h3>

        {{-- Bagian 2: Data Pribadi --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div>
                <label for="tempat_lahir" class="block text-xs font-medium text-gray-700 mb-1">Tempat Lahir</label>
                <input type="text" wire:model.live="tempat_lahir" id="tempat_lahir" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                @error('tempat_lahir') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="tanggal_lahir" class="block text-xs font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                <input type="date" wire:model.live="tanggal_lahir" id="tanggal_lahir" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                @error('tanggal_lahir') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="umur" class="block text-xs font-medium text-gray-700 mb-1">Umur </label>
                {{-- 🎯 Umur read-only dengan font dan padding disesuaikan --}}
                <input type="text" wire:model="umur" id="umur" class="block w-full px-3 py-2 text-sm rounded-md border-gray-300 bg-gray-100 shadow-sm" readonly>
                @error('umur') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="jenis_kelamin" class="block text-xs font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                <select wire:model.live="jenis_kelamin" id="jenis_kelamin" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                    <option value="">Pilih</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
                @error('jenis_kelamin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Bagian 3: Detail Fisik & Sosial --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div>
                <label for="golongan_darah" class="block text-xs font-medium text-gray-700 mb-1">Golongan Darah</label>
                <select wire:model.live="golongan_darah" id="golongan_darah" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                    <option value="">Pilih Golongan Darah</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="AB">AB</option>
                    <option value="O">O</option>
                </select>
                @error('golongan_darah') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="agama" class="block text-xs font-medium text-gray-700 mb-1">Agama</label>
                <select wire:model.live="agama" id="agama" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                    <option value="">Pilih Agama</option>
                    <option value="Islam">Islam</option>
                    <option value="Kristen">Kristen</option>
                    <option value="Katolik">Katolik</option>
                    <option value="Hindu">Hindu</option>
                    <option value="Buddha">Buddha</option>
                    <option value="Konghucu">Konghucu</option>
                </select>
                @error('agama') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="status_pernikahan" class="block text-xs font-medium text-gray-700 mb-1">Status Pernikahan</label>
                <select wire:model.live="status_pernikahan" id="status_pernikahan" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                    <option value="">Pilih Status</option>
                    <option value="Menikah">Menikah</option>
                    <option value="Belum Menikah">Belum Menikah</option>
                    <option value="Cerai Hidup">Cerai Hidup</option>
                    <option value="Cerai Mati">Cerai Mati</option>
                </select>
                @error('status_pernikahan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="hubungan" class="block text-xs font-medium text-gray-700 mb-1">Hubungan</label>
                <input type="text" wire:model.live="hubungan" id="hubungan" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                @error('hubungan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 mt-6">🏢 Organisasi & Pendidikan</h3>

        {{-- Bagian 4: Informasi kebangsaan & Pekerjaan --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div>
                <label for="kebangsaan" class="block text-xs font-medium text-gray-700 mb-1">Kebangsaan</label>
                <select wire:model.live="kebangsaan" id="kebangsaan" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                    <option value="">Pilih Kebangsaan</option>
                    <option value="WNI">WNI</option>
                    <option value="WNA">WNA</option>
                </select>
                @error('kebangsaan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="jabatan" class="block text-xs font-medium text-gray-700 mb-1">Jabatan</label>
                <select wire:model.live="jabatan" id="jabatan" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                    <option value="">Pilih Jabatan</option>
                    <option value="Senior Manager">Senior Manager</option>
                    <option value="Manager">Manager</option>
                    <option value="Associate">Associate</option>
                    <option value="Supervisor">Supervisor</option>
                    <option value="Staff">Staff</option>
                </select>
                @error('jabatan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="eselon" class="block text-xs font-medium text-gray-700 mb-1">Eselon</label>
                <select wire:model.live="eselon" id="eselon" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                    <option value="">Pilih Eselon</option>
                    <option value="I">I</option>
                    <option value="II">II</option>
                    <option value="III">III</option>
                    <option value="IV">IV</option>
                    <option value="V">V</option>
                </select>
                @error('eselon') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="pendidikan" class="block text-xs font-medium text-gray-700 mb-1">Pendidikan</label>
                <select wire:model.live="pendidikan" id="pendidikan" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
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
                @error('pendidikan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Bagian 5: Dropdown Organisasi & Lokasi --}}
        {{-- 🎯 Diubah ke grid-cols-1 di mobile --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
            <div class="col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-2">Departemen & Unit Kerja</label>
                {{-- Livewire component di sini akan mengambil ruang penuh --}}
                @livewire('searchable-departemen', [
                    'initialDepartemenId' => $departemens_id,
                    'initialUnitKerjaId' => $unit_kerjas_id
                ])
                @error('departemens_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                @error('unit_kerjas_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-2">Alamat Domisili (Provinsi, Kab/Kota, Kec.)</label>

                <div class="grid grid-cols-3 gap-3">
                    {{-- Input Provinsi (Dropdown Biasa) --}}
                    <div>
                        <label for="provinsi_id" class="block text-xs font-medium text-gray-700 mb-1">Provinsi</label>
                        <select wire:model.live="provinsi_id" id="provinsi_id" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinsis as $provinsi)
                                <option value="{{ $provinsi->id }}">{{ $provinsi->nama_provinsi }}</option>
                            @endforeach
                        </select>
                        @error('provinsi_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    {{-- Input Kabupaten (Text Biasa) - PERUBAHAN wire:model --}}
                    <div>
                        <label for="nama_kabupaten" class="block text-xs font-medium text-gray-700 mb-1">Kabupaten/Kota</label>
                        <input type="text" wire:model.live="nama_kabupaten" id="nama_kabupaten" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="Contoh: Jakarta Pusat">
                        @error('nama_kabupaten') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Input Kecamatan (Text Biasa) - PERUBAHAN wire:model --}}
                    <div>
                        <label for="nama_kecamatan" class="block text-xs font-medium text-gray-700 mb-1">Kecamatan</label>
                        <input type="text" wire:model.live="nama_kecamatan" id="nama_kecamatan" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="Contoh: Tanah Abang">
                        @error('nama_kecamatan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>
        
        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 mt-6">📞 Kontak & Keluarga</h3>

        {{-- Bagian 6: Informasi Kontak & Akun --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div>
                <label for="email" class="block text-xs font-medium text-gray-700 mb-1">Email</label>
                <input type="email" wire:model.live="email" id="email" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="password" class="block text-xs font-medium text-gray-700 mb-1">Password</label>
                <input type="password" wire:model.live="password" id="password" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="no_hp" class="block text-xs font-medium text-gray-700 mb-1">Nomor HP</label>
                <input type="text" wire:model.live="no_hp" id="no_hp" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                @error('no_hp') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
             <div>
                <label for="alamat" class="block text-xs font-medium text-gray-700 mb-1">Alamat (Detail)</label>
                <input type="text" wire:model.live="alamat" id="alamat" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                @error('alamat') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
           
            <div>
                <label for="suami_istri" class="block text-xs font-medium text-gray-700 mb-1">Nama Suami/Istri</label>
                <input type="text" wire:model.live="suami_istri" id="suami_istri" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                @error('suami_istri') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="pekerjaan_suami_istri" class="block text-xs font-medium text-gray-700 mb-1">Pekerjaan Suami/Istri</label>
                <input type="text" wire:model.live="pekerjaan_suami_istri" id="pekerjaan_suami_istri" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                @error('pekerjaan_suami_istri') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            {{-- Bagian Tinggi/Berat dipindah ke sini agar kontak tergabung --}}
            <div>
                <label for="tinggi_badan" class="block text-xs font-medium text-gray-700 mb-1">Tinggi Badan (cm)</label>
                <input type="number" wire:model.live="tinggi_badan" id="tinggi_badan" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="Contoh: 175.5">
                @error('tinggi_badan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="berat_badan" class="block text-xs font-medium text-gray-700 mb-1">Berat Badan (kg)</label>
                <input type="number" wire:model.live="berat_badan" id="berat_badan" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="Contoh: 70.2">
                @error('berat_badan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Tombol Simpan --}}
        <div class="flex items-center justify-end mt-8">
            <button type="submit" class="w-full md:w-auto bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-md shadow-lg transition duration-150 ease-in-out" wire:loading.attr="disabled">
                <span wire:loading.remove>Simpan Data Karyawan</span>
                <span wire:loading>Menyimpan...</span>
            </button>
        </div>
    </form>
</div>