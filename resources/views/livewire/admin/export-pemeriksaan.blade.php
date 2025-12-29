<div class="space-y-6">
    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fa-solid fa-file-export mr-3 text-red-600"></i> Panel Ekspor Laporan Tahunan/Kolektif
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="space-y-1">
                <label class="text-xs font-bold text-gray-600 uppercase">Rentang Tanggal Awal</label>
                <input type="date" wire:model.live="date_start" class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 shadow-sm">
            </div>
            <div class="space-y-1">
                <label class="text-xs font-bold text-gray-600 uppercase">Rentang Tanggal Akhir</label>
                <input type="date" wire:model.live="date_end" class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 shadow-sm">
            </div>

            <div class="space-y-1">
                <label class="text-xs font-bold text-gray-600 uppercase">Departemen (Karyawan)</label>
                <select wire:model.live="departemens_id" class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 shadow-sm">
                    <option value="">-- Semua Departemen (Khusus Karyawan) --</option>
                    @foreach($listDepartemen as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-bold text-gray-600 uppercase">Kategori Peserta</label>
                <select wire:model.live="tipe_anggota" 
                    {{ $departemens_id ? 'disabled' : '' }}
                    class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 shadow-sm {{ $departemens_id ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($listKategori as $kat)
                        <option value="{{ $kat }}">{{ $kat }}</option>
                    @endforeach
                </select>
                @if($departemens_id)
                    <p class="text-[10px] text-red-500 italic mt-1">Kosongkan departemen untuk memfilter kategori non-karyawan.</p>
                @endif
            </div>

            <div class="space-y-1">
                <label class="text-xs font-bold text-gray-600 uppercase">Status Kehadiran</label>
                <select wire:model.live="status_kehadiran" class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 shadow-sm">
                    <option value="">-- Semua Status --</option>
                    @foreach($statusLabels as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-8 p-4 bg-gray-50 rounded-xl border border-dashed border-gray-300 flex flex-col md:flex-row items-center justify-between">
            <div class="mb-4 md:mb-0">
                <p class="text-sm text-gray-600">Ditemukan data sebanyak:</p>
                <h3 class="text-2xl font-black text-gray-800">{{ number_format($total_preview) }} <span class="text-lg font-normal text-gray-500">Record Pasien</span></h3>
            </div>
            
            <div class="flex space-x-3">
                <button wire:click="exportExcel" 
                    @if($total_preview == 0) disabled @endif
                    class="disabled:opacity-50 disabled:cursor-not-allowed bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg flex items-center shadow-lg transform active:scale-95 transition">
                    <i class="fa-solid fa-file-excel mr-2 text-xl"></i> Download Laporan (.xlsx)
                </button>
            </div>
        </div>

        <div class="mt-4 flex items-start text-amber-600 bg-amber-50 p-3 rounded-lg">
            <i class="fa-solid fa-circle-info mt-1 mr-3"></i>
            <p class="text-xs leading-relaxed">
                <strong>Informasi Ekspor:</strong> Laporan ini menyajikan data identitas, hasil pemeriksaan medis (resume), dan saran dokter. 
                Pemilihan <strong>"Departemen"</strong> (termasuk Semua Departemen) secara otomatis akan memfilter data <strong>Khusus Karyawan PTST</strong>. 
                Kosongkan pilihan departemen jika Anda ingin mengekspor kategori peserta lain seperti Istri, Suami, atau Non-Karyawan.
            </p>
        </div>
    </div>
</div>