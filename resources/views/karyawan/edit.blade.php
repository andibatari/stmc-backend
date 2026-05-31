@extends('layouts.app')
@section('title', 'Edit Data Karyawan')

@section('content')
<div class="px-2 md:px-6 py-6 min-h-screen">
    @livewire('edit-karyawan-form', ['karyawan' => $karyawan])
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Livewire.on('karyawanUpdated', () => {
        Swal.fire({
            title: 'Berhasil!',
            text: 'Data karyawan sukses diperbarui.',
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