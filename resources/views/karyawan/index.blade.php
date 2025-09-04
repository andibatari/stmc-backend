@extends('layouts.app')

@section('title', 'Daftar Karyawan')

@section('content')
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Daftar Karyawan</h1>
        </div>

        @livewire('search-karyawan')

        {{-- Bagian Filter dan Download --}}

        {{-- <!-- Bagian Filter dan Download -->
        <div class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4">
            <div class="flex flex-col md:flex-row items-center gap-4 w-full">
                <input type="text" placeholder="Cari SAP Karyawan" class="w-full md:w-auto p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                <input type="text" placeholder="Cari Nama Karyawan" class="w-full md:w-auto p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                <select class="w-full md:w-auto p-2 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">Unit Kerja</option>
                    <!-- Opsi unit kerja akan diisi dari data database -->
                    <option value="1">Unit of STMC Management</option>
                    <option value="2">Unit of HVC Operation</option>
                </select>
            </div>
            <button class="w-full md:w-auto flex items-center justify-center bg-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-red-700 transition-colors duration-200">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span>Download</span>
            </button>
            
        </div>
        <div class="flex justify-end mb-6">
            <a href="{{ route('karyawan.create') }}" class="flex items-center justify-center bg-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-red-700 transition-colors duration-200">
                <svg class="h-6 w-6 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Karyawan
            </a>
        </div> --}}

        <!-- Tabel Daftar Karyawan -->
        {{-- <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">No</th>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">SAP</th>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">NIK Karyawan</th>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">Nama Karyawan</th>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">Jenis Kelamin</th>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">Unit Kerja</th>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">Departemen</th>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">Tanggal Lahir</th>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">Alamat</th>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">Email</th>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">No. HP</th>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    {{-- Loop data karyawan dari database --}}
                    {{-- Pastikan controller Anda meneruskan data dengan variabel $karyawans --}}
                    {{-- @foreach ($karyawans as $karyawan)
                    <tr>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ $loop->iteration }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ $karyawan->no_sap }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ $karyawan->nik_karyawan }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ $karyawan->nama_karyawan }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ $karyawan->jenis_kelamin }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ $karyawan->unitKerja->nama_unit_kerja ?? 'N/A' }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ $karyawan->departemen->nama_departemen ?? 'N/A' }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ $karyawan->tanggal_lahir }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ $karyawan->alamat }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ $karyawan->email }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ $karyawan->no_hp }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('karyawan.show', $karyawan->id) }}" class="text-blue-600 hover:text-blue-900 transition-colors duration-200" title="Detail">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <a href="{{ route('karyawan.edit', $karyawan->id) }}" class="text-green-600 hover:text-green-900 transition-colors duration-200" title="Edit">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form action="{{ route('karyawan.destroy', $karyawan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 transition-colors duration-200" title="Hapus">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div> --}} 
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('status'))
            Swal.fire({
                title: 'Berhasil!',
                text: "{{ session('status') }}",
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc2626'
            });
        @endif
    });
</script>
@endpush
