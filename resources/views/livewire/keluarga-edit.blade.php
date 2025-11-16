<div>
    <form wire:submit.prevent="updateKeluarga">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            {{-- Form hanya untuk pasangan --}}
            <div>
                <label for="nik_pasien" class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                <input type="text" wire:model.live="nik_pasien" id="nik_pasien" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                @error('nik_pasien') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" wire:model.live="nama_lengkap" id="nama_lengkap" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" required>
                @error('nama_lengkap') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label for="pekerjaan" class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                <input type="text" wire:model.live="pekerjaan" id="pekerjaan" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                @error('pekerjaan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                <select wire:model.live="jenis_kelamin" id="jenis_kelamin" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    <option value="">Pilih</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
                @error('jenis_kelamin') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div>
                <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                <input type="text" wire:model.live="tempat_lahir" id="tempat_lahir" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                @error('tempat_lahir') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                <input type="date" wire:model.live="tanggal_lahir" id="tanggal_lahir" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                @error('tanggal_lahir') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label for="umur" class="block text-sm font-medium text-gray-700 mb-1">Umur</label>
                <input type="text" wire:model="umur" id="umur" class="block w-full px-4 py-2 rounded-md border-gray-300 bg-gray-100 shadow-sm" readonly>
                @error('umur') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="golongan_darah" class="block text-sm font-medium text-gray-700 mb-1">Golongan Darah</label>
                <select wire:model.live="golongan_darah" id="golongan_darah" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    <option value="">Pilih Golongan Darah</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="AB">AB</option>
                    <option value="O">O</option>
                </select>
                @error('golongan_darah') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            
            <div>
                <label for="agama" class="block text-sm font-medium text-gray-700 mb-1">Agama</label>
                <select wire:model.live="agama" id="agama" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    <option value="">Pilih Agama</option>
                    <option value="Islam">Islam</option>
                    <option value="Kristen">Kristen</option>
                    <option value="Katolik">Katolik</option>
                    <option value="Hindu">Hindu</option>
                    <option value="Buddha">Buddha</option>
                    <option value="Konghucu">Konghucu</option>
                </select>
                @error('agama') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="pendidikan" class="block text-sm font-medium text-gray-700 mb-1">Pendidikan</label>
                <select wire:model.live="pendidikan" id="pendidikan" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
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
                @error('pendidikan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="departemens_id" class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
                <select wire:model.live="departemens_id" id="departemens_id" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                    <option value="">Pilih Departemen</option>
                    @foreach($departemens as $departemen)
                        <option value="{{ $departemen->id }}">{{ $departemen->nama_departemen }}</option>
                    @endforeach
                </select>
                @error('departemens_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="unit_kerjas_id" class="block text-sm font-medium text-gray-700 mb-1">Unit Kerja</label>
                <select wire:model.live="unit_kerjas_id" id="unit_kerjas_id" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                    <option value="">Pilih Unit Kerja</option>
                    @foreach($unitKerjas as $unitKerja)
                        <option value="{{ $unitKerja->id }}">{{ $unitKerja->nama_unit_kerja }}</option>
                    @endforeach
                </select>
                @error('unit_kerjas_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div>
                <label for="provinsi_id" class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                <select wire:model.live="provinsi_id" id="provinsi_id" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    <option value="">Pilih Provinsi</option>
                    @foreach($provinsis as $provinsi)
                        <option value="{{ $provinsi->id }}">{{ $provinsi->nama_provinsi }}</option>
                    @endforeach
                </select>
                @error('provinsi_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="kabupaten_id" class="block text-sm font-medium text-gray-700 mb-1">Kabupaten</label>
                <select wire:model.live="kabupaten_id" id="kabupaten_id" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" >
                    <option value="">Pilih Kabupaten</option>
                    @foreach($kabupatens as $kabupaten)
                        <option value="{{ $kabupaten->id }}">{{ $kabupaten->nama_kabupaten }}</option>
                    @endforeach
                </select>
                @error('kabupaten_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="kecamatan_id" class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                <select wire:model.live="kecamatan_id" id="kecamatan_id" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    <option value="">Pilih Kecamatan</option>
                    @foreach($kecamatans as $kecamatan)
                        <option value="{{ $kecamatan->id }}">{{ $kecamatan->nama_kecamatan }}</option>
                    @endforeach
                </select>
                @error('kecamatan_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <input type="text" wire:model.live="alamat" id="alamat" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                @error('alamat') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-1">Nomor HP</label>
                <input type="text" wire:model.live="no_hp" id="no_hp" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                @error('no_hp') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" wire:model.live="email" id="email" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="perusahaan_asal" class="block text-sm font-medium text-gray-700 mb-1">Perusahaan</label>
                <input wire:model="perusahaan_asal" type="text" name="perusahaan_asal" id="perusahaan_asal" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 transition-colors duration-200" placeholder="Contoh: PT ABC">
                @error('perusahaan_asal') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="flex items-center justify-end mt-8">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-md shadow-lg transition duration-150 ease-in-out">
                Update Data
            </button>
        </div>
    </form>
</div>