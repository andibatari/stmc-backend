@extends('layouts.app')

@section('title', 'Manajemen Pasien / Daftar Pasien / Tambah Pasien')

@section('content')
<div class="container mx-auto p-8">
    <div class="bg-white p-8 rounded-xl shadow-2xl border-gray-100">
        @if ($karyawan)
        <a href="{{ route('karyawan.show', $karyawan->id) }}" class="inline-flex items-center text-red-600 hover:text-red-800 font-semibold mb-6 transition-colors duration-200">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Detail Karyawan
        </a>
        <h1 class="text-3xl font-extrabold text-gray-800 mb-6 border-b pb-2">Tambah Anggota Keluarga</h1>
        <p class="text-gray-600 mb-8">Lengkapi data untuk menambahkan anggota keluarga baru untuk <b>{{ $karyawan->nama_karyawan }}</b>.</p>
        <livewire:add-keluarga-karyawan :karyawan_id="$karyawan->id" />
        @else
        <a href="#" onclick="window.history.back(); return false;" class="inline-flex items-center text-red-600 hover:text-red-800 font-semibold mb-6 transition-colors duration-200">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
        <h1 class="text-3xl font-extrabold text-gray-800 mb-6 border-b pb-2">Tambah Pasien Non-Karyawan</h1>
        <p class="text-gray-600 mb-8">Silakan isi formulir di bawah ini untuk menambahkan pasien non-karyawan.</p>
        <livewire:add-keluarga-karyawan  />
        @endif
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
                if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                // Mengirim nilai umur ke Livewire Component
                Livewire.dispatch('updateUmur', { age: age });
            });
        }
    });

    // Event listener untuk update umur di input
    Livewire.on('updateUmur', (age) => {
        const umurInput = document.getElementById('umur');
        if (umurInput) {
            umurInput.value = age.age;
        }
    });

    // Livewire akan mendengarkan event 'karyawanSaved'
    Livewire.on('karyawanSaved', () => {
        // Saat event diterima, tampilkan pop-up SweetAlert
        Swal.fire({
            title: 'Berhasil!',
            text: 'Data anggota keluarga berhasil disimpan.',
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#dc2626'
        }).then((result) => {
            // Setelah pengguna menekan 'OK', arahkan kembali ke halaman detail karyawan
            if (result.isConfirmed) {
                window.location.href = "{{ route('karyawan.index') }}";
            }
        });
    });

    // Livewire akan mendengarkan event 'pesertaSaved'
    Livewire.on('pesertaSaved', () => {
        // Saat event diterima, tampilkan pop-up SweetAlert
        Swal.fire({
            title: 'Berhasil!',
            text: 'Data peserta non-karyawan berhasil disimpan.',
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#dc2626'
        }).then((result) => {
            // Setelah pengguna menekan 'OK', arahkan kembali ke halaman daftar karyawan
            if (result.isConfirmed) {
                window.location.href = "{{ route('karyawan.index') }}";
            }
        });
    });
</script>
@endpush
 