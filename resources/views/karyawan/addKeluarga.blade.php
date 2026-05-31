@extends('layouts.app')
@section('title', 'Tambah Pasien')

@section('content')
<div class="px-2 md:px-6 py-6 min-h-screen">
    {{-- Tombol Kembali --}}
    <div class="mb-6 lg:mb-8">
        @if ($karyawan)
            <a href="{{ route('karyawan.show', $karyawan->id) }}" class="inline-flex items-center px-4 py-2 text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-xl shadow-sm hover:bg-slate-50 hover:-translate-x-1 transition-all">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Profil {{ $karyawan->nama_karyawan }}
            </a>
        @else
            <a href="javascript:history.back()" class="inline-flex items-center px-4 py-2 text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-xl shadow-sm hover:bg-slate-50 hover:-translate-x-1 transition-all">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        @endif
    </div>

    <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden p-6 md:p-10 max-w-7xl mx-auto">
        <div class="mb-8 border-b border-slate-100 pb-6">
            <h1 class="text-2xl lg:text-3xl font-black text-slate-800 flex items-center">
                <div class="w-10 h-10 bg-red-100 text-red-600 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas {{ $karyawan ? 'fa-users' : 'fa-user-injured' }} text-xl"></i>
                </div>
                {{ $karyawan ? 'Tambah Anggota Keluarga' : 'Tambah Pasien Umum (Non-PTST)' }}
            </h1>
            <p class="text-sm font-medium text-slate-500 mt-2 ml-14">
                {{ $karyawan ? "Lengkapi data untuk menambahkan keluarga Bapak/Ibu {$karyawan->nama_karyawan}." : 'Lengkapi formulir di bawah untuk meregistrasi pasien dari luar perusahaan.' }}
            </p>
        </div>
        
        <livewire:add-keluarga-karyawan :karyawan_id="$karyawan ? $karyawan->id : null" />
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tanggalLahirInput = document.getElementById('tanggal_lahir');
        if (tanggalLahirInput) {
            tanggalLahirInput.addEventListener('change', function() {
                const birthDate = new Date(this.value);
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const m = today.getMonth() - birthDate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) { age--; }
                Livewire.dispatch('updateUmur', { age: age });
            });
        }
    });

    Livewire.on('updateUmur', (age) => {
        const umurInput = document.getElementById('umur');
        if (umurInput) { umurInput.value = age.age; }
    });

    Livewire.on('show-success-popup', (event) => {
        Swal.fire({
            title: event[0].title, text: event[0].message, icon: 'success', confirmButtonColor: '#dc2626', customClass: { popup: 'rounded-[2rem]' }
        });
    });

    Livewire.on('show-error-popup', (event) => {
        Swal.fire({
            title: 'Error!', text: event[0].message, icon: 'error', confirmButtonColor: '#dc2626', customClass: { popup: 'rounded-[2rem]' }
        });
    });
</script>
@endpush