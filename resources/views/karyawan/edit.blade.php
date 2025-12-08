@extends('layouts.app')

@section('title', 'Edit Data Karyawan')

@section('content')

    {{-- Memanggil komponen Livewire EditKaryawanForm untuk mengelola seluruh form --}}
    @livewire('edit-karyawan-form', ['karyawan' => $karyawan])

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Livewire.on('karyawanUpdated', () => {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Data karyawan berhasil diperbarui.',
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc2626'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('karyawan.index') }}";
                }
            });
        });
    </script>
@endpush
@endsection
