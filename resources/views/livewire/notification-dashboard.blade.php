{{-- Ganti seluruh konten di livewire/notification-dashboard.blade.php dengan ini --}}

<div class="bg-white rounded-xl shadow-lg p-6 mb-6 border border-gray-100">
    <h3 class="text-xl font-bold mb-4 flex items-center">
        <i class="fas fa-bell text-red-600 mr-2"></i> Dashboard Notifikasi Pengingat MCU
    </h3>
    <p class="text-gray-600 mb-4">Atur dan kirim pengingat jadwal MCU kepada karyawan melalui aplikasi mobile dan email.</p>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    {{-- KONTROL FILTER UTAMA --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end mb-6 border-b pb-4">
        
        {{-- KRITIS: Filter Mode Notifikasi --}}
        <div>
            <label for="notif_mode" class="block text-sm font-semibold text-gray-700 mb-1">Mode Notifikasi</label>
            <select id="notif_mode" wire:model.live="notificationMode" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 transition duration-150">
                <option value="scheduled">1. Pengingat Jadwal (Sudah Terdaftar)</option>
                <option value="submission">2. Pengingat Pengajuan (Belum Terdaftar)</option>
            </select>
        </div>
        
        {{-- Filter Target Tanggal Jadwal (HANYA untuk mode 'scheduled') --}}
        @if ($notificationMode === 'scheduled')
            <div>
                <label for="filter_date" class="block text-sm font-semibold text-gray-700 mb-1">Target Tgl Jadwal</label>
                <select id="filter_date" wire:model.live="filterDate" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 transition duration-150">
                    <option value="today">Hari Ini</option>
                    <option value="tomorrow">Besok (Rekomendasi)</option>
                    <option value="specific">Pilih Tanggal Spesifik</option>
                </select>
            </div>
            
            @if ($filterDate === 'specific')
                <div>
                    <label for="specific_date" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal MCU</label>
                    <input type="date" wire:model.live="specificDate" id="specific_date" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 transition duration-150">
                </div>
            @else
                <div></div> {{-- Filler --}}
            @endif

        {{-- Filter Departemen (HANYA untuk mode 'submission') --}}
        @elseif ($notificationMode === 'submission')
            <div>
                <label for="filter_departemen" class="block text-sm font-semibold text-gray-700 mb-1">Target Departemen</label>
                <select id="filter_departemen" wire:model.live="filterDepartemenId" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 transition duration-150">
                    <option value="">-- Pilih Departemen --</option>
                    @foreach ($departemenOptions as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                    @endforeach
                </select>
            </div>
            
            {{-- Input Tanggal Target Pengajuan (misal: Batas akhir pengajuan) --}}
            <div>
                <label for="specific_date" class="block text-sm font-semibold text-gray-700 mb-1">Tgl Batas Pengajuan (Ref)</label>
                <input type="date" wire:model.live="specificDate" id="specific_date" placeholder="Contoh: 26 Nov" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 transition duration-150">
            </div>
        @endif
        
        {{-- Tombol Kirim --}}
        <div class="md:col-span-{{ $notificationMode === 'scheduled' ? ($filterDate === 'specific' ? '2' : '2') : '2' }} text-right self-end">
            <button 
                wire:click="sendNotifications"
                wire:loading.attr="disabled"
                wire:target="sendNotifications, loadData"
                class="w-full inline-flex items-center justify-center px-6 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-red-700 transition ease-in-out duration-150 disabled:opacity-50"
                @if($jadwalsToNotify->isEmpty() || ($notificationMode === 'submission' && !$filterDepartemenId)) disabled @endif
            >
                <span wire:loading.remove wire:target="sendNotifications, loadData">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Pengingat ke {{ $jadwalsToNotify->count() }} Karyawan
                </span>
                <span wire:loading wire:target="sendNotifications, loadData">Memproses Notifikasi...</span>
            </button>
        </div>
    </div>

    {{-- INPUT PENCARIAN --}}
    <div class="mb-6">
        <label for="search" class="sr-only">Cari Karyawan</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" fill-rule="evenodd"></path></svg>
            </div>
            <input type="text" wire:model.live="searchQuery" id="search" placeholder="Cari Nama Karyawan, NIK, atau No. SAP..." 
                   class="block w-full pl-10 pr-4 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-red-500 focus:border-red-500">
        </div>
    </div>
    
    {{-- PRATINJAU JADWAL / KARYAWAN --}}
    <h4 class="text-lg font-bold mt-4 mb-3">Pratinjau Karyawan yang Ditargetkan ({{ $jadwalsToNotify->count() }} Total)</h4>
    
    <div class="overflow-x-auto bg-white border rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-center">
                        <input type="checkbox" 
                               @if($jadwalsToNotify->isNotEmpty())
                               wire:click="$set('selectedRecipients', $selectedRecipients ? [] : $jadwalsToNotify->pluck('id')->toArray())"
                               @endif
                               @checked(count($selectedRecipients) === $jadwalsToNotify->count() && $jadwalsToNotify->count() > 0)
                               class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Karyawan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departemen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK / No. SAP</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($jadwalsToNotify as $data)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                             {{-- ID yang digunakan adalah JadwalMcu ID untuk 'scheduled', dan Karyawan ID untuk 'submission' --}}
                             <input type="checkbox" wire:model.defer="selectedRecipients" value="{{ $data->id }}"
                                class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($notificationMode === 'scheduled')
                                {{ $data->patient->nama_lengkap ?? $data->patient->nama_karyawan ?? 'N/A' }}
                            @else
                                {{ $data->nama_karyawan }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{-- Tampilkan data departemen --}}
                            @if ($notificationMode === 'scheduled')
                                {{ $data->patient->departemen->nama_departemen ?? 'N/A' }}
                            @else
                                {{ $data->departemen->nama_departemen ?? 'N/A' }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($notificationMode === 'scheduled')
                                {{ $data->patient->nik_karyawan ?? $data->patient->no_sap ?? $data->nik_pasien }}
                            @else
                                {{ $data->nik_karyawan ?? $data->no_sap }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($notificationMode === 'scheduled')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $data->status }}
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Belum Ajukan Jadwal
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data karyawan yang cocok dengan filter ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>