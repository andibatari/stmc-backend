@section('title', 'Tambah Data Pemantauan')

<div class="px-2 md:px-6 py-6 min-h-screen">
    {{-- Tombol Kembali --}}
    <div class="mb-6 lg:mb-8">
        <a href="{{ route('pemantauan.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-xl shadow-sm hover:bg-slate-50 hover:-translate-x-1 transition-all duration-200">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
        </a>
    </div>

    @if (session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl flex items-center shadow-sm mb-6 animate-fade-in">
            <i class="fas fa-check-circle text-xl mr-3"></i>
            <span class="font-bold text-sm">{{ session('message') }}</span>
        </div>
    @endif

    <form wire:submit.prevent="simpanPemantauan" class="space-y-6 lg:space-y-8">
        
        {{-- KARTU 1: INFO DASAR & NAB --}}
        <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
            <div class="px-6 md:px-10 py-6 border-b border-slate-100 bg-slate-50/50">
                <h2 class="text-xl font-black text-slate-800 flex items-center">
                    <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3"><i class="fas fa-info-circle text-sm"></i></div>
                    1. Setup Lokasi & Acuan NAB
                </h2>
                <p class="text-sm font-medium text-slate-500 mt-1 ml-11">Pilih departemen dan tentukan standar ambang batas.</p>
            </div>

            <div class="p-6 md:p-10 space-y-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        @livewire('searchable-departemen', ['initialDepartemenId' => $departemens_id, 'initialUnitKerjaId' => $unit_kerjas_id], key('departemen-unit-picker'))
                        @error('departemens_id') <span class="text-red-500 text-xs font-bold mt-1 block">Pilih Departemen terlebih dahulu.</span> @enderror
                        @error('unit_kerjas_id') <span class="text-red-500 text-xs font-bold mt-1 block">Pilih Unit Kerja terlebih dahulu.</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Area (Sektor) <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.defer="area" placeholder="Contoh: Area Kiln / Crusher" class="block w-full rounded-xl border border-slate-200 bg-slate-50 text-sm font-medium focus:bg-white focus:border-blue-500 focus:ring-blue-500 p-3.5 transition-colors">
                        @error('area') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="h-px bg-slate-100 w-full"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tanggal Pemantauan</label>
                        <input type="date" wire:model.defer="tanggal_pemantauan" class="block w-full rounded-xl border border-slate-200 bg-slate-50 text-sm font-bold focus:bg-white focus:border-blue-500 focus:ring-blue-500 p-3.5 transition-colors">
                        @error('tanggal_pemantauan') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    @foreach (['nabCahaya' => 'NAB Cahaya (Lux)', 'nabBising' => 'NAB Bising (dB)', 'nabDebu' => 'NAB Debu (mg/Nm3)', 'nabSuhu' => 'NAB Suhu (°C)'] as $model => $label)
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">{{ $label }}</label>
                            <input type="{{ $model == 'nabDebu' ? 'text' : 'number' }}" step="0.01" wire:model.defer="{{ $model }}" class="block w-full rounded-xl border border-slate-200 bg-white text-sm font-mono focus:border-blue-500 focus:ring-blue-500 p-3.5 shadow-sm">
                            @error($model) <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- KARTU 2: LOKASI DINAMIS --}}
        <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 p-6 md:p-10">
            <div class="flex justify-between items-end mb-8 border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-xl font-black text-slate-800 flex items-center">
                        <div class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center mr-3"><i class="fas fa-map-marker-alt text-sm"></i></div>
                        2. Titik Lokasi & Pengukuran Aktual
                    </h2>
                    <p class="text-sm font-medium text-slate-500 mt-1 ml-11">Input hasil pengukuran di lapangan.</p>
                </div>
                <button type="button" wire:click="addLokasi" class="hidden md:inline-flex items-center px-4 py-2 bg-emerald-50 text-emerald-700 font-bold text-sm rounded-xl border border-emerald-200 hover:bg-emerald-100 transition-colors">
                    <i class="fas fa-plus mr-2"></i> Tambah Titik
                </button>
            </div>

            <div class="space-y-6">
                @foreach ($lokasiData as $index => $lokasiItem)
                <div class="bg-slate-50 rounded-2xl border border-slate-200 p-6 relative group transition-all hover:border-slate-300 hover:shadow-sm">
                    
                    @if(count($lokasiData) > 1)
                        <button type="button" wire:click="removeLokasi({{ $index }})" class="absolute top-4 right-4 w-8 h-8 bg-white border border-slate-200 rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-500 hover:border-red-200 flex items-center justify-center transition-all shadow-sm" title="Hapus Titik Lokasi">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    @endif

                    <div class="flex items-center gap-3 mb-6 pr-10">
                        <span class="w-6 h-6 bg-slate-800 text-white rounded-full flex items-center justify-center text-xs font-black">{{ $index + 1 }}</span>
                        <div class="flex-1 max-w-sm">
                            <input type="text" wire:model.defer="lokasiData.{{ $index }}.lokasi" placeholder="Nama Lokasi Spesifik (Mis: Ruang Kontrol)" class="block w-full border-0 border-b-2 border-slate-200 bg-transparent text-lg font-bold text-slate-800 focus:ring-0 focus:border-red-500 placeholder-slate-300 p-0 transition-colors">
                            @error("lokasiData.{$index}.lokasi") <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
                        @foreach ([
                            'cahaya' => 'Cahaya (Lux)', 'bising' => 'Bising (dB)', 'debu' => 'Debu',
                            'suhu_basah' => 'Suhu Basah', 'suhu_kering' => 'Suhu Kering', 'suhu_radiasi' => 'Suhu Radiasi',
                            'isbb_indoor' => 'ISBB In', 'isbb_outdoor' => 'ISBB Out', 'rh' => 'RH (%)',
                        ] as $key => $label)
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ $label }}</label>
                                <input type="number" step="0.01" wire:model.defer="lokasiData.{{ $index }}.pemantauan.{{ $key }}" class="block w-full rounded-xl border border-slate-200 bg-white text-sm font-mono focus:border-blue-500 focus:ring-blue-500 p-2.5 shadow-sm transition-colors">
                            </div>
                        @endforeach
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kesimpulan & Rekomendasi</label>
                        <textarea wire:model.defer="lokasiData.{{ $index }}.kesimpulan" rows="2" placeholder="Catatan lapangan atau tindakan korektif..." class="block w-full rounded-xl border border-slate-200 bg-white text-sm focus:border-blue-500 focus:ring-blue-500 p-3 shadow-sm resize-none"></textarea>
                    </div>
                </div>
                @endforeach
            </div>
            
            <button type="button" wire:click="addLokasi" class="md:hidden mt-6 w-full flex items-center justify-center px-4 py-3 bg-emerald-50 text-emerald-700 font-bold text-sm rounded-xl border border-emerald-200 hover:bg-emerald-100">
                <i class="fas fa-plus mr-2"></i> Tambah Titik Lokasi
            </button>
        </div>

        {{-- ACTION BUTTONS STICKY --}}
        <div class="sticky bottom-4 lg:bottom-8 z-40 bg-slate-800/90 backdrop-blur-md rounded-2xl shadow-2xl p-4 flex flex-col sm:flex-row justify-between items-center gap-4 border border-slate-700">
            <div class="text-slate-300 text-sm font-medium hidden md:block">
                Pastikan semua nilai aktual dan batas NAB telah terisi dengan benar.
            </div>
            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-gradient-to-r from-red-600 to-red-700 text-white font-black tracking-wide rounded-xl shadow-lg hover:shadow-red-500/30 hover:-translate-y-0.5 transition-all text-sm">
                <span wire:loading.remove wire:target="simpanPemantauan"><i class="fas fa-save mr-2"></i> Simpan Laporan Pemantauan</span>
                <span wire:loading wire:target="simpanPemantauan"><i class="fas fa-circle-notch fa-spin mr-2"></i> Menyinkronkan Data...</span>
            </button>
        </div>
    </form>
</div>