@extends('layouts.app')
@section('title', 'Tambah Jadwal MCU')

@section('content')
<div class="px-2 md:px-6 py-6 min-h-screen">
    {{-- Tombol Kembali --}}
    <div class="mb-6 lg:mb-8">
        <a href="{{ route('jadwal.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-xl shadow-sm hover:bg-slate-50 hover:text-slate-800 hover:-translate-x-1 transition-all duration-200">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Jadwal
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6 md:p-10 border border-slate-100">
        <div class="mb-8 border-b border-slate-100 pb-6">
            <h1 class="text-2xl lg:text-3xl font-black text-slate-800 flex items-center">
                <div class="w-10 h-10 bg-red-100 text-red-600 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-calendar-plus text-xl"></i>
                </div>
                Buat Jadwal MCU Baru
            </h1>
            <p class="text-sm font-medium text-slate-500 mt-2 ml-14">Lengkapi informasi di bawah untuk menjadwalkan pemeriksaan pasien.</p>
        </div>
        
        {{-- Form Livewire --}}
        @livewire('jadwal.create-karyawan-form')
    </div>
</div>

{{-- Skrip SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('jadwal-created', (event) => {
            const { type, message } = event[0];
            Swal.fire({
                title: type === 'success' ? 'Berhasil!' : 'Gagal!',
                text: message,
                icon: type,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#dc2626',
                customClass: { popup: 'rounded-3xl' }
            });
        });
    });
</script>
@endsection