@extends('layouts.app')

@section('title', 'Manajemen Jadwal MCU')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Manajemen Jadwal MCU</h1>

    <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-700">Daftar Jadwal</h2>
            <a href="{{ route('jadwal.create') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-md shadow-lg transition duration-150 ease-in-out mt-4 md:mt-0">
                + Tambah Jadwal
            </a>
        </div>

        <form method="GET" action="{{ route('jadwal.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
<div>
                    <label for="tanggal_filter" class="block text-sm font-medium text-gray-700 mb-1">Filter Tanggal</label>
                    <div class="flex items-center gap-2">
                        <input type="date" name="tanggal_filter" id="tanggal_filter" value="{{ request('tanggal_filter') }}" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" onchange="this.form.submit()">
                        <button type="button" onclick="document.getElementById('tanggal_filter').value = ''; this.form.submit();" class="px-3 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors duration-200">
                            Hapus
                        </button>
                    </div>
                </div>
                 <div>
                    <label for="tipe_pasien" class="block text-sm font-medium text-gray-700 mb-1">Tipe Pasien</label>
                    <select id="tipe_pasien" name="tipe_pasien" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" onchange="this.form.submit()">
                        <option value="" @if(!$tipe_pasien) selected @endif>All</option>
                        <option value="ptst" @if($tipe_pasien == 'ptst') selected @endif>PTST</option>
                        <option value="non-ptst" @if($tipe_pasien == 'non-ptst') selected @endif>Non-PTST</option>
                    </select>
                </div>
                <div>
                    <label for="status_jadwal" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status_jadwal" name="status" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" onchange="this.form.submit()">
                        <option value="" @if(!$status) selected @endif>All</option>
                        <option value="Scheduled" @if($status == 'Scheduled') selected @endif>Scheduled</option>
                        <option value="Waited" @if($status == 'Waited') selected @endif>Waited</option>
                        <option value="Canceled" @if($status == 'Canceled') selected @endif>Canceled</option>
                        <option value="Finished" @if($status == 'Finished') selected @endif>Finished</option>
                    </select>
                </div>
            </div>
            <noscript><button type="submit" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-md">Filter</button></noscript>
        </form>

        <div class="overflow-x-auto max-h-[500px] overflow-y-auto border border-gray-200 rounded-lg">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">No</th>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">No Antrean</th>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">No SAP</th>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">Nama Pasien</th>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">Tanggal Pendaftaran</th>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">Tanggal MCU</th>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">Dokter</th>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">Tipe Pasien</th>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-left">Status</th>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($jadwals as $jadwalMcu)
                        <tr>
                            <td class="py-3 px-4 text-sm text-gray-700">{{ ($jadwals->currentPage() - 1) * $jadwals->perPage() + $loop->iteration }}</td>
                            <td class="py-3 px-4 text-sm text-gray-700">{{ $jadwalMcu->no_antrean ?? '-' }}</td>
                            
                            <td class="py-3 px-4 text-sm text-gray-700">
                                @if ($jadwalMcu->tipe_pasien === 'ptst')
                                    {{ $jadwalMcu->karyawan->no_sap ?? '-' }}
                                @else
                                    -
                                @endif
                            </td>
                            
                            <td class="py-3 px-4 text-sm text-gray-700">
                                @if ($jadwalMcu->tipe_pasien === 'ptst')
                                    {{ $jadwalMcu->karyawan->nama_karyawan ?? '-' }}
                                @else
                                    {{ $jadwalMcu->nama_pasien ?? '-' }}
                                @endif
                            </td>

                            <td class="py-3 px-4 text-sm text-gray-700">{{ \Carbon\Carbon::parse($jadwalMcu->tanggal_pendaftaran)->format('d-m-Y') }}</td>
                            <td class="py-3 px-4 text-sm text-gray-700">{{ \Carbon\Carbon::parse($jadwalMcu->tanggal_mcu)->format('d-m-Y') }}</td>
                            <td class="py-3 px-4 text-sm text-gray-700">{{ $jadwalMcu->dokter ?? '-' }}</td>
                            <td class="py-3 px-4 text-sm text-gray-700">{{ strtoupper($jadwalMcu->tipe_pasien) }}</td>
                            <td class="py-3 px-4 text-sm text-gray-700">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($jadwalMcu->status === 'Scheduled') bg-yellow-100 text-yellow-800 @endif
                                    @if($jadwalMcu->status === 'Finished') bg-green-100 text-green-800 @endif
                                    @if($jadwalMcu->status === 'Canceled') bg-red-100 text-red-800 @endif">
                                    {{ $jadwalMcu->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-700 text-center relative">
                                <div x-data="{ open: false }" @click.outside="open = false" class="inline-block text-left">
                                    <div>
                                        <button @click="open = !open; $dispatch('open-dropdown', { id: {{ $jadwalMcu->id }}, event: $event })" type="button" class="flex items-center text-gray-400 hover:text-gray-600 transition-colors duration-200 focus:outline-none" aria-expanded="true" aria-haspopup="true">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
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
     @open-dropdown.window="openDropdown($event.detail.id, $event.detail.event)">
    
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-100" 
         x-transition:enter-start="transform opacity-0 scale-95" 
         x-transition:enter-end="transform opacity-100 scale-100" 
         x-transition:leave="transition ease-in duration-75" 
         x-transition:leave-start="transform opacity-100 scale-100" 
         x-transition:leave-end="transform opacity-0 scale-95" 
         x-bind:style="`top: ${top}px; left: ${left}px;`"
         @click.outside="open = false"
         class="fixed w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
         
         <div class="py-1">
             <form x-data :action="`{{ route('jadwal.update-status', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', id)" method="POST">
                 @csrf
                 <input type="hidden" name="status" value="Scheduled">
                 <button type="submit" class="group flex items-center px-4 py-2 w-full text-left text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200" role="menuitem">
                     <svg class="h-5 w-5 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h6m-1 0v6a2 2 0 01-2 2H8a2 2 0 01-2-2v-6z"></path></svg>
                     Scheduled
                 </button>
             </form>
             
             <form x-data :action="`{{ route('jadwal.update-status', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', id)" method="POST">
                 @csrf
                 <input type="hidden" name="status" value="Waited">
                 <button type="submit" class="group flex items-center px-4 py-2 w-full text-left text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200" role="menuitem">
                     <svg class="h-5 w-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                     Waited
                 </button>
             </form>

             <form x-data :action="`{{ route('jadwal.update-status', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', id)" method="POST">
                 @csrf
                 <input type="hidden" name="status" value="Canceled">
                 <button type="submit" class="group flex items-center px-4 py-2 w-full text-left text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200" role="menuitem">
                     <svg class="h-5 w-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                     Canceled
                 </button>
             </form>
             
             <form x-data :action="`{{ route('jadwal.update-status', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', id)" method="POST">
                 @csrf
                 <input type="hidden" name="status" value="Finished">
                 <button type="submit" class="group flex items-center px-4 py-2 w-full text-left text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200" role="menuitem">
                     <svg class="h-5 w-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                     Finished
                 </button>
             </form>

             <a x-bind:href="`{{ route('jadwal.show', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', id)" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200" title="Detail" role="menuitem">
                 <svg class="h-5 w-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                 Details
             </a>
             <form x-data :action="`{{ route('jadwal.destroy', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', id)" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');" class="w-full">
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
            top: 0,
            left: 0,
            openDropdown(id, event) {
                this.id = id;
                const buttonRect = event.target.closest('button').getBoundingClientRect();
                
                // Menentukan posisi top dropdown
                this.top = buttonRect.bottom + window.scrollY + 5; 
                
                // Menentukan posisi left dropdown (sejajar dengan tombol)
                let dropdownLeft = buttonRect.left;
                const dropdownWidth = 192; // Lebar dropdown w-48 (~192px)

                // Sesuaikan posisi jika dropdown terlalu dekat ke tepi kanan layar
                if (dropdownLeft + dropdownWidth > window.innerWidth) {
                    dropdownLeft = window.innerWidth - dropdownWidth - 20; // Margin dari tepi kanan
                }
                
                this.left = dropdownLeft;
                this.open = true;
            }
        }
    }
</script>
@endsection