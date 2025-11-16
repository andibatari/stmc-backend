@extends('layouts.app')

@section('title', 'Manajemen Pasien / Daftar Pasien / Tambah Karyawan')

@section('content')
    <div class="container mx-auto p-6">
        <a href="#" onclick="history.back(); return false;" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-100 transition-colors duration-200 mb-6">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali
        </a>
    </div>
    <div class="bg-white rounded-xl shadow-lg p-8 max-w-7xl mx-auto">
        
        <h1 class="text-2xl font-bold text-gray-800 mb-8">Tambah Karyawan Baru</h1>
        @livewire('create-karyawan-form')

    </div>

     @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Livewire akan mendengarkan event 'karyawanSaved'
            Livewire.on('karyawanSaved', () => {
                // Saat event diterima, tampilkan pop-up SweetAlert
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Data karyawan berhasil disimpan.',
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
@endsection
