<form wire:submit.prevent="save">
    <div class="space-y-8"> 

        {{-- 1. BAGIAN PENCARIAN PASIEN --}}
        <div class="bg-slate-50 p-6 md:p-8 rounded-2xl border border-slate-100 relative">
            <h4 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4"><i class="fas fa-search mr-2"></i> Langkah 1: Pilih Pasien</h4>
            
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-user-circle text-slate-400 text-lg"></i>
                </div>
                <input type="text" id="karyawan_search" wire:model.live.debounce.300ms="search" 
                    class="block w-full pl-12 pr-4 py-3.5 text-sm font-medium rounded-xl border border-slate-200 bg-white shadow-sm focus:border-red-500 focus:ring-red-500 placeholder-slate-400 transition-colors"
                    placeholder="Ketik Nama, NIK, atau No. SAP pasien di sini..." autocomplete="off">
                @error('karyawan_id') <p class="mt-2 text-xs font-bold text-red-500"><i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}</p> @enderror

                {{-- Hasil Pencarian Dropdown --}}
                @if (!empty($results))
                    <div class="absolute z-50 w-full mt-2 bg-white border border-slate-100 rounded-2xl shadow-[0_15px_40px_rgba(0,0,0,0.12)] max-h-64 overflow-y-auto overflow-hidden divide-y divide-slate-50">
                        @foreach ($results as $item)
                            <div wire:click="selectPatient({{ $item['id'] }}, '{{ $item['search_type'] }}')"
                                class="px-5 py-3 cursor-pointer hover:bg-slate-50 transition-colors duration-150 group">
                                <p class="font-bold text-sm text-slate-800 group-hover:text-red-600 transition-colors">{{ $item['search_name'] }}</p>
                                <p class="text-xs font-medium text-slate-500 mt-0.5">
                                    NIK: <span class="font-mono">{{ $item['search_nik'] }}</span> | SAP: <span class="font-mono">{{ $item['no_sap'] ?? '-' }}</span>
                                    @if ($item['search_type'] == 'peserta_mcu')
                                        <span class="ml-2 inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100">Umum / Non-PTST</span>
                                    @else
                                        <span class="ml-2 inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">Karyawan</span>
                                    @endif
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- 2. BAGIAN DETAIL PASIEN (Muncul jika dipilih) --}}
        @if ($selectedKaryawan)
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm animate-fade-in">
                <h4 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4"><i class="fas fa-id-card mr-2"></i> Profil Pasien Terpilih</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4"> 
                    @php
                        $details = [];
                        if ($patientType === 'karyawan') {
                            $details = [
                                'NO SAP' => ['value' => $selectedKaryawan->no_sap, 'icon' => 'fa-hashtag'],
                                'NAMA LENGKAP' => ['value' => $selectedKaryawan->nama_karyawan, 'icon' => 'fa-user'],
                                'UNIT KERJA' => ['value' => $selectedKaryawan->unitKerja->nama_unit_kerja ?? 'N/A', 'icon' => 'fa-building'],
                                'JABATAN' => ['value' => $selectedKaryawan->jabatan ?? 'N/A', 'icon' => 'fa-briefcase'],
                            ];
                        } elseif ($patientType === 'peserta_mcu') {
                             $details = [
                                'NAMA LENGKAP' => ['value' => $selectedKaryawan->nama_lengkap, 'icon' => 'fa-user'],
                                'NIK' => ['value' => $selectedKaryawan->nik_pasien ?? 'N/A', 'icon' => 'fa-id-badge'],
                                'PERUSAHAAN' => ['value' => $selectedKaryawan->perusahaan_asal ?? 'N/A', 'icon' => 'fa-building'],
                                'TIPE PASIEN' => ['value' => $selectedKaryawan->tipe_anggota ?? 'N/A', 'icon' => 'fa-users'],
                            ];
                        }
                    @endphp

                    @foreach ($details as $label => $data)
                        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100 flex items-start gap-3"> 
                            <div class="bg-white p-2 rounded-lg text-slate-400 shadow-sm"><i class="fas {{ $data['icon'] }} w-4 text-center"></i></div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $label }}</p>
                                <p class="font-bold text-sm text-slate-800 mt-0.5 truncate">{{ $data['value'] ?? '-' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- 3. PENGATURAN JADWAL MCU --}}
        <div class="bg-white p-6 md:p-8 rounded-2xl border border-slate-200 shadow-sm">
            <h4 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-5"><i class="fas fa-calendar-check mr-2"></i> Langkah 2: Pengaturan Jadwal & Layanan</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5"> 
                <div>
                    <label for="tanggal_mcu" class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal MCU <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_mcu" id="tanggal_mcu" wire:model="tanggal_mcu" required
                        class="block w-full px-4 py-3 text-sm font-semibold rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-red-500 focus:ring-red-500 transition-colors">
                    @error('tanggal_mcu') <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label for="dokter_id" class="block text-xs font-bold text-slate-700 mb-1.5">Dokter Penanggung Jawab <span class="text-red-500">*</span></label>
                    <select id="dokter_id" wire:model="dokter_id" required
                        class="block w-full px-4 py-3 text-sm font-semibold rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-red-500 focus:ring-red-500 transition-colors cursor-pointer">
                        <option value="">-- Pilih Dokter --</option>
                        @foreach ($daftarDokter as $dokter)
                            <option value="{{ $dokter->id }}">{{ $dokter->nama_lengkap }} - {{ $dokter->spesialisasi }}</option>
                        @endforeach
                    </select>
                    @error('dokter_id') <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label for="paket_mcus_id" class="block text-xs font-bold text-slate-700 mb-1.5">Paket Pemeriksaan <span class="text-red-500">*</span></label>
                    <select id="paket_mcus_id" wire:model="paket_mcus_id" required
                        class="block w-full px-4 py-3 text-sm font-semibold rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-red-500 focus:ring-red-500 transition-colors cursor-pointer">
                        <option value="">-- Pilih Paket MCU --</option>
                        @foreach ($daftarPaket as $paket)
                            <option value="{{ $paket->id }}">{{ $paket->nama_paket }}</option>
                        @endforeach
                    </select>
                    @error('paket_mcus_id') <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Tombol Simpan --}}
        <div class="flex justify-end mt-8 border-t border-slate-100 pt-6"> 
            <button type="submit" wire:loading.attr="disabled"
                class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 text-sm">
                <span wire:loading.remove><i class="fas fa-save mr-2"></i> {{ $isEditMode ? 'Simpan Perubahan Jadwal' : 'Buat Jadwal MCU Sekarang' }}</span>
                <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> Sedang Menyimpan...</span>
            </button>
        </div>
    </div>

    <style>
        .animate-fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</form>