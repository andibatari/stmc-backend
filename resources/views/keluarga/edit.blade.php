@extends('layouts.app')

@section('title', 'Edit Data Pasien Non Karyawan')

@section('content')
<div class="container mx-auto p-2">
    
    @livewire('keluarga-edit', ['keluarga' => $keluarga])

</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Livewire.on('keluargaUpdated', () => {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Data berhasil diperbarui.',
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc2626'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('karyawan.index', $keluarga->karyawan_id) }}";
                }
            });
        });
    </script>
@endpush
@endsection