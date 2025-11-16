<div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-6">
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tipe Anggota</label>
        <p class="text-base font-medium text-gray-900">{{ $user->tipe_anggota ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">NIK Karyawan</label>
        <p class="text-base font-medium text-gray-900">{{ $user->nik_karyawan ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Jenis Kelamin</label>
        <p class="text-base font-medium text-gray-900">{{ $user->jenis_kelamin ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tanggal Lahir</label>
        <p class="text-base font-medium text-gray-900">{{ $user->tanggal_lahir ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Umur</label>
        <p class="text-base font-medium text-gray-900">{{ $user->umur ?? 'N/A' }}</p>
    </div>
    {{-- Tambahkan baris baru untuk Tinggi dan Berat Badan --}}
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tinggi Badan (cm)</label>
        <p class="text-base font-medium text-gray-900">{{ $user->tinggi_badan ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Berat Badan (kg)</label>
        <p class="text-base font-medium text-gray-900">{{ $user->berat_badan ?? 'N/A' }}</p>
    </div>
    
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Pendidikan</label>
        <p class="text-base font-medium text-gray-900">{{ $user->pendidikan ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Kebangsaan</label>
        <p class="text-base font-medium text-gray-900">{{ $user->kebangsaan ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tempat Lahir</label>
        <p class="text-base font-medium text-gray-900">{{ $user->tempat_lahir ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Golongan Darah</label>
        <p class="text-base font-medium text-gray-900">{{ $user->golongan_darah ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Pekerjaan</label>
        <p class="text-base font-medium text-gray-900">{{ $user->pekerjaan ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Agama</label>
        <p class="text-base font-medium text-gray-900">{{ $user->agama ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status Pernikahan</label>
        <p class="text-base font-medium text-gray-900">{{ $user->status_pernikahan ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Hubungan</label>
        <p class="text-base font-medium text-gray-900">{{ $user->hubungan ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Jabatan</label>
        <p class="text-base font-medium text-gray-900">{{ $user->jabatan ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Eselon</label>
        <p class="text-base font-medium text-gray-900">{{ $user->eselon ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Suami/Istri</label>
        <p class="text-base font-medium text-gray-900">{{ $user->suami_istri ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Pekerjaan Suami/Istri</label>
        <p class="text-base font-medium text-gray-900">{{ $user->pekerjaan_suami_istri ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Unit Kerja</label>
        <p class="text-base font-medium text-gray-900">{{ $user->unitKerja->nama_unit_kerja ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Departemen</label>
        <p class="text-base font-medium text-gray-900">{{ $user->departemen->nama_departemen ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Provinsi</label>
        <p class="text-base font-medium text-gray-900">{{ $user->kecamatan->kabupaten->provinsi->nama_provinsi ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Kabupaten</label>
        <p class="text-base font-medium text-gray-900">{{ $user->kecamatan->kabupaten->nama_kabupaten ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Kecamatan</label>
        <p class="text-base font-medium text-gray-900">{{ $user->kecamatan->nama_kecamatan ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Alamat Lengkap</label>
        <p class="text-base font-medium text-gray-900">{{ $user->alamat ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Email</label>
        <p class="text-base font-medium text-gray-900">{{ $user->email ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Nomor HP</label>
        <p class="text-base font-medium text-gray-900">{{ $user->no_hp ?? 'N/A' }}</p>
    </div>
</div>
<div class="mt-8 flex justify-end">
    {{-- Menggunakan kondisi untuk mengarahkan ke rute yang berbeda --}}
    @if ($user->id === $karyawan->id)
        {{-- Tombol Edit untuk Karyawan --}}
        <a href="{{ route('karyawan.edit', ['karyawan' => $karyawan->id]) }}" class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-5 rounded-full shadow-lg transition duration-200 ease-in-out">
            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zm-2.121 2.121L2.828 13.586a2 2 0 00-.573 1.054L2 17.5a1 1 0 001 1l2.859-.255a2 2 0 001.054-.573L14.586 8.414l-2.828-2.828z" />
            </svg>
            Edit Data Karyawan
        </a>
    @else
        {{-- Tombol Edit untuk Pasangan --}}
        <a href="{{ route('keluarga.edit', ['keluarga' => $user->id]) }}" class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-5 rounded-full shadow-lg transition duration-200 ease-in-out">
            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zm-2.121 2.121L2.828 13.586a2 2 0 00-.573 1.054L2 17.5a1 1 0 001 1l2.859-.255a2 2 0 001.054-.573L14.586 8.414l-2.828-2.828z" />
            </svg>
            Edit Data Pasangan
        </a>
    @endif
</div>