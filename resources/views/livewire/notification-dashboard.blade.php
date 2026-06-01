<div x-data="{ activeTab: 'broadcast' }" class="max-w-7xl mx-auto">
    @section('title', 'Kontrol Notifikasi & Pengingat MCU')

    {{-- TOMBOL NAVIGASI TAB --}}
    <div class="mb-6 flex space-x-2 bg-white p-2 rounded-2xl shadow-sm border border-gray-100">
        <button @click="activeTab = 'broadcast'" 
            :class="activeTab === 'broadcast' ? 'bg-red-50 text-red-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 font-medium'"
            class="flex-1 py-3 px-4 rounded-xl transition-all duration-200 flex items-center justify-center">
            <i class="fa-solid fa-bullhorn mr-2" :class="activeTab === 'broadcast' ? 'text-red-500' : 'text-gray-400'"></i>
            Kirim Pengumuman Bebas
        </button>
        <button @click="activeTab = 'pengingat'" 
            :class="activeTab === 'pengingat' ? 'bg-blue-50 text-blue-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 font-medium'"
            class="flex-1 py-3 px-4 rounded-xl transition-all duration-200 flex items-center justify-center">
            <i class="fa-solid fa-calendar-check mr-2" :class="activeTab === 'pengingat' ? 'text-blue-500' : 'text-gray-400'"></i>
            Sistem Pengingat MCU
        </button>
    </div>

    {{-- GLOBAL ALERT --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 text-sm"><i class="fas fa-check-circle mr-1"></i> {{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-sm"><i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}</div>
    @endif

    {{-- ==================================================
         TAB 1: KIRIM PENGUMUMAN BEBAS (BROADCAST MESSAGE)
         ================================================== --}}
    <div x-show="activeTab === 'broadcast'" x-transition.opacity.duration.300ms class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
        <h3 class="text-lg sm:text-xl font-bold mb-2 flex items-center text-gray-800">
            <i class="fas fa-bullhorn text-red-600 mr-2"></i> Broadcast Pengumuman Aplikasi
        </h3>
        <p class="text-sm text-gray-500 mb-6 border-b pb-4">Kirim notifikasi atau pengumuman penting secara instan ke layar HP karyawan.</p>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Judul Pesan</label>
                    <input type="text" wire:model.defer="broadcastTitle" placeholder="Cth: Info Libur Klinik STMC" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-3">
                    @error('broadcastTitle') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Isi Pesan Lengkap</label>
                    <textarea wire:model.defer="broadcastMessage" rows="5" placeholder="Ketik pesan pengumuman..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-3"></textarea>
                    @error('broadcastMessage') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="space-y-5 bg-gray-50 p-6 rounded-xl border border-gray-100">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Target Penerima Notifikasi</label>
                    <select wire:model.live="broadcastTargetType" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-3 font-medium">
                        <option value="all">🔊 Kirim ke Semua Karyawan PTST</option>
                        <option value="dept">🏢 Kirim ke Departemen Tertentu</option>
                        <option value="individual">👤 Pilih Karyawan Secara Spesifik</option>
                    </select>
                </div>
                
                @if($broadcastTargetType === 'dept')
                <div class="animate-fade-in-down">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Pilih Departemen</label>
                    <select wire:model.defer="broadcastTargetDeptId" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-3">
                        <option value="">-- Pilih Departemen --</option>
                        @foreach($departemenOptions as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                        @endforeach
                    </select>
                    @error('broadcastTargetDeptId') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                @endif
                
                {{-- FITUR BARU: MULTI-SELECT LIVE SEARCH INDIVIDUAL --}}
                @if($broadcastTargetType === 'individual')
                <div class="animate-fade-in-down relative">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">Cari & Tambah Karyawan</label>
                    
                    {{-- Input Pencarian --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="searchEmployeeQuery" placeholder="Ketik nama atau No. SAP..." class="w-full pl-10 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm p-3">
                        
                        {{-- Loading Indicator --}}
                        <div wire:loading wire:target="searchEmployeeQuery" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-spinner fa-spin text-blue-500"></i>
                        </div>
                    </div>

                    {{-- Dropdown Hasil Pencarian --}}
                    @if(!empty($searchEmployeeQuery) && !empty($employeeSearchResults))
                    <ul class="absolute z-50 mt-1 w-full bg-white shadow-xl max-h-60 rounded-lg border border-gray-200 overflow-auto">
                        @foreach($employeeSearchResults as $emp)
                        <li wire:click="addEmployeeToBroadcast({{ $emp->id }}, '{{ addslashes($emp->nama_karyawan) }}', '{{ $emp->no_sap }}')" 
                            class="cursor-pointer hover:bg-blue-50 px-4 py-2 border-b border-gray-100 last:border-b-0 transition">
                            <div class="font-bold text-sm text-gray-800">{{ $emp->nama_karyawan }}</div>
                            <div class="text-xs text-gray-500">SAP: {{ $emp->no_sap }} | Dept: {{ $emp->departemen->nama_departemen ?? '-' }}</div>
                        </li>
                        @endforeach
                    </ul>
                    @elseif(!empty($searchEmployeeQuery) && empty($employeeSearchResults))
                    <ul class="absolute z-50 mt-1 w-full bg-white shadow-xl rounded-lg border border-gray-200">
                        <li class="px-4 py-3 text-sm text-gray-500 text-center">Karyawan tidak ditemukan.</li>
                    </ul>
                    @endif

                    {{-- Daftar Karyawan Terpilih (Chips/Badges) --}}
                    @if(count($selectedIndividualEmployees) > 0)
                    <div class="mt-4 border border-blue-100 bg-blue-50/50 p-3 rounded-xl">
                        <p class="text-[10px] font-bold text-blue-800 uppercase tracking-widest mb-2">Karyawan Terpilih ({{ count($selectedIndividualEmployees) }}):</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($selectedIndividualEmployees as $selected)
                            <span class="inline-flex items-center bg-white border border-blue-200 text-blue-800 text-xs font-semibold px-2.5 py-1.5 rounded-lg shadow-sm">
                                {{ $selected['name'] }} <span class="text-gray-400 font-normal ml-1">({{ $selected['sap'] }})</span>
                                <button type="button" wire:click="removeEmployeeFromBroadcast({{ $selected['id'] }})" class="ml-2 text-red-500 hover:text-red-700 focus:outline-none">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @error('selectedIndividualEmployees') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                @endif

                <div class="pt-4 border-t border-gray-200">
                    <button wire:click="sendBroadcast" wire:loading.attr="disabled" class="w-full flex items-center justify-center px-6 py-3 bg-red-600 rounded-xl font-bold text-white uppercase tracking-widest hover:bg-red-700 transition shadow-lg shadow-red-500/30">
                        <i class="fas fa-paper-plane mr-2"></i> <span wire:loading.remove wire:target="sendBroadcast">Kirim Notifikasi Sekarang</span>
                        <span wire:loading wire:target="sendBroadcast">Mengirim...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================================================
         TAB 2: PENGINGAT MCU MANUAL (KODE ASLI)
         ================================================== --}}
    <div x-show="activeTab === 'pengingat'" style="display: none;" x-transition.opacity.duration.300ms class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6 border border-gray-100">
        <h3 class="text-lg sm:text-xl font-bold mb-4 flex items-center">
            <i class="fas fa-calendar-check text-blue-600 mr-2"></i> Papan Pengingat MCU 
        </h3>
        <p class="text-sm text-gray-600 mb-4 border-b pb-4">Secara standar sistem mengirim pengingat otomatis tiap jam 8 pagi. Gunakan panel ini jika Anda butuh mengirim pengingat manual.</p>

        {{-- FILTER PENGINGAT MCU --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-start mb-6 border-b pb-4">
            <div class="md:col-span-1">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Mode Notifikasi</label>
                <select wire:model.live="notificationMode" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2">
                    <option value="scheduled">1. Pengingat Jadwal (Terdaftar)</option>
                    <option value="submission">2. Pengingat Pengajuan (Belum Terdaftar)</option>
                </select>
            </div>
            
            @if ($notificationMode === 'scheduled')
                <div class="md:col-span-1">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Target Tgl Jadwal</label>
                    <select wire:model.live="filterDate" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2">
                        <option value="today">Hari Ini</option>
                        <option value="tomorrow">Besok (Rekomendasi)</option>
                        <option value="specific">Tanggal Spesifik</option>
                    </select>
                </div>
                @if ($filterDate === 'specific')
                    <div class="md:col-span-1">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal MCU</label>
                        <input type="date" wire:model.live="specificDate" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm p-2">
                    </div>
                @else
                    <div class="md:col-span-1 hidden md:block"></div> 
                @endif
            @elseif ($notificationMode === 'submission')
                <div class="md:col-span-1">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Target Departemen</label>
                    <select wire:model.live="filterDepartemenId" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm p-2">
                        <option value="">-- Pilih Departemen --</option>
                        @foreach ($departemenOptions as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-1">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Batas Pengajuan (Opsional)</label>
                    <input type="date" wire:model.live="specificDate" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm p-2">
                </div>
            @endif
            
            <div class="md:col-span-2 text-right self-end w-full">
                <button wire:click="sendNotifications" wire:loading.attr="disabled" class="w-full px-6 py-2 bg-blue-600 rounded-lg font-semibold text-sm text-white hover:bg-blue-700 transition disabled:opacity-50 mt-4 md:mt-0"
                    @if($jadwalsToNotify->isEmpty() || ($notificationMode === 'submission' && !$filterDepartemenId)) disabled @endif>
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Manual ({{ $jadwalsToNotify->count() }} Orang)
                </button>
            </div>
        </div>

        {{-- SEARCH BAR --}}
        <div class="mb-6">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"><i class="fas fa-search text-gray-400"></i></div>
                <input type="text" wire:model.live="searchQuery" placeholder="Cari Nama Karyawan atau SAP..." class="block w-full pl-10 py-2.5 text-sm border-gray-300 rounded-lg">
            </div>
        </div>
        
        <h4 class="font-bold text-gray-800 mb-3">Daftar Penerima Notifikasi</h4>
        
        {{-- TABEL DESKTOP --}}
        <div class="overflow-x-auto bg-white border rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-center"><input type="checkbox" @if($jadwalsToNotify->isNotEmpty()) wire:click="$set('selectedRecipients', $selectedRecipients ? [] : $jadwalsToNotify->pluck('id')->toArray())" @endif @checked(count($selectedRecipients) === $jadwalsToNotify->count() && $jadwalsToNotify->count() > 0) class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-200"></th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Karyawan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Departemen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIK / SAP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($jadwalsToNotify as $data)
                        @php
                            $name = ($notificationMode === 'scheduled') ? ($data->patient->nama_lengkap ?? $data->patient->nama_karyawan ?? 'N/A') : $data->nama_karyawan;
                            $deptName = ($notificationMode === 'scheduled') ? ($data->patient->departemen->nama_departemen ?? 'N/A') : ($data->departemen->nama_departemen ?? 'N/A');
                            $nikSap = ($notificationMode === 'scheduled') ? ($data->patient->nik_karyawan ?? $data->patient->no_sap ?? $data->nik_pasien) : ($data->nik_karyawan ?? $data->no_sap);
                            $statusBadge = ($notificationMode === 'scheduled') ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800';
                            $statusText = ($notificationMode === 'scheduled') ? $data->status : 'Belum Ajukan Jadwal';
                        @endphp
                        <tr>
                            <td class="px-6 py-4 text-center"><input type="checkbox" wire:model.defer="selectedRecipients" value="{{ $data->id }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-200"></td>
                            <td class="px-6 py-4 text-sm font-medium">{{ $name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $deptName }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $nikSap }}</td>
                            <td class="px-6 py-4 text-sm"><span class="px-2 py-0.5 rounded-full {{ $statusBadge }}">{{ $statusText }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500 italic">Tidak ada karyawan yang cocok dengan filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>