@extends('layouts.app')
<meta http-equiv="refresh" content="5">
@section('title', 'Daftar Jadwal')

@section('content')
<div class="px-2 md:px-4 py-4 min-h-screen">
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
            
            {{-- Tombol Tambah Mobile --}}
            <a href="{{ route('jadwal.create') }}" class="md:hidden w-full flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-red-600/30 mb-6 transition-colors">
                <i class="fas fa-plus mr-2"></i> Tambah Jadwal Baru
            </a>

            {{-- AREA ANTREAN POLI (Mini Livewire) --}}
            <div class="mb-8 bg-slate-50 rounded-2xl p-5 border border-slate-100">
                <h2 class="text-sm font-bold text-slate-700 mb-4 flex items-center">
                    <div class="bg-red-100 text-red-600 w-8 h-8 rounded-lg flex items-center justify-center mr-3"><i class="fas fa-users"></i></div>
                    Pantauan Antrean Poli Hari Ini
                </h2>
                <livewire:admin.card-antrean-poli />
            </div>

            {{-- FILTER SECTION --}}
            <form method="GET" action="{{ route('jadwal.index') }}" class="bg-white border border-slate-200 rounded-2xl p-4 mb-6 flex flex-col md:flex-row md:items-end gap-4 shadow-sm">
                <div class="w-full md:w-1/3">
                    <label for="tanggal_filter" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Filter Tanggal</label>
                    <div class="flex items-center gap-2">
                        <input type="date" name="tanggal_filter" id="tanggal_filter" value="{{ $tanggal_filter ?? '' }}" 
                            class="block w-full px-4 py-2.5 text-sm font-medium rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-red-500 focus:ring-red-500 transition-colors" onchange="this.form.submit()">
                        <button type="button" onclick="document.getElementById('tanggal_filter').value = ''; this.closest('form').submit();"
                            class="px-4 py-2.5 text-sm font-bold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 hover:text-slate-800 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="w-full md:w-1/3">
                    <label for="status_jadwal" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Status Pemeriksaan</label>
                    <select id="status_jadwal" name="status" onchange="this.form.submit()"
                        class="block w-full px-4 py-2.5 text-sm font-medium rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-red-500 focus:ring-red-500 transition-colors cursor-pointer">
                        <option value="" @if(!$status) selected @endif>Semua Status</option>
                        <option value="Pending" @if($status == 'Pending') selected @endif>Menunggu (Pending)</option>
                        <option value="Scheduled" @if($status == 'Scheduled') selected @endif>Terjadwal (Scheduled)</option>
                        <option value="Present" @if($status == 'Present') selected @endif>Hadir (Present)</option>
                        <option value="Finished" @if($status == 'Finished') selected @endif>Selesai (Finished)</option>
                        <option value="Canceled" @if($status == 'Canceled') selected @endif>Batal (Canceled)</option>
                    </select>
                </div>

                <div class="w-full md:w-auto ml-auto">
                    <button type="button" onclick="window.location.href = '{{ route('jadwal.index') }}';"
                        class="w-full md:w-auto px-6 py-2.5 text-sm font-bold text-slate-600 border-2 border-slate-200 bg-white rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all">
                        Reset Filter
                    </button>
                </div>
                <noscript><button type="submit" class="hidden">Filter</button></noscript>
            </form>

            {{-- TABLE SECTION --}}
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
                            <tr class="hover:bg-slate-50/50 transition-colors group">
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
                                
                                <td class="py-2 px-2 text-xs lg:text-sm text-gray-700 text-center relative">
                                <div x-data="{ open: false }" @click.outside="open = false" class="inline-block text-left">
                                    <div>
                                        {{-- ✅ PERBAIKAN 1: Tambahkan status: '{{ $jadwalMcu->status }}' agar menu tidak hilang dan gunakan @click.stop --}}
                                        <button @click.stop="open = !open; $dispatch('open-dropdown', { id: {{ $jadwalMcu->id }}, status: '{{ $jadwalMcu->status }}', event: $event })" type="button" class="p-2 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors focus:outline-none" aria-expanded="true" aria-haspopup="true">
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

<div x-data="dropdownMenu()" 
     @open-dropdown.window="openDropdown($event.detail.id, $event.detail.status, $event.detail.event)">
    
    {{-- Tambahkan id="actionDropdownMenu" agar tinggi bisa diukur oleh JavaScript --}}
    <div id="actionDropdownMenu" x-show="open" 
         x-transition:enter="transition ease-out duration-100" 
         x-transition:enter-start="transform opacity-0 scale-95" 
         x-transition:enter-end="transform opacity-100 scale-100" 
         x-transition:leave="transition ease-in duration-75" 
         x-transition:leave-start="transform opacity-100 scale-100" 
         x-transition:leave-end="transform opacity-0 scale-95" 
         x-bind:style="`top: ${top}px; left: ${left}px;`"
         @click.outside="open = false"
         class="fixed w-52 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-[9999] py-1">
         
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
            top: -999, // Render di luar layar dulu agar tidak berkedip
            left: -999,
            openDropdown(id, status, event) {
                this.id = id;
                this.currentStatus = status;

                // Pastikan event target ada
                if (!event || !event.target) return;

                const buttonRect = event.target.closest('button').getBoundingClientRect();
                
                // Hitung posisi horizontal (kiri/kanan)
                let dropdownLeft = buttonRect.left + window.scrollX - 160;
                if (dropdownLeft < 10) dropdownLeft = 10;
                this.left = dropdownLeft;
                
                // Buka menu agar DOM merender elemen dan kita bisa menghitung tingginya
                this.open = true;

                // ✅ PERBAIKAN 2: Deteksi Benturan Bawah Layar (Collision Detection)
                this.$nextTick(() => {
                    const menuEl = document.getElementById('actionDropdownMenu');
                    const menuHeight = menuEl ? menuEl.offsetHeight : 280; // Ambil tinggi menu

                    // Default: Buka ke bawah tombol
                    let dropdownTop = buttonRect.bottom + window.scrollY + 5; 

                    // Jika posisi bawah menu menabrak batas bawah layar browser
                    if (buttonRect.bottom + menuHeight + 20 > window.innerHeight) {
                        // Paksa buka ke ATAS tombol
                        dropdownTop = buttonRect.top + window.scrollY - menuHeight - 5;
                    }

                    this.top = dropdownTop;
                });
            }
        }
    }
</script>
<style>
    [x-cloak] { display: none !important; }
    /* Menyembunyikan Garis Scrollbar Horizontal di HP */
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .hide-scrollbar {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
</style>
@endsection