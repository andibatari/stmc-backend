@extends('layouts.app')

@section('title', 'Tambah Jadwal MCU')

@section('content')
<div class="max-w-full mx-auto py-4 lg:py-8 px-4 lg:px-0">
    <a href="{{ route('jadwal.index') }}" class="inline-flex items-center px-3 py-1.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-100 transition-colors duration-200 mb-4 lg:mb-8">
        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Kembali ke Daftar
    </a>

    <div class="bg-white rounded-xl shadow-lg p-4 md:p-8 border border-gray-100">
        <h1 class="text-2xl lg:text-3xl font-extrabold text-gray-900 mb-2">Buat Jadwal MCU</h1>
        <p class="text-sm text-gray-500 mb-6 lg:mb-8">Lengkapi informasi di bawah untuk menjadwalkan Medical Check-Up.</p>
        
        {{-- Form Livewire yang sudah disatukan --}}
        @livewire('jadwal.create-karyawan-form')
    </div>
    
</div>

{{-- Tambahkan skrip SweetAlert2 di sini --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('jadwal-created', (event) => {
            const { type, message } = event[0];
            Swal.fire({
                title: type === 'success' ? 'Berhasil!' : 'Gagal!',
                text: message,
                icon: type,
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc2626'
            });
        });
    });
</script>
@endsection