<div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-6">
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tipe Anggota</label>
        <p class="text-base font-medium text-gray-900">{{ $pesertaMcu->tipe_anggota ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">NIK</label>
        <p class="text-base font-medium text-gray-900">{{ $pesertaMcu->nik_pasien ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Jenis Kelamin</label>
        <p class="text-base font-medium text-gray-900">{{ $pesertaMcu->jenis_kelamin ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tanggal Lahir</label>
        <p class="text-base font-medium text-gray-900">{{ $pesertaMcu->tanggal_lahir ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Umur</label>
        <p class="text-base font-medium text-gray-900">{{ $pesertaMcu->umur ?? 'N/A' }}</p>
    </div>

    {{-- Tambahkan baris baru untuk Tinggi dan Berat Badan --}}
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tinggi Badan (cm)</label>
        <p class="text-base font-medium text-gray-900">{{ $pesertaMcu->tinggi_badan ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Berat Badan (kg)</label>
        <p class="text-base font-medium text-gray-900">{{ $pesertaMcu->berat_badan ?? 'N/A' }}</p>
    </div>
    
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Pendidikan</label>
        <p class="text-base font-medium text-gray-900">{{ $pesertaMcu->pendidikan ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tempat Lahir</label>
        <p class="text-base font-medium text-gray-900">{{ $pesertaMcu->tempat_lahir ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Golongan Darah</label>
        <p class="text-base font-medium text-gray-900">{{ $pesertaMcu->golongan_darah ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Pekerjaan</label>
        <p class="text-base font-medium text-gray-900">{{ $pesertaMcu->pekerjaan ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Agama</label>
        <p class="text-base font-medium text-gray-900">{{ $pesertaMcu->agama ?? 'N/A' }}</p>
    </div>
    {{-- PERUBAHAN UNTUK LOKASI DI BLOK $pesertaMcu --}}
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Provinsi</label>
        <p class="text-base font-medium text-gray-900">{{ $pesertaMcu->provinsi->nama_provinsi ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Kabupaten</label>
        <p class="text-base font-medium text-gray-900">{{ $pesertaMcu->nama_kabupaten ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Kecamatan</label>
        <p class="text-base font-medium text-gray-900">{{ $pesertaMcu->nama_kecamatan ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Alamat Lengkap</label>
        <p class="text-base font-medium text-gray-900">{{ $pesertaMcu->alamat ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Email</label>
        <p class="text-base font-medium text-gray-900">{{ $pesertaMcu->email ?? 'N/A' }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Nomor HP</label>
        <p class="text-base font-medium text-gray-900">{{ $pesertaMcu->no_hp ?? 'N/A' }}</p>
    </div>
</div>
<div class="mt-8 flex justify-end">
    <a href="{{ route('keluarga.edit', ['keluarga' => $pesertaMcu->id]) }}" class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-5 rounded-full shadow-lg transition duration-200 ease-in-out">
        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zm-2.121 2.121L2.828 13.586a2 2 0 00-.573 1.054L2 17.5a1 1 0 001 1l2.859-.255a2 2 0 001.054-.573L14.586 8.414l-2.828-2.828z"></path></svg>
        Edit Data
    </a>
</div>