@extends('layouts.app')

@section('title', 'Detail Pasien')

@section('content')
<div class="container mx-auto ">
    <livewire:peserta-mcu-detail-manager :pesertaMcu="$pesertaMcu" />
</div>
@endsection

