@extends('layouts.app')

{{-- @section('title', 'Manajemen Pasien / Daftar Pasien / Detail Karyawan') --}}
@section('head')
    {{-- Halaman ini akan me-refresh dirinya sendiri secara otomatis setiap 3 detik --}}
    <meta http-equiv="refresh" content="3">
@endsection
@section('content')
<div class="container mx-auto p-1">
    <livewire:employee-detail-manager :karyawan="$karyawan" />
</div>
@endsection

