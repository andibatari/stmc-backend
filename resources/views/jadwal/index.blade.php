@extends('layouts.app')
@section('title', 'Daftar Jadwal')

@section('content')
<style>
    [x-cloak] { display: none !important; }
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

{{-- PUSAT KENDALI TUNGGAL --}}
<div x-data="{
        isOpen: false,
        currentId: null,
        currentStatus: '',
        top: 0,
        left: 0,
        openMenu(event, id, status) {
            this.currentId = id;
            this.currentStatus = status;
            
            const rect = event.currentTarget.getBoundingClientRect();
            
            let x = rect.right - 208;
            if (x < 10) x = 10;
            if (x + 208 > window.innerWidth) x = window.innerWidth - 218;
            
            let y = rect.bottom + 5;
            if (y + 250 > window.innerHeight) y = rect.top - 260;
            
            this.left = x;
            this.top = y;
            this.isOpen = true;
        }
     }" 
     class="px-2 md:px-4 py-4 min-h-screen relative">
    
    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6 lg:mb-8">
        <div>
            <h1 class="text-2xl lg:text-3xl font-black text-slate-800">Manajemen Jadwal</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Kelola dan pantau antrean MCU pasien.</p>
        </div>
        <a href="{{ route('jadwal.create') }}" class="hidden md:inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-lg shadow-red-600/30 hover:-translate-y-0.5 transition-all duration-200 text-sm">
            <i class="fas fa-plus mr-2"></i> Tambah Jadwal Baru
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
        <div class="p-6 md:p-8">
            
            <a href="{{ route('jadwal.create') }}" class="md:hidden w-full flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-red-600/30 mb-6 transition-colors">
                <i class="fas fa-plus mr-2"></i> Tambah Jadwal Baru
            </a>

            <div class="mb-8 bg-slate-50 rounded-2xl p-5 border border-slate-100">
                <h2 class="text-sm font-bold text-slate-700 mb-4 flex items-center">
                    <div class="bg-red-100 text-red-600 w-8 h-8 rounded-lg flex items-center justify-center mr-3"><i class="fas fa-users"></i></div>
                    Pantauan Antrean Poli Hari Ini
                </h2>
                <livewire:admin.card-antrean-poli />
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-5 mb-6 flex items-center justify-between shadow-sm">
                <div>
                    <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-1">Status Kuota MCU Hari Ini</h2>
                    <p class="text-xs text-slate-500">{{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-2xl font-black {{ $sisaKuota == 0 ? 'text-red-600' : 'text-emerald-600' }}">
                            {{ $kuotaTerisi }} <span class="text-sm font-medium text-slate-400">/ 30 Pasien</span>
                        </p>
                        <p class="text-[10px] font-bold uppercase tracking-wider {{ $sisaKuota == 0 ? 'text-red-500' : 'text-emerald-500' }}">
                            {{ $sisaKuota == 0 ? 'Kuota Penuh' : 'Tersedia: ' . $sisaKuota }}
                        </p>
                    </div>
                </div>
            </div>

            <form method="GET" action="{{ route('jadwal.index') }}" class="bg-white border border-slate-200 rounded-2xl p-4 mb-6 flex flex-col md:flex-row md:items-end gap-4 shadow-sm">
                <div class="w-full md:w-1/4">
                    <label for="search_sap" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Cari No. SAP</label>
                    <input type="text" name="search_sap" id="search_sap" value="{{ $search_sap ?? '' }}" placeholder="Contoh: 12345678"
                        class="block w-full px-4 py-2.5 text-sm font-medium rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-red-500 focus:ring-red-500 transition-colors">
                </div>
                <div class="w-full md:w-1/4">
                    <label for="tanggal_filter" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Filter Tanggal</label>
                    <div class="flex items-center gap-2">
                        <input type="date" name="tanggal_filter" id="tanggal_filter" value="{{ $tanggal_filter ?? '' }}" 
                            class="block w-full px-4 py-2.5 text-sm font-medium rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-red-500 focus:ring-red-500 transition-colors">
                    </div>
                </div>
                <div class="w-full md:w-1/4">
                    <label for="status_jadwal" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Status</label>
                    <select id="status_jadwal" name="status"
                        class="block w-full px-4 py-2.5 text-sm font-medium rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-red-500 focus:ring-red-500 transition-colors cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="Pending" @if($status == 'Pending') selected @endif>Menunggu</option>
                        <option value="Scheduled" @if($status == 'Scheduled') selected @endif>Terjadwal</option>
                        <option value="Present" @if($status == 'Present') selected @endif>Hadir</option>
                        <option value="Finished" @if($status == 'Finished') selected @endif>Selesai</option>
                        <option value="Canceled" @if($status == 'Canceled') selected @endif>Batal</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-all">Cari</button>
                    <a href="{{ route('jadwal.index') }}" class="px-6 py-2.5 text-sm font-bold text-slate-600 border border-slate-200 bg-white rounded-xl hover:bg-slate-50 transition-all">Reset</a>
                </div>
            </form>

            <div class="overflow-x-auto border border-slate-100 rounded-2xl">
                <table class="min-w-full bg-white text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">No</th>
                            <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider hidden sm:table-cell">Antrean</th>
                            <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider hidden md:table-cell">SAP</th>
                            <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Pasien</th>
                            <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Tgl Daftar</th>
                            <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tgl MCU</th>
                            <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider hidden sm:table-cell">Dokter</th>
                            <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Paket</th>
                            <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse ($jadwals as $jadwalMcu)
                            <tr class="hover:bg-slate-100/100 transition-colors group cursor-pointer" 
                                @click="window.location.href='{{ route('qr-patient-detail', $jadwalMcu->id) }}'">
                                
                                <td class="py-4 px-4 text-sm font-medium text-slate-500">{{ ($jadwals->currentPage() - 1) * $jadwals->perPage() + $loop->iteration }}</td>
                                <td class="py-4 px-4 text-sm font-bold text-slate-700 hidden sm:table-cell">{{ $jadwalMcu->no_antrean ?? '-' }}</td>
                                <td class="py-4 px-4 text-sm text-slate-500 hidden md:table-cell font-mono">{{ $jadwalMcu->no_sap ?? '-' }}</td>
                                
                                <td class="py-4 px-4 text-sm">
                                    <div class="font-bold text-slate-800">
                                        @if ($jadwalMcu->karyawan_id) {{ $jadwalMcu->karyawan->nama_karyawan ?? '-' }}
                                        @elseif ($jadwalMcu->peserta_mcus_id) {{ $jadwalMcu->pesertaMcu->nama_lengkap ?? '-' }}
                                        @else {{ $jadwalMcu->nama_pasien ?? '-' }} @endif
                                    </div>
                                    <div class="text-xs text-slate-400 mt-0.5">
                                        {{ $jadwalMcu->karyawan_id ? 'Karyawan' : 'Non-PTST' }}
                                    </div>
                                </td>

                                <td class="py-4 px-4 text-sm text-slate-600 hidden lg:table-cell">{{ \Carbon\Carbon::parse($jadwalMcu->tanggal_pendaftaran)->format('d M Y') }}</td>
                                <td class="py-4 px-4 text-sm font-semibold text-slate-700">{{ \Carbon\Carbon::parse($jadwalMcu->tanggal_mcu)->format('d M Y') }}</td>
                                
                                <td class="py-4 px-4 text-sm text-slate-600 hidden sm:table-cell">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-[10px]"><i class="fas fa-user-md"></i></div>
                                        <span class="truncate max-w-[120px]">{{ $jadwalMcu->dokter->nama_lengkap ?? '-' }}</span>
                                    </div>
                                </td>
                                
                                <td class="py-4 px-4 text-sm text-slate-600 hidden lg:table-cell">
                                    <span class="bg-slate-100 text-slate-600 px-2.5 py-1 rounded-lg text-xs font-semibold">{{ $jadwalMcu->paketMcu->nama_paket ?? '-' }}</span>
                                </td>
                                
                                <td class="py-4 px-4">
                                    <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full border
                                        @if($jadwalMcu->status === 'Pending') bg-slate-50 text-slate-600 border-slate-200 @endif
                                        @if($jadwalMcu->status === 'Scheduled') bg-amber-50 text-amber-600 border-amber-200 @endif
                                        @if($jadwalMcu->status === 'Present') bg-blue-50 text-blue-600 border-blue-200 @endif
                                        @if($jadwalMcu->status === 'Finished') bg-emerald-50 text-emerald-600 border-emerald-200 @endif
                                        @if($jadwalMcu->status === 'Canceled') bg-red-50 text-red-600 border-red-200 @endif">
                                        @if($jadwalMcu->status === 'Finished') <i class="fas fa-check-circle mr-1 mt-0.5"></i> @endif
                                        {{ $jadwalMcu->status }}
                                    </span>
                                </td>
                                
                                <td class="py-2 px-2 text-xs lg:text-sm text-gray-700 text-center relative" @click.stop>
                                    <div class="flex items-center justify-center gap-2">
                                        
                                        @if($jadwalMcu->status === 'Present')
                                            <form action="{{ route('jadwal.update-status', $jadwalMcu->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="Finished">
                                                <button type="submit" class="bg-emerald-100 text-emerald-600 hover:bg-emerald-500 hover:text-white w-8 h-8 rounded-lg flex items-center justify-center transition-colors shadow-sm" title="Tandai Selesai">
                                                    <i class="fas fa-check-double text-xs"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <div class="inline-block text-left">
                                            {{-- CUKUP PANGGIL openMenu --}}
                                            <button @click.prevent="openMenu($event, {{ $jadwalMcu->id }}, '{{ $jadwalMcu->status }}')" 
                                                    type="button" 
                                                    class="p-2 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors focus:outline-none">
                                                <i class="fas fa-ellipsis-v pointer-events-none"></i>
                                            </button>
                                        </div>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="bg-slate-50 w-16 h-16 rounded-full flex items-center justify-center mb-3">
                                            <i class="fas fa-calendar-times text-2xl text-slate-400"></i>
                                        </div>
                                        <p class="text-slate-500 font-medium">Belum ada jadwal yang terdaftar atau sesuai filter.</p>
                                    </div>
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

    {{-- LAYAR TEMBUS PANDANG (Penangkal Bug Multi-Menu & Bug Macet) --}}
    <div x-show="isOpen" 
         @click="isOpen = false"
         @scroll.window="isOpen = false"
         x-cloak 
         class="fixed inset-0 z-[9998]"></div>

    {{-- KOTAK DROPDOWN TUNGGAL --}}
    <div x-show="isOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-100" 
         x-transition:enter-start="transform opacity-0 scale-95" 
         x-transition:enter-end="transform opacity-100 scale-100" 
         x-transition:leave="transition ease-in duration-75" 
         x-transition:leave-start="transform opacity-100 scale-100" 
         x-transition:leave-end="transform opacity-0 scale-95" 
         x-bind:style="`top: ${top}px; left: ${left}px;`"
         class="fixed w-52 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-[9999] py-1">

        <template x-if="currentStatus === 'Pending'">
            <form :action="`{{ route('jadwal.update-status', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', currentId)" method="POST">
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
                <form :action="`{{ route('jadwal.update-status', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', currentId)" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="Present">
                    <button type="submit" class="flex items-center px-4 py-2 w-full text-left text-sm text-gray-700 hover:bg-gray-100">
                        <svg class="h-4 w-4 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Set Present
                    </button>
                </form>
                <form :action="`{{ route('jadwal.update-status', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', currentId)" method="POST">
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
                <form :action="`{{ route('jadwal.update-status', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', currentId)" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="Canceled">
                    <button type="submit" class="flex items-center px-4 py-2 w-full text-left text-sm text-red-600 hover:bg-red-50">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Cancel Jadwal
                    </button>
                </form>
            </template>

            <a x-bind:href="`{{ route('qr-patient-detail', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', currentId)" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                <svg class="h-5 w-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                Details
            </a>
            <a x-bind:href="`{{ route('jadwal.edit', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', currentId)" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                <svg class="h-5 w-5 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit
            </a>
            <form :action="`{{ route('jadwal.destroy', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', currentId)" method="POST" class="w-full">
                @csrf
                @method('DELETE')
                <button type="submit" class="group flex items-center px-4 py-2 w-full text-left text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200" onclick="return confirm('Yakin ingin menghapus jadwal ini?');">
                    <svg class="h-5 w-5 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Delete
                </button>
            </form>
        </div>
    </div>

</div>
@endsection