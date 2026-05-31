@extends('layouts.app')
@section('title', 'Tambah Karyawan')

@section('content')
<div class="px-2 md:px-6 py-6 min-h-screen">
    {{-- Tombol Kembali --}}
    <div class="mb-6 lg:mb-8">
        <a href="javascript:history.back()" class="inline-flex items-center px-4 py-2 text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-xl shadow-sm hover:bg-slate-50 hover:text-slate-800 hover:-translate-x-1 transition-all duration-200">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden p-6 md:p-10 max-w-7xl mx-auto">
        <div class="mb-8 border-b border-slate-100 pb-6">
            <h1 class="text-2xl lg:text-3xl font-black text-slate-800 flex items-center">
                <div class="w-10 h-10 bg-red-100 text-red-600 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-user-plus text-xl"></i>
                </div>
                Tambah Karyawan Baru
            </h1>
            <p class="text-sm font-medium text-slate-500 mt-2 ml-14">Lengkapi form di bawah ini untuk menambahkan data karyawan ke dalam sistem.</p>
        </div>
        
        @livewire('create-karyawan-form')
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Livewire.on('karyawanSaved', () => {
        Swal.fire({
            title: 'Berhasil!',
            text: 'Data karyawan berhasil disimpan.',
            icon: 'success',
            confirmButtonText: 'Lanjutkan',
            confirmButtonColor: '#dc2626',
            customClass: { popup: 'rounded-[2rem]' }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('karyawan.index') }}";
            }
        });
    });
</script>
@endpush
@endsection