<div class="py-2 px-2 sm:px-4 md:py-2 md:px-2 lg:max-w-7xl lg:mx-auto">
    
    {{-- Tombol Kembali --}}
    <div class="mb-4">
        <a href="javascript:history.back()" class="inline-flex items-center text-sm font-semibold text-gray-600 hover:text-red-600 transition duration-150">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-2xl p-4 sm:p-6 md:p-8 border border-gray-100">
        <h1 class="text-xl md:text-2xl font-bold text-gray-900 mb-6 border-b pb-3">
            Edit Data Pasien
        </h1>

        <form wire:submit.prevent="updateKeluarga" class="space-y-6">
            
            {{-- Bagian 1: Identitas Dasar --}}
            <h2 class="text-lg font-semibold text-red-600 border-b pb-1 mb-3">
                Identitas Dasar 🆔
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                
                {{-- Nama Lengkap --}}
                <div class="space-y-1 md:col-span-2">
                    <label for="nama_lengkap" class="block text-xs font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" wire:model.live="nama_lengkap" id="nama_lengkap" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" required>
                    @error('nama_lengkap') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                {{-- NIK --}}
                <div class="space-y-1">
                    <label for="nik_pasien" class="block text-xs font-medium text-gray-700">NIK/Nomor Identitas</label>
                    <input type="text" wire:model.live="nik_pasien" id="nik_pasien" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('nik_pasien') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- NIP/SAP --}}
                <div class="space-y-1">
                    <label for="no_sap" class="block text-xs font-medium text-gray-700">NIP/SAP</label>
                    <input type="text" wire:model.live="no_sap" id="no_sap" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('no_sap') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Perusahaan Asal --}}
                <div class="space-y-1">
                    <label for="perusahaan_asal" class="block text-xs font-medium text-gray-700">Perusahaan Asal</label>
                    <input type="text" wire:model.live="perusahaan_asal" id="perusahaan_asal" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('perusahaan_asal') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                {{-- Pekerjaan --}}
                <div class="space-y-1">
                    <label for="pekerjaan" class="block text-xs font-medium text-gray-700">Pekerjaan</label>
                    <input type="text" wire:model.live="pekerjaan" id="pekerjaan" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('pekerjaan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
            </div>
            
            <hr class="my-4">

            {{-- Bagian 2: Kelahiran & Fisik --}}
            <h2 class="text-lg font-semibold text-red-600 border-b pb-1 mb-3">
                Kelahiran & Fisik 🧬
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                
                {{-- Tanggal Lahir --}}
                <div class="space-y-1">
                    <label for="tanggal_lahir" class="block text-xs font-medium text-gray-700">Tanggal Lahir</label>
                    <input type="date" wire:model.live="tanggal_lahir" id="tanggal_lahir" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('tanggal_lahir') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                {{-- Umur (Readonly) --}}
                <div class="space-y-1">
                    <label for="umur" class="block text-xs font-medium text-gray-700">Umur</label>
                    <input type="text" wire:model="umur" id="umur" class="block w-full px-3 py-2 text-sm rounded-lg border-gray-300 bg-gray-100 shadow-sm" readonly>
                    @error('umur') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Tempat Lahir --}}
                <div class="space-y-1">
                    <label for="tempat_lahir" class="block text-xs font-medium text-gray-700">Tempat Lahir</label>
                    <input type="text" wire:model.live="tempat_lahir" id="tempat_lahir" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('tempat_lahir') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                {{-- Jenis Kelamin --}}
                <div class="space-y-1">
                    <label for="jenis_kelamin" class="block text-xs font-medium text-gray-700">Jenis Kelamin</label>
                    <select wire:model.live="jenis_kelamin" id="jenis_kelamin" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        <option value="">Pilih</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                    @error('jenis_kelamin') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                {{-- Golongan Darah --}}
                <div class="space-y-1">
                    <label for="golongan_darah" class="block text-xs font-medium text-gray-700">Golongan Darah</label>
                    <select wire:model.live="golongan_darah" id="golongan_darah" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        <option value="">Pilih</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="AB">AB</option>
                        <option value="O">O</option>
                    </select>
                    @error('golongan_darah') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Pendidikan --}}
                <div class="space-y-1">
                    <label for="pendidikan" class="block text-xs font-medium text-gray-700">Pendidikan</label>
                    <select wire:model.live="pendidikan" id="pendidikan" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        <option value="">Pilih</option>
                        <option value="SD">SD</option>
                        <option value="SMP">SMP</option>
                        <option value="SMA/SMK">SMA/SMK</option>
                        <option value="D3">D3</option>
                        <option value="S1">S1</option>
                        <option value="S2">S2</option>
                        <option value="S3">S3</option>
                    </select>
                    @error('pendidikan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                {{-- Tinggi Badan --}}
                <div class="space-y-1">
                    <label for="tinggi_badan" class="block text-xs font-medium text-gray-700">Tinggi Badan (cm)</label>
                    <input type="number" wire:model.live="tinggi_badan" id="tinggi_badan" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('tinggi_badan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Berat Badan --}}
                <div class="space-y-1">
                    <label for="berat_badan" class="block text-xs font-medium text-gray-700">Berat Badan (kg)</label>
                    <input type="number" wire:model.live="berat_badan" id="berat_badan" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('berat_badan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                {{-- Agama --}}
                <div class="space-y-1">
                    <label for="agama" class="block text-xs font-medium text-gray-700">Agama</label>
                    <select wire:model.live="agama" id="agama" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        <option value="">Pilih</option>
                        <option value="Islam">Islam</option>
                        <option value="Kristen">Kristen</option>
                        <option value="Katolik">Katolik</option>
                        <option value="Hindu">Hindu</option>
                        <option value="Buddha">Buddha</option>
                        <option value="Konghucu">Konghucu</option>
                    </select>
                    @error('agama') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <hr class="my-4">

            {{-- Bagian 3: Organisasi (Hanya jika bukan Non-Karyawan) --}}
            {{-- FIX LINE 162: Mengubah variabel yang salah $PesertaMcu menjadi $keluarga --}}
            @if ($keluarga->tipe_anggota != 'Non-Karyawan')
                <h2 class="text-lg font-semibold text-red-600 border-b pb-1 mb-3">
                    Struktur Organisasi 🏢
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    
                    {{-- Departemen --}}
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
                    
                    {{-- Unit Kerja --}}
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
                </div>
                <hr class="my-4">
            @endif


            {{-- Bagian 4: Alamat & Kontak --}}
            <h2 class="text-lg font-semibold text-red-600 border-b pb-1 mb-3">
                Alamat & Kontak 📞
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                {{-- Kolom Kiri: Provinsi --}}
                <div class="space-y-1">
                    <label for="provinsi_id" class="block text-xs font-medium text-gray-700">Provinsi</label>
                    <select wire:model.live="provinsi_id" id="provinsi_id" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        <option value="">Pilih Provinsi</option>
                        @foreach($provinsis as $provinsi)
                            <option value="{{ $provinsi->id }}">{{ $provinsi->nama_provinsi }}</option>
                        @endforeach
                    </select>
                    @error('provinsi_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Kolom Kanan: Kabupaten/Kota --}}
                <div class="space-y-1">
                    <label for="nama_kabupaten" class="block text-xs font-medium text-gray-700">Kabupaten/Kota</label>
                    <input type="text" wire:model.live="nama_kabupaten" id="nama_kabupaten" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('nama_kabupaten') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Kolom Kiri: Kecamatan --}}
                <div class="space-y-1">
                    <label for="nama_kecamatan" class="block text-xs font-medium text-gray-700">Kecamatan</label>
                    <input type="text" wire:model.live="nama_kecamatan" id="nama_kecamatan" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('nama_kecamatan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Kolom Kanan: Nomor HP --}}
                <div class="space-y-1">
                    <label for="no_hp" class="block text-xs font-medium text-gray-700">Nomor HP</label>
                    <input type="text" wire:model.live="no_hp" id="no_hp" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('no_hp') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Kolom Kiri: Email --}}
                <div class="space-y-1">
                    <label for="email" class="block text-xs font-medium text-gray-700">Email</label>
                    <input type="email" wire:model.live="email" id="email" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Kolom Kosong (Spacer) agar simetris di laptop, 
                    Atau biarkan kosong agar Textarea di bawahnya menarik perhatian --}}
                <div class="hidden md:block"></div>

                {{-- Alamat Lengkap (Textarea - Memanjang 2 kolom di Laptop) --}}
                <div class="space-y-1 md:col-span-2">
                    <label for="alamat" class="block text-xs font-medium text-gray-700">Alamat Lengkap</label>
                    <textarea wire:model="alamat" id="alamat" rows="3" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2 transition-colors duration-200"></textarea>
                    @error('alamat') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

            </div>

            <hr class="my-4">
            
            {{-- Bagian 5: Login/Akun --}}
            <h2 class="text-lg font-semibold text-red-600 border-b pb-1 mb-3">
                Update Akun Login
            </h2>
            <p class="text-xs text-gray-500 mb-3">Kosongkan kolom password jika tidak ingin diubah.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label for="password" class="block text-xs font-medium text-gray-700">Password Baru</label>
                    <input type="password" wire:model.live="password" id="password" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="Kosongkan jika tidak diubah">
                    @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-medium text-gray-700">Konfirmasi Password Baru</label>
                    <input type="password" wire:model.live="password_confirmation" id="password_confirmation" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('password_confirmation') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end mt-8 border-t pt-4">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-md shadow-lg transition duration-150 ease-in-out">
                    <i class="fas fa-save mr-2"></i> Update Data
                </button>
            </div>
        </form>
    </div>
</div>