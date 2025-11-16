@extends('layouts.app')

@section('title', 'Manajemen Pasien / Daftar Pasien / Detail Pasien')

@section('content')
<div class="container mx-auto p-8">
    <livewire:peserta-mcu-detail-manager :pesertaMcu="$pesertaMcu" />
</div>
@endsection

