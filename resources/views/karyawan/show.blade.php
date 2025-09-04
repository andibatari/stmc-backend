@extends('layouts.app')

@section('title', 'Detail Karyawan')

@section('content')
<div class="container mx-auto p-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Card Profil Karyawan -->
        <div class="w-full lg:w-1/3">
            <div class="p-6 bg-white rounded-xl shadow-2xl border border-gray-100">
                <div class="flex flex-col items-center text-center">
                    @if ($karyawan->foto_profil)
                        <img src="{{ asset('storage/' . $karyawan->foto_profil) }}" alt="Foto Profil" class="w-32 h-32 object-cover rounded-full shadow-lg border-4 border-white mb-4">
                    @else
                        <div class="w-32 h-32 bg-gray-200 flex items-center justify-center rounded-full shadow-lg border-4 border-white mb-4">
                            <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                        </div>
                    @endif
                    <h2 class="text-xl font-bold text-gray-800">{{ $karyawan->nama_karyawan }}</h2>
                    <p class="text-sm text-gray-500">{{ $karyawan->unitKerja->nama_unit_kerja ?? 'N/A' }}</p>
                </div>
                <div class="mt-6 text-center">
                     <p class="text-gray-500 font-semibold">No SAP: <span class="text-gray-900 font-normal">{{ $karyawan->no_sap }}</span></p>
                </div>
            </div>
        </div>

        <!-- Card Tab (Data Karyawan & Riwayat MCU) -->
        <div class="w-full lg:w-6/3">
            <div class="p-6 bg-white rounded-xl shadow-2xl border border-gray-100">
                <div x-data="{ activeTab: 'data-karyawan' }" class="bg-gray-50 rounded-xl shadow-md border border-gray-200">
                    <!-- Tab Headers -->
                    <div class="flex border-b border-gray-200">
                        <button @click="activeTab = 'data-karyawan'" :class="{ 'bg-white border-b-2 border-red-500 text-red-600': activeTab === 'data-karyawan', 'text-gray-600 hover:text-red-600': activeTab !== 'data-karyawan' }" class="py-4 px-6 font-semibold text-sm transition-colors duration-200">
                            Data Karyawan
                        </button>
                        <button @click="activeTab = 'riwayat-mcu'" :class="{ 'bg-white border-b-2 border-red-500 text-red-600': activeTab === 'riwayat-mcu', 'text-gray-600 hover:text-red-600': activeTab !== 'riwayat-mcu' }" class="py-4 px-6 font-semibold text-sm transition-colors duration-200">
                            Riwayat MCU
                        </button>
                    </div>

                    <!-- Tab Content -->
                    <div class="p-6">
                        <!-- Konten Data Karyawan -->
                        <div x-show="activeTab === 'data-karyawan'">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-6">
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">NIK Karyawan</label>
                                    <p class="text-base font-medium text-gray-900">{{ $karyawan->nik_karyawan }}</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Jenis Kelamin</label>
                                    <p class="text-base font-medium text-gray-900">{{ $karyawan->jenis_kelamin }}</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tanggal Lahir</label>
                                    <p class="text-base font-medium text-gray-900">{{ $karyawan->tanggal_lahir }}</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Umur</label>
                                    <p class="text-base font-medium text-gray-900">{{ $karyawan->umur ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Pendidikan</label>
                                    <p class="text-base font-medium text-gray-900">{{ $karyawan->pendidikan ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Kebangsaan</label>
                                    <p class="text-base font-medium text-gray-900">{{ $karyawan->kebangsaan ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tempat Lahir</label>
                                    <p class="text-base font-medium text-gray-900">{{ $karyawan->tempat_lahir ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Golongan Darah</label>
                                    <p class="text-base font-medium text-gray-900">{{ $karyawan->golongan_darah ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Pekerjaan</label>
                                    <p class="text-base font-medium text-gray-900">{{ $karyawan->pekerjaan ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Agama</label>
                                    <p class="text-base font-medium text-gray-900">{{ $karyawan->agama ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status Pernikahan</label>
                                    <p class="text-base font-medium text-gray-900">{{ $karyawan->status_pernikahan ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Hubungan</label>
                                    <p class="text-base font-medium text-gray-900">{{ $karyawan->hubungan ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Jabatan</label>
                                    <p class="text-base font-medium text-gray-900">{{ $karyawan->jabatan ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Eselon</label>
                                    <p class="text-base font-medium text-gray-900">{{ $karyawan->eselon ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Suami/Istri</label>
                                    <p class="text-base font-medium text-gray-900">{{ $karyawan->suami_istri ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Pekerjaan Suami/Istri</label>
                                    <p class="text-base font-medium text-gray-900">{{ $karyawan->pekerjaan_suami_istri ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Unit Kerja</label>
                                    <p class="text-base font-medium text-gray-900">{{ $karyawan->unitKerja->nama_unit_kerja ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Departemen</label>
                                    <p class="text-base font-medium text-gray-900">{{ $karyawan->departemen->nama_departemen ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Provinsi</label>
                                    <p class="text-base font-medium text-gray-900">{{ $karyawan->kecamatan->kabupaten->provinsi->nama_provinsi ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Kabupaten</label>
                                    <p class="text-base font-medium text-gray-900">{{ $karyawan->kecamatan->kabupaten->nama_kabupaten ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Kecamatan</label>
                                    <p class="text-base font-medium text-gray-900">{{ $karyawan->kecamatan->nama_kecamatan ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Alamat Lengkap</label>
                                    <p class="text-base font-medium text-gray-900">{{ $karyawan->alamat }}</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Email</label>
                                    <p class="text-base font-medium text-gray-900">{{ $karyawan->email }}</p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Nomor HP</label>
                                    <p class="text-base font-medium text-gray-900">{{ $karyawan->no_hp }}</p>
                                </div>
                            </div>
                            <!-- Tombol edit dipindahkan ke sini, di dalam konten Data Karyawan -->
                            <div class="mt-8 flex justify-end">
                                <a href="{{ route('karyawan.edit', $karyawan->id) }}" class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-5 rounded-full shadow-lg transition duration-200 ease-in-out">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Edit Data Karyawan
                                </a>
                            </div>
                        </div>

                        <!-- Konten Riwayat MCU -->
                        <div x-show="activeTab === 'riwayat-mcu'">
                            <!-- Placeholder untuk tabel Riwayat MCU -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">No</th>
                                            <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">Tanggal MCU</th>
                                            <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">Dokter</th>
                                            <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">Status</th>
                                            <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        {{-- Anda bisa menampilkan riwayat MCU di sini dengan loop --}}
                                        <tr>
                                            <td colspan="5" class="py-4 text-center text-gray-500">
                                                Tidak ada riwayat MCU.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
