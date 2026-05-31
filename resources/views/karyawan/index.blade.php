@extends('layouts.app')
@section('title', 'Manajemen Pasien / Daftar Pasien')

@section('content')
    <div class="px-2 md:px-4 py-4 min-h-screen">
        <div class="flex justify-between items-center mb-6 lg:mb-8">
            <div>
                <h1 class="text-2xl lg:text-3xl font-black text-slate-800">Manajemen Pasien</h1>
                <p class="text-sm font-medium text-slate-500 mt-1">Basis data Karyawan PTST & Peserta Umum.</p>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden p-6 md:p-8">
            @livewire('search-karyawan')
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
            Swal.fire({
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#dc2626',
                customClass: { popup: 'rounded-3xl' }
            });
        @endif
    });
</script>
@endpush