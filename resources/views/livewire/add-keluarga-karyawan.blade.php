<form wire:submit.prevent="save">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        {{-- BARIS 1 & 2: INPUT DASAR & PRIBADI --}}
        <div class="col-span-1">
            <label for="tipe_anggota" class="block text-sm font-semibold text-gray-700 mb-1">Tipe Anggota</label>
            <select wire:model.live="tipe_anggota" id="tipe_anggota" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 transition-colors duration-200">
                <option value="">Pilih...</option>
                @if ($karyawan)
                <option value="Istri">Istri</option>
                <option value="Suami">Suami</option>
                @else
                <option value="Non-Karyawan">Non-Karyawan</option>
                @endif
            </select>
            @error('tipe_anggota') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="col-span-1">
            <label for="no_sap" class="block text-sm font-semibold text-gray-700 mb-1">NIP/SAP (dari perusahaan)</label>
            <input wire:model="no_sap" type="text" name="no_sap" id="no_sap" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 transition-colors duration-200">
            @error('no_sap') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="col-span-1">
            <label for="nik_pasien" class="block text-sm font-semibold text-gray-700 mb-1">NIK/Nomor Identitas</label>
            <input wire:model="nik_pasien" type="text" name="nik_pasien" id="nik_pasien" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 transition-colors duration-200" required>
            @error('nik_pasien') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="col-span-1">
            <label for="nama_lengkap" class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
            <input wire:model="nama_lengkap" type="text" name="nama_lengkap" id="nama_lengkap" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 transition-colors duration-200" required>
            @error('nama_lengkap') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="col-span-1">
            <label for="jenis_kelamin" class="block text-sm font-semibold text-gray-700 mb-1">Jenis Kelamin</label>
            <select wire:model="jenis_kelamin" id="jenis_kelamin" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 transition-colors duration-200">
                <option value="">Pilih...</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>
            @error('jenis_kelamin') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="col-span-1">
            <label for="tempat_lahir" class="block text-sm font-semibold text-gray-700 mb-1">Tempat Lahir</label>
            <input wire:model="tempat_lahir" type="text" name="tempat_lahir" id="tempat_lahir" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 transition-colors duration-200">
            @error('tempat_lahir') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="col-span-1">
            <label for="tanggal_lahir" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Lahir</label>
            <input wire:model.live="tanggal_lahir" type="date" name="tanggal_lahir" id="tanggal_lahir" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 transition-colors duration-200">
            @error('tanggal_lahir') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="col-span-1">
            <label for="umur" class="block text-sm font-semibold text-gray-700 mb-1">Umur</label>
            <input wire:model="umur" type="number" name="umur" id="umur" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 transition-colors duration-200" readonly>
            @error('umur') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- BARIS 3: INFORMASI LAINNYA --}}
        <div class="col-span-1">
            <label for="golongan_darah" class="block text-sm font-semibold text-gray-700 mb-1">Golongan Darah</label>
            <select wire:model="golongan_darah" id="golongan_darah" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 transition-colors duration-200">
                <option value="">Pilih...</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="AB">AB</option>
                <option value="O">O</option>
            </select>
            @error('golongan_darah') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="col-span-1">
            <label for="pendidikan" class="block text-sm font-semibold text-gray-700 mb-1">Pendidikan</label>
            <select wire:model="pendidikan" id="pendidikan" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 transition-colors duration-200">
                <option value="">Pilih...</option>
                <option value="SD">SD</option>
                <option value="SMP">SMP</option>
                <option value="SMA/SMK">SMA/SMK</option>
                <option value="D3">D3</option>
                <option value="S1">S1</option>
                <option value="S2">S2</option>
                <option value="S3">S3</option>
            </select>
            @error('pendidikan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="col-span-1">
            <label for="pekerjaan" class="block text-sm font-semibold text-gray-700 mb-1">Pekerjaan</label>
            <input wire:model="pekerjaan" type="text" name="pekerjaan" id="pekerjaan" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 transition-colors duration-200">
            @error('pekerjaan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="col-span-1">
            <label for="perusahaan_asal" class="block text-sm font-semibold text-gray-700 mb-1">Perusahaan Asal</label>
            <input wire:model="perusahaan_asal" type="text" name="perusahaan_asal" id="perusahaan_asal" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 transition-colors duration-200">
            @error('perusahaan_asal') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- BARIS 4: KONTAK DAN LOKASI ORGANISASI --}}
        <div class="col-span-1">
            <label for="agama" class="block text-sm font-semibold text-gray-700 mb-1">Agama</label>
            <select wire:model="agama" id="agama" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 transition-colors duration-200">
                <option value="">Pilih...</option>
                <option value="Islam">Islam</option>
                <option value="Kristen">Kristen</option>
                <option value="Katolik">Katolik</option>
                <option value="Hindu">Hindu</option>
                <option value="Buddha">Buddha</option>
                <option value="Konghucu">Konghucu</option>
            </select>
            @error('agama') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="col-span-1">
            <label for="no_hp" class="block text-sm font-semibold text-gray-700 mb-1">Nomor HP</label>
            <input wire:model="no_hp" type="text" name="no_hp" id="no_hp" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 transition-colors duration-200">
            @error('no_hp') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="col-span-1">
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
            <input wire:model="email" type="email" name="email" id="email" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 transition-colors duration-200">
            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        @if ($tipe_anggota == 'Non-Karyawan' || in_array($tipe_anggota, ['Istri', 'Suami']))
        <div class="col-span-1">
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
            <input wire:model="password" type="password" id="password" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 transition-colors duration-200">
            @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="col-span-1">
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi Password</label>
            <input wire:model="password_confirmation" type="password" id="password_confirmation" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 transition-colors duration-200">
            @error('password_confirmation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        @endif
            {{-- Jika tipe adalah non_karyawan, Istri, atau Suami, tampilkan dropdown departemen dan unit kerja --}}
            {{-- Departemen dan Unit Kerja hanya relevan untuk karyawan dan pasangan mereka --}}
        @if ($karyawan && in_array($tipe_anggota, ['Istri', 'Suami']))
        <div class="col-span-1">
            <label for="departemens_id" class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
            <select wire:model.live="departemens_id" id="departemens_id" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                <option value="">Pilih Departemen</option>
                @foreach($departemens as $departemen)
                <option value="{{ $departemen->id }}">{{ $departemen->nama_departemen }}</option>
                @endforeach
            </select>
            @error('departemens_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="col-span-1">
            <label for="unit_kerjas_id" class="block text-sm font-medium text-gray-700 mb-1">Unit Kerja</label>
            <select wire:model.live="unit_kerjas_id" id="unit_kerjas_id" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                <option value="">Pilih Unit Kerja</option>
                @foreach($unitKerjas as $unitKerja)
                    <option value="{{ $unitKerja->id }}">{{ $unitKerja->nama_unit_kerja }}</option>
                @endforeach
            </select>
            @error('unit_kerjas_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        @endif

        {{-- BARIS 5: ALAMAT & PASSWORD --}}
        <div class="col-span-1">
            <label for="provinsi_id" class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
            <select wire:model.live="provinsi_id" id="provinsi_id" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                <option value="">Pilih Provinsi</option>
                @foreach($provinsis as $provinsi)
                <option value="{{ $provinsi->id }}">{{ $provinsi->nama_provinsi }}</option>
                @endforeach
            </select>
            @error('provinsi_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="col-span-1">
            <label for="kabupaten_id" class="block text-sm font-medium text-gray-700 mb-1">Kabupaten</label>
            <select wire:model.live="kabupaten_id" id="kabupaten_id" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                <option value="">Pilih Kabupaten</option>
                @foreach($kabupatens as $kabupaten)
                <option value="{{ $kabupaten->id }}">{{ $kabupaten->nama_kabupaten }}</option>
                @endforeach
            </select>
            @error('kabupaten_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="col-span-1">
            <label for="kecamatan_id" class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
            <select wire:model.live="kecamatan_id" id="kecamatan_id" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                <option value="">Pilih Kecamatan</option>
                @foreach($kecamatans as $kecamatan)
                <option value="{{ $kecamatan->id }}">{{ $kecamatan->nama_kecamatan }}</option>
                @endforeach
            </select>
            @error('kecamatan_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="col-span-1">
            <label for="alamat" class="block text-sm font-semibold text-gray-700 mb-1">Alamat Lengkap</label>
            <textarea wire:model="alamat" id="alamat" rows="1" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 transition-colors duration-200"></textarea>
            @error('alamat') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        {{-- BARIS 6: TINGGI & BERAT BADAN --}}
        <div class="col-span-1">
            <label for="tinggi_badan" class="block text-sm font-semibold text-gray-700 mb-1">Tinggi Badan (cm)</label>
            <input wire:model="tinggi_badan" type="number" name="tinggi_badan" id="tinggi_badan" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 transition-colors duration-200" placeholder="Contoh: 175.5">
            @error('tinggi_badan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="col-span-1">
            <label for="berat_badan" class="block text-sm font-semibold text-gray-700 mb-1">Berat Badan (kg)</label>
            <input wire:model="berat_badan" type="number" name="berat_badan" id="berat_badan" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 transition-colors duration-200" placeholder="Contoh: 70.2">
            @error('berat_badan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        {{-- @if ($tipe == 'non_karyawan')
        <div class="col-span-1">
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
            <input wire:model="password" type="password" id="password" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 transition-colors duration-200">
            @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="col-span-1">
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi Password</label>
            <input wire:model="password_confirmation" type="password" id="password_confirmation" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 transition-colors duration-200">
            @error('password_confirmation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        @endif --}}
    </div>
    <div class="mt-8 flex justify-end">
        <button type="submit" class="w-full md:w-auto inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-base font-bold rounded-full text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200 ease-in-out transform hover:scale-105">
            Simpan Data
        </button>
    </div>
</form>