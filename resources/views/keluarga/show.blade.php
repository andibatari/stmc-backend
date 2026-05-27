@extends('layouts.app')
@section('head')
    {{-- Halaman ini akan me-refresh dirinya sendiri secara otomatis setiap 3 detik --}}
    <meta http-equiv="refresh" content="3">
@endsection
@section('title', 'Detail Pasien')

@section('content')
<div class="container mx-auto ">
    <livewire:peserta-mcu-detail-manager :pesertaMcu="$pesertaMcu" />
</div>
@endsection

