@extends('layouts.app')

@section('title', 'Tambah Jadwal MCU')

@section('content')
<div >
    <div class="max-w-6xl mx-auto">
        <a href="{{ route('jadwal.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-100 transition-colors duration-200 mb-8">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Daftar
        </a>

        <div class="bg-white rounded-xl shadow-2xl p-8 md:p-10 border border-gray-100">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Buat Jadwal MCU</h1>
            <p class="text-gray-500 mb-8">Lengkapi informasi di bawah untuk menjadwalkan Medical Check-Up.</p>

            <div x-data="{ tipePasien: 'ptst' }">
                <div class="mb-8">
                    <label class="block text-lg font-semibold text-gray-800 mb-3">Pilih Tipe Pasien</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <button @click="tipePasien = 'ptst'" :class="{ 'bg-red-600 text-white shadow-lg': tipePasien === 'ptst', 'bg-white text-gray-700 border border-gray-300': tipePasien !== 'ptst' }" class="flex items-center justify-center px-6 py-4 rounded-xl font-bold text-lg transition-all duration-300 hover:scale-[1.02] active:scale-100">
                            <span class="mr-2">👨‍💼</span> Pasien Internal (PTST)
                        </button>
                        <button @click="tipePasien = 'non-ptst'" :class="{ 'bg-red-600 text-white shadow-lg': tipePasien === 'non-ptst', 'bg-white text-gray-700 border border-gray-300': tipePasien !== 'non-ptst' }" class="flex items-center justify-center px-6 py-4 rounded-xl font-bold text-lg transition-all duration-300 hover:scale-[1.02] active:scale-100">
                            <span class="mr-2">🏥</span> Pasien Eksternal (Non-PTST)
                        </button>
                    </div>
                </div>

                <div x-show="tipePasien === 'ptst'" x-cloak>
                    <h3 class="text-2xl font-bold text-gray-800 mb-6 border-b border-gray-200 pb-3">Jadwal untuk Pasien Internal</h3>
                    {{-- Form Livewire akan di sini --}}
                    @livewire('jadwal.create-karyawan-form')
                </div>

                <div x-show="tipePasien === 'non-ptst'" x-cloak>
                    <h3 class="text-2xl font-bold text-gray-800 mb-6 border-b border-gray-200 pb-3">Jadwal untuk Pasien Eksternal</h3>
                    <form action="#" method="POST">
                        @csrf
                        <input type="hidden" name="tipe_pasien" value="non-ptst">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="nama_pasien_eksternal" class="block text-sm font-medium text-gray-700 mb-1">Nama Pasien</label>
                                <input type="text" name="nama_pasien" id="nama_pasien_eksternal" class="form-input block w-full px-4 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 placeholder-gray-400" placeholder="Nama lengkap pasien" required>
                            </div>
                            <div>
                                <label for="no_identitas_eksternal" class="block text-sm font-medium text-gray-700 mb-1">No. Identitas (KTP/SIM)</label>
                                <input type="text" name="no_identitas" id="no_identitas_eksternal" class="form-input block w-full px-4 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 placeholder-gray-400" placeholder="Nomor KTP/SIM" required>
                            </div>
                            <div>
                                <label for="perusahaan_afiliasi" class="block text-sm font-medium text-gray-700 mb-1">Perusahaan Afiliasi</label>
                                <input type="text" name="perusahaan_afiliasi" id="perusahaan_afiliasi" class="form-input block w-full px-4 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 placeholder-gray-400" placeholder="Contoh: PT. Mitra Sejahtera" required>
                            </div>
                            <div>
                                <label for="tanggal_lahir_eksternal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir_eksternal" class="form-input block w-full px-4 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" required>
                            </div>
                            <div>
                                <label for="tanggal_mcu_eksternal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal MCU</label>
                                <input type="date" name="tanggal_mcu" id="tanggal_mcu_eksternal" class="form-input block w-full px-4 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" required>
                            </div>
                        </div>
                        <div class="flex justify-end mt-6">
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-md shadow-lg transition duration-150 ease-in-out">
                                Simpan Jadwal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection