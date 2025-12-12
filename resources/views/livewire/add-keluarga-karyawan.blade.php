<form wire:submit.prevent="save">
    
    <h3 class="text-base font-bold text-gray-800 mb-3 border-b pb-1 mt-4">👤 Informasi Dasar Pasien</h3>

    {{-- Bagian 1: Informasi Identitas & Dasar --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        
        <div>
            <label for="tipe_anggota" class="block text-xs font-semibold text-gray-700 mb-1">Tipe Anggota</label>
            <select wire:model.live="tipe_anggota" id="tipe_anggota" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2 transition-colors duration-200">
                <option value="">Pilih...</option>
                @if ($karyawan)
                <option value="Istri">Istri</option>
                <option value="Suami">Suami</option>
                @else
                <option value="Non-Karyawan">Non-Karyawan</option>
                @endif
            </select>
            @error('tipe_anggota') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="nama_lengkap" class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap</label>
            <input wire:model="nama_lengkap" type="text" name="nama_lengkap" id="nama_lengkap" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2 transition-colors duration-200" required>
            @error('nama_lengkap') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="nik_pasien" class="block text-xs font-semibold text-gray-700 mb-1">NIK/Nomor Identitas</label>
            <input wire:model="nik_pasien" type="text" name="nik_pasien" id="nik_pasien" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2 transition-colors duration-200" required>
            @error('nik_pasien') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="no_sap" class="block text-xs font-semibold text-gray-700 mb-1">NIP/SAP (dari perusahaan)</label>
            <input wire:model="no_sap" type="text" name="no_sap" id="no_sap" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2 transition-colors duration-200">
            @error('no_sap') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="perusahaan_asal" class="block text-xs font-semibold text-gray-700 mb-1">Perusahaan Asal</label>
            <input wire:model="perusahaan_asal" type="text" name="perusahaan_asal" id="perusahaan_asal" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2 transition-colors duration-200">
            @error('perusahaan_asal') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
    </div>
    
    <h3 class="text-base font-bold text-gray-800 mb-3 border-b pb-1 mt-6">📅 Data Lahir & Fisik</h3>

    {{-- Bagian 2: Data Kelahiran & Fisik --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <div>
            <label for="jenis_kelamin" class="block text-xs font-semibold text-gray-700 mb-1">Jenis Kelamin</label>
            <select wire:model="jenis_kelamin" id="jenis_kelamin" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2 transition-colors duration-200">
                <option value="">Pilih...</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>
            @error('jenis_kelamin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="tempat_lahir" class="block text-xs font-semibold text-gray-700 mb-1">Tempat Lahir</label>
            <input wire:model="tempat_lahir" type="text" name="tempat_lahir" id="tempat_lahir" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2 transition-colors duration-200">
            @error('tempat_lahir') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="tanggal_lahir" class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Lahir</label>
            <input wire:model.live="tanggal_lahir" type="date" name="tanggal_lahir" id="tanggal_lahir" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2 transition-colors duration-200">
            @error('tanggal_lahir') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="umur" class="block text-xs font-semibold text-gray-700 mb-1">Umur</label>
            <input wire:model="umur" type="number" name="umur" id="umur" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2 transition-colors duration-200 bg-gray-100" readonly>
            @error('umur') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="golongan_darah" class="block text-xs font-semibold text-gray-700 mb-1">Golongan Darah</label>
            <select wire:model="golongan_darah" id="golongan_darah" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2 transition-colors duration-200">
                <option value="">Pilih...</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="AB">AB</option>
                <option value="O">O</option>
            </select>
            @error('golongan_darah') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="tinggi_badan" class="block text-xs font-semibold text-gray-700 mb-1">Tinggi Badan (cm)</label>
            <input wire:model="tinggi_badan" type="number" name="tinggi_badan" id="tinggi_badan" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2 transition-colors duration-200" placeholder="Contoh: 175.5">
            @error('tinggi_badan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="berat_badan" class="block text-xs font-semibold text-gray-700 mb-1">Berat Badan (kg)</label>
            <input wire:model="berat_badan" type="number" name="berat_badan" id="berat_badan" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2 transition-colors duration-200" placeholder="Contoh: 70.2">
            @error('berat_badan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="pendidikan" class="block text-xs font-semibold text-gray-700 mb-1">Pendidikan</label>
            <select wire:model="pendidikan" id="pendidikan" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2 transition-colors duration-200">
                <option value="">Pilih...</option>
                <option value="SD">SD</option>
                <option value="SMP">SMP</option>
                <option value="SMA/SMK">SMA/SMK</option>
                <option value="D3">D3</option>
                <option value="S1">S1</option>
                <option value="S2">S2</option>
                <option value="S3">S3</option>
            </select>
            @error('pendidikan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
    </div>

    <h3 class="text-base font-bold text-gray-800 mb-3 border-b pb-1 mt-6">📞 Kontak & Akun</h3>

    {{-- Bagian 3: Kontak, Agama & Akun --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <div>
            <label for="no_hp" class="block text-xs font-semibold text-gray-700 mb-1">Nomor HP</label>
            <input wire:model="no_hp" type="text" name="no_hp" id="no_hp" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2 transition-colors duration-200">
            @error('no_hp') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="email" class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
            <input wire:model="email" type="email" name="email" id="email" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2 transition-colors duration-200">
            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="agama" class="block text-xs font-semibold text-gray-700 mb-1">Agama</label>
            <select wire:model="agama" id="agama" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2 transition-colors duration-200">
                <option value="">Pilih...</option>
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
            <label for="pekerjaan" class="block text-xs font-semibold text-gray-700 mb-1">Pekerjaan</label>
            <input wire:model="pekerjaan" type="text" name="pekerjaan" id="pekerjaan" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2 transition-colors duration-200">
            @error('pekerjaan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        @if ($tipe_anggota == 'Non-Karyawan' || in_array($tipe_anggota, ['Istri', 'Suami']))
        <div>
            <label for="password" class="block text-xs font-semibold text-gray-700 mb-1">Password</label>
            <input wire:model="password" type="password" id="password" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2 transition-colors duration-200">
            @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        
        {{-- Input Konfirmasi Password --}}
        <div> 
            <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 mb-1">Konfirmasi Password</label>
            <input wire:model="password_confirmation" type="password" id="password_confirmation" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2 transition-colors duration-200">
            @error('password_confirmation') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        @endif
    </div>

    <h3 class="text-base font-bold text-gray-800 mb-3 border-b pb-1 mt-6">📍 Alamat Domisili</h3>

    {{-- Bagian 4: Alamat (Dropdown dan Alamat Lengkap) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <div>
            <label for="provinsi_id" class="block text-xs font-medium text-gray-700 mb-1">Provinsi</label>
            <select wire:model.live="provinsi_id" id="provinsi_id" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                <option value="">Pilih Provinsi</option>
                @foreach($provinsis as $provinsi)
                <option value="{{ $provinsi->id }}">{{ $provinsi->nama_provinsi }}</option>
                @endforeach
            </select>
            @error('provinsi_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="nama_kabupaten" class="block text-xs font-medium text-gray-700 mb-1">Kabupaten/Kota</label>
            <input wire:model.live="nama_kabupaten" type="text" id="nama_kabupaten" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="Contoh: Jakarta Pusat">
            @error('nama_kabupaten') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="nama_kecamatan" class="block text-xs font-medium text-gray-700 mb-1">Kecamatan</label>
            <input wire:model.live="nama_kecamatan" type="text" id="nama_kecamatan" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="Contoh: Tanah Abang">
            @error('nama_kecamatan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div class="col-span-1 lg:col-span-4">
            <label for="alamat" class="block text-xs font-semibold text-gray-700 mb-1">Alamat Lengkap</label>
            <textarea wire:model="alamat" id="alamat" rows="2" class="block w-full rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2 transition-colors duration-200"></textarea>
            @error('alamat') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
    </div>
    
    {{-- Tombol Simpan --}}
    <div class="mt-6 flex justify-end">
        <button type="submit" class="w-full md:w-auto inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-bold rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-200 ease-in-out">
            Simpan Data
        </button>
    </div>
</form>