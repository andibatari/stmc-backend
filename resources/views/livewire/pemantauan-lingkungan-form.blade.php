@section('title', 'Tambah Data Pemantauan')

<div class="px-2 md:px-4 py-4 min-h-screen">
    <div class="mb-4">
        <a href="{{ route('pemantauan.index') }}" class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50">
            <i class="fas fa-arrow-left mr-1.5"></i> Kembali
        </a>
    </div>

    <form wire:submit.prevent="simpanPemantauan" class="space-y-4">
        
        {{-- KARTU 1: Dipersempit agar bagian atas form tidak terlalu panjang memakan tempat --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50 flex items-center">
                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                <h2 class="text-sm font-black text-slate-800">1. Setup Lokasi & NAB</h2>
            </div>

            <div class="p-4">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
                    <div class="lg:col-span-2">
                        @livewire('searchable-departemen', ['initialDepartemenId' => $departemens_id, 'initialUnitKerjaId' => $unit_kerjas_id], key('departemen-unit-picker'))
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Area (Sektor) <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.defer="area" class="w-full rounded-lg border-slate-200 bg-slate-50 text-xs p-2">
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    <div>
                        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Tanggal</label>
                        <input type="date" wire:model.defer="tanggal_pemantauan" class="w-full rounded-lg border-slate-200 text-xs p-2">
                    </div>
                    @foreach (['nabCahaya' => 'NAB Cahaya', 'nabBising' => 'NAB Bising', 'nabDebu' => 'NAB Debu', 'nabSuhu' => 'NAB Suhu'] as $model => $label)
                        <div>
                            <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">{{ $label }}</label>
                            <input type="{{ $model == 'nabDebu' ? 'text' : 'number' }}" step="0.01" wire:model.defer="{{ $model }}" class="w-full rounded-lg border-slate-200 text-xs p-2">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- KARTU 2: Array Input Form yang dinamis direduksi margin & paddingnya secara drastis --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
            <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-3">
                <h2 class="text-sm font-black text-slate-800"><i class="fas fa-map-marker-alt text-emerald-500 mr-2"></i> 2. Titik Pengukuran</h2>
                <button type="button" wire:click="addLokasi" class="px-3 py-1.5 bg-emerald-50 text-emerald-700 font-bold text-[10px] rounded-lg border border-emerald-200">
                    <i class="fas fa-plus mr-1"></i> Tambah Titik
                </button>
            </div>

            <div class="space-y-4">
                @foreach ($lokasiData as $index => $lokasiItem)
                {{-- Loop input data diminimalkan secara vertikal dengan menggunakan grid column rapat --}}
                <div class="bg-slate-50 rounded-xl border border-slate-200 p-3 relative">
                    
                    @if(count($lokasiData) > 1)
                        <button type="button" wire:click="removeLokasi({{ $index }})" class="absolute top-2 right-2 text-rose-400 hover:text-rose-600"><i class="fas fa-times-circle"></i></button>
                    @endif

                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-5 h-5 bg-slate-800 text-white rounded-full flex items-center justify-center text-[10px] font-black">{{ $index + 1 }}</span>
                        <div class="flex-1 max-w-sm">
                            <input type="text" wire:model.defer="lokasiData.{{ $index }}.lokasi" placeholder="Nama Titik Lokasi" class="w-full border-0 border-b border-slate-300 bg-transparent text-sm font-bold text-slate-800 focus:ring-0 p-0">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 md:grid-cols-5 lg:grid-cols-9 gap-2 mb-3">
                        @foreach (['cahaya'=>'Cahaya', 'bising'=>'Bising', 'debu'=>'Debu', 'suhu_basah'=>'S.Basah', 'suhu_kering'=>'S.Kering', 'suhu_radiasi'=>'S.Rad', 'isbb_indoor'=>'ISBB In', 'isbb_outdoor'=>'ISBB Out', 'rh'=>'RH%'] as $key => $label)
                            <div>
                                <label class="block text-[8px] font-bold text-slate-500 uppercase mb-0.5">{{ $label }}</label>
                                <input type="number" step="0.01" wire:model.defer="lokasiData.{{ $index }}.pemantauan.{{ $key }}" class="w-full rounded border-slate-200 text-[10px] p-1.5">
                            </div>
                        @endforeach
                    </div>

                    <div>
                        <input type="text" wire:model.defer="lokasiData.{{ $index }}.kesimpulan" placeholder="Kesimpulan Singkat / Rekomendasi (Opsional)" class="w-full rounded border-slate-200 text-[10px] p-2">
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Tombol submit diposisikan melayang (sticky) tapi tidak memakan banyak tempat tinggi --}}
        <div class="sticky bottom-4 z-40 bg-slate-800/95 backdrop-blur rounded-xl p-3 flex justify-between items-center shadow-lg">
            <div class="text-slate-300 text-[10px] hidden md:block">Cek kembali seluruh nilai sebelum menyimpan.</div>
            <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-red-600 text-white font-bold rounded-lg shadow-md text-xs hover:bg-red-700">
                <span wire:loading.remove wire:target="simpanPemantauan"><i class="fas fa-save mr-1.5"></i> Simpan Data</span>
                <span wire:loading wire:target="simpanPemantauan"><i class="fas fa-spinner fa-spin mr-1.5"></i> Menyimpan...</span>
            </button>
        </div>
    </form>
</div>