@extends('layouts.app')
@section('head')
    {{-- Halaman ini akan me-refresh dirinya sendiri secara otomatis setiap 30 detik --}}
    <meta http-equiv="refresh" content="20">
@endsection

@section('title', 'Daftar Jadwal')

@section('content')
<div class="container mx-auto p-1 lg:p-4">
    <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 mb-4 lg:mb-6">Manajemen Jadwal MCU</h1>

    <div class="bg-white rounded-xl shadow-lg p-4 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-3">
            <div class="mb-4 md:mb-0">
                <h2 class="text-lg font-semibold text-gray-700">Daftar Jadwal</h2>
            </div>
            <div>
                <a href="{{ route('jadwal.create') }}" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-md shadow-lg transition duration-150 ease-in-out text-sm">
                    + Tambah Jadwal
                </a>
            </div>
        </div>

        {{-- AREA ANTREAN POLI (Horizontal Scroll) --}}
        <div class="mb-6 lg:mb-8">
            <h2 class="text-sm md:text-base font-bold text-gray-800 mb-3 border-b pb-2">
                <i class="fas fa-users mr-2 text-red-600"></i> Antrean Poli Hari Ini
            </h2>
            
            {{-- Container yang bisa di-scroll ke samping --}}
            <div class="flex overflow-x-auto space-x-4 pb-4 snap-x hide-scrollbar">
                
                @forelse ($polis as $poli)
                    @if ($poli->jadwalPoli->count() > 0)
                        {{-- Card Poli --}}
                        <div class="flex-none w-72 bg-gray-50 border border-gray-200 rounded-xl shadow-sm snap-start">
                            <div class="bg-red-600 text-white px-4 py-2 rounded-t-xl flex justify-between items-center">
                                <h3 class="font-bold text-sm truncate">{{ $poli->nama_poli }}</h3>
                                <span class="bg-white text-red-600 text-xs font-bold px-2 py-0.5 rounded-full">
                                    {{ $poli->jadwalPoli->count() }} Antre
                                </span>
                            </div>
                            
                            {{-- Daftar Pasien di dalam Card --}}
                            <div class="p-3 max-h-48 overflow-y-auto space-y-2">
                                @foreach ($poli->jadwalPoli as $index => $antrean)
                                    @php
                                        // 1. Logika PHP ditaruh di sini (tanpa HTML)
                                        // Tentukan nama pasien
                                        $jadwal = $antrean->jadwalMcu;
                                        $namaPasien = '-';
                                        if ($jadwal->karyawan_id) {
                                            $namaPasien = $jadwal->karyawan->nama_karyawan;
                                        } elseif ($jadwal->peserta_mcus_id) {
                                            $namaPasien = $jadwal->pesertaMcu->nama_lengkap;
                                        } else {
                                            $namaPasien = $jadwal->nama_pasien;
                                        }
                                    @endphp
                                    
                                    {{-- 2. HTML ditaruh di bawahnya --}}
                                    <div class="flex items-start bg-white p-2 rounded border border-gray-100 shadow-sm">
                                        <div class="flex-shrink-0 w-6 h-6 bg-red-100 text-red-700 font-bold rounded flex items-center justify-center text-xs mr-3">
                                            {{ $index + 1 }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            {{-- 3. Bungkus nama pasien dengan tag <a> yang bisa diklik --}}
                                            <a href="{{ route('qr-patient-detail', $jadwal->id) }}?tab={{ Str::slug($poli->nama_poli) }}" 
                                                class="text-xs font-bold text-gray-800 hover:text-gray-600 truncate block cursor-pointer">
                                                {{ $namaPasien }}
                                            </a>
                                            <p class="text-[10px] text-gray-500 truncate">SAP: {{ $jadwal->no_sap ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="w-full p-4 bg-gray-50 text-gray-500 text-sm text-center rounded-lg border border-dashed border-gray-300">
                        Belum ada data poli yang tersedia.
                    </div>
                @endforelse

                {{-- Pesan jika tidak ada pasien yang mengantre sama sekali --}}
                @if ($polis->sum(fn($p) => $p->jadwalPoli->count()) === 0)
                    <div class="w-full p-4 bg-green-50 text-green-700 text-sm font-medium text-center rounded-lg border border-green-200">
                        🎉 Tidak ada antrean pasien di semua poli saat ini.
                    </div>
                @endif
            </div>
        </div>
        {{-- END AREA ANTREAN POLI --}}

        <form method="GET" action="{{ route('jadwal.index') }}">
            <div class="flex flex-col md:flex-row md:items-end md:justify-start gap-3 mb-4 lg:gap-4 lg:mb-6">

                <div class="w-full md:w-auto">
                    <label for="tanggal_filter" class="block text-xs font-medium text-gray-700 mb-1">Filter Tanggal</label>
                    <div class="flex items-center gap-2">
                        <input type="date" name="tanggal_filter" id="tanggal_filter" value="{{ $tanggal_filter ?? '' }}" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" onchange="this.form.submit()">
                        <button 
                            type="button" 
                            onclick="document.getElementById('tanggal_filter').value = ''; this.closest('form').submit();"
                            class="px-3 py-2 text-xs font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors duration-200 whitespace-nowrap"
                        >
                            Hapus
                        </button>
                    </div>
                </div>

                <div class="w-full md:w-auto">
                    <label for="status_jadwal" class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                    <select id="status_jadwal" name="status" class="block w-full px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" onchange="this.form.submit()">
                        <option value="" @if(!$status) selected @endif>All</option>
                        <option value="Pending" @if($status == 'Pending') selected @endif>Pending</option>
                        <option value="Scheduled" @if($status == 'Scheduled') selected @endif>Scheduled</option>
                        <option value="Present" @if($status == 'Present') selected @endif>Present</option>
                        <option value="Canceled" @if($status == 'Canceled') selected @endif>Canceled</option>
                        <option value="Finished" @if($status == 'Finished') selected @endif>Finished</option>
                    </select>
                </div>

                <div class="w-full md:w-auto md:self-end">
                    <button 
                        type="button" 
                        onclick="window.location.href = '{{ route('jadwal.index') }}';"
                        class="w-full px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-md hover:bg-red-700 transition-colors duration-200"
                    >
                        Tampilkan Semua
                    </button>
                </div>
            </div>
            <noscript><button type="submit" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-md">Filter</button></noscript>
        </form>

        <div class="overflow-x-auto max-h-[500px] overflow-y-auto border border-gray-200 rounded-lg">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="py-2 px-2 text-xs lg:text-sm font-semibold text-gray-600 text-left">No</th>
                        
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left hidden sm:table-cell">No Antrean</th>
                        
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left hidden md:table-cell">No SAP</th>
                        
                        <th class="py-2 px-2 text-xs lg:text-sm font-semibold text-gray-600 text-left">Nama Pasien</th>
                        
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left hidden lg:table-cell">Tanggal Pendaftaran</th>
                        
                        <th class="py-2 px-2 text-xs lg:text-sm font-semibold text-gray-600 text-left">Tanggal MCU</th>
                        
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left hidden sm:table-cell">Dokter</th>
                        
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left hidden lg:table-cell">Paket</th>
                        
                        <th class="py-2 px-2 text-xs lg:text-sm font-semibold text-gray-600 text-left">Status</th>
                        
                        <th class="py-2 px-2 text-xs lg:text-sm font-semibold text-gray-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($jadwals as $jadwalMcu)
                        <tr>
                            <td class="py-2 px-2 text-xs lg:text-sm text-gray-700">{{ ($jadwals->currentPage() - 1) * $jadwals->perPage() + $loop->iteration }}</td>
                            
                            <td class="py-3 px-4 text-sm text-gray-700 hidden sm:table-cell">{{ $jadwalMcu->no_antrean ?? '-' }}</td>
                            
                            <td class="py-3 px-4 text-sm text-gray-700 hidden md:table-cell">
                                {{ $jadwalMcu->no_sap ?? '-' }}
                            </td>

                            <td class="py-2 px-2 text-xs lg:text-sm text-gray-700 font-medium">
                                @if ($jadwalMcu->karyawan_id)
                                    {{ $jadwalMcu->karyawan->nama_karyawan ?? '-' }}
                                @elseif ($jadwalMcu->peserta_mcus_id)
                                    {{ $jadwalMcu->pesertaMcu->nama_lengkap ?? '-' }}
                                @else
                                    {{ $jadwalMcu->nama_pasien ?? '-' }}
                                @endif
                            </td>

                            <td class="py-3 px-4 text-sm text-gray-700 hidden lg:table-cell whitespace-nowrap">{{ \Carbon\Carbon::parse($jadwalMcu->tanggal_pendaftaran)->format('d-m-Y') }}</td>
                            
                            <td class="py-2 px-2 text-xs lg:text-sm text-gray-700 whitespace-nowrap">{{ \Carbon\Carbon::parse($jadwalMcu->tanggal_mcu)->format('d-m-Y') }}</td>
                            
                            <td class="py-3 px-4 text-sm text-gray-700 hidden sm:table-cell">
                                {{ $jadwalMcu->dokter->nama_lengkap ?? '-' }}
                            </td>
                            
                            <td class="py-3 px-4 text-sm text-gray-700 hidden lg:table-cell">{{ $jadwalMcu->paketMcu->nama_paket ?? '-' }}</td>
                            
                            <td class="py-2 px-2 text-xs lg:text-sm text-gray-700">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($jadwalMcu->status === 'Pending') bg-gray-100 text-gray-800 @endif
                                    @if($jadwalMcu->status === 'Scheduled') bg-yellow-100 text-yellow-800 @endif
                                    @if($jadwalMcu->status === 'Present') bg-gray-100 text-gray-800 @endif
                                    @if($jadwalMcu->status === 'Finished') bg-green-100 text-green-800 @endif
                                    @if($jadwalMcu->status === 'Canceled') bg-red-100 text-red-800 @endif">
                                    {{ $jadwalMcu->status }}
                                </span>
                            </td>
                            
                            <td class="py-2 px-2 text-xs lg:text-sm text-gray-700 text-center relative">
                                <div x-data="{ open: false }" @click.outside="open = false" class="inline-block text-left">
                                    <div>
                                        <button @click="open = !open; $dispatch('open-dropdown', { id: {{ $jadwalMcu->id }}, event: $event })" type="button" class="flex items-center text-gray-400 hover:text-gray-600 transition-colors duration-200 focus:outline-none p-1" aria-expanded="true" aria-haspopup="true">
                                            <svg class="h-4 w-4 lg:h-5 lg:w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-4 text-center text-gray-500">
                                Belum ada jadwal yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-6">
            {{ $jadwals->appends(request()->input())->links() }}
        </div>
    </div>
</div>

<div x-data="dropdownMenu()" 
     @open-dropdown.window="openDropdown($event.detail.id, $event.detail.status, $event.detail.event)">
    
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-100" 
         x-transition:enter-start="transform opacity-0 scale-95" 
         x-transition:enter-end="transform opacity-100 scale-100" 
         x-transition:leave="transition ease-in duration-75" 
         x-transition:leave-start="transform opacity-100 scale-100" 
         x-transition:leave-end="transform opacity-0 scale-95" 
         x-bind:style="`top: ${top}px; left: ${left}px;`"
         @click.outside="open = false"
         class="fixed w-52 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 py-1">
         
        <template x-if="currentStatus === 'Pending'">
            <form x-data :action="`{{ route('jadwal.update-status', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', id)" method="POST">
                @csrf
                <input type="hidden" name="status" value="Scheduled">
                <button type="submit" class="flex items-center px-4 py-2 w-full text-left text-sm text-white bg-green-600 hover:bg-green-700 font-bold border-b border-green-700">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Approve Jadwal
                </button>
            </form>
        </template>

        <template x-if="currentStatus !== 'Pending' && currentStatus !== 'Finished' && currentStatus !== 'Canceled'">
            <div class="border-b border-gray-100">
                <form x-data :action="`{{ route('jadwal.update-status', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', id)" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="Present">
                    <button type="submit" class="flex items-center px-4 py-2 w-full text-left text-sm text-gray-700 hover:bg-gray-100">
                        <svg class="h-4 w-4 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Set Present
                    </button>
                </form>
                <form x-data :action="`{{ route('jadwal.update-status', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', id)" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="Finished">
                    <button type="submit" class="flex items-center px-4 py-2 w-full text-left text-sm text-gray-700 hover:bg-gray-100">
                        <svg class="h-4 w-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Set Finished
                    </button>
                </form>
            </div>
        </template>

        <div class="py-1">
            <template x-if="currentStatus !== 'Finished' && currentStatus !== 'Canceled'">
                <form x-data :action="`{{ route('jadwal.update-status', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', id)" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="Canceled">
                    <button type="submit" class="flex items-center px-4 py-2 w-full text-left text-sm text-red-600 hover:bg-red-50">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Cancel Jadwal
                    </button>
                </form>
            </template>

            <a x-bind:href="`{{ route('qr-patient-detail', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', id)" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200" title="Detail" role="menuitem">
                <svg class="h-5 w-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                Details
            </a>
            <a x-bind:href="`{{ route('jadwal.edit', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', id)" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200" title="Edit Jadwal" role="menuitem">
                <svg class="h-5 w-5 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit
            </a>
            <form x-data :action="`{{ route('jadwal.destroy', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', id)" method="POST" class="w-full">
                @csrf
                @method('DELETE')
                <button type="submit" class="group flex items-center px-4 py-2 w-full text-left text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200" role="menuitem">
                    <svg class="h-5 w-5 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Delete
                </button>
            </form>
        </div>
      </div>
</div>

<script>
    function dropdownMenu() {
        return {
            open: false,
            id: null,
            currentStatus: '',
            top: 0,
            left: 0,
            openDropdown(id, status, event) {
                this.id = id;
                this.currentStatus = status;
                const buttonRect = event.target.closest('button').getBoundingClientRect();
                this.top = buttonRect.bottom + window.scrollY + 5; 
                let dropdownLeft = buttonRect.left + window.scrollX - 160;
                if (dropdownLeft < 10) dropdownLeft = 10;
                this.left = dropdownLeft;
                this.open = true;
            }
        }
    }
</script>
<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
