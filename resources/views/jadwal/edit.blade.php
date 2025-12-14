@extends('layouts.app')
@section('title', 'Manajemen Jadwal / Edit Jadwal')
@section('content')
<div class="container mx-auto p-4">
    <a href="{{ route('jadwal.index') }}" class="inline-flex items-center px-3 py-1.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-100 transition-colors duration-200 mb-4 lg:mb-8">
        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Kembali
    </a>
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Jadwal MCU</h1>
    <livewire:jadwal.create-karyawan-form :jadwalId="$jadwal->id" />
</div>
@endsection