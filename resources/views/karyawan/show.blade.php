@extends('layouts.app')

{{-- @section('title', 'Manajemen Pasien / Daftar Pasien / Detail Karyawan') --}}

@section('content')
<div class="container mx-auto p-1">
    <livewire:employee-detail-manager :karyawan="$karyawan" />
</div>
@endsection

