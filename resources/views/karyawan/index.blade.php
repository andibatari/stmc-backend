@extends('layouts.app')

@section('title', 'Manajemen Pasien / Daftar Pasien')

@section('content')
    <div class="bg-white rounded-xl shadow-md p-4 lg:p-6">
        <div class="flex justify-between items-center mb-4 lg:mb-6">
            <h1 class="text-xl lg:text-2xl font-bold text-gray-800">Daftar Pasien</h1>
        </div>

        @livewire('search-karyawan')
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
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc2626'
            });
        @endif
    });
</script>
@endpush
