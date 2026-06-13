<div class="space-y-6">
    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl flex items-center shadow-sm">
            <i class="fas fa-check-circle text-xl mr-3"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif
    
    <form wire:submit.prevent="simpanHasil" class="space-y-6">
        
        {{-- DOKTER PEMERIKSA --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-800"><i class="fas fa-user-md text-blue-500 mr-2"></i>Dokter Pemeriksa Poli Mata</h3>
            </div>
            <div class="p-6">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Dokter</label>
                <select wire:model.defer="dokterId" class="block w-full md:w-1/2 rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500">
                    <option value="">-- Pilih Dokter --</option>
                    @foreach ($listDokter as $dokter)
                        <option value="{{ $dokter->id }}">{{ $dokter->nama_lengkap ?? $dokter->name }}</option>
                    @endforeach
                </select>
                @error('dokterId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- FORM PEMERIKSAAN MATA (Sesuai Gambar Referensi) --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-800"><i class="fas fa-eye text-blue-500 mr-2"></i>Hasil Pemeriksaan Mata</h3>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-3xl">
                    {{-- VISUS --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2">Visus Kanan</label>
                        <input type="text" wire:model.defer="dataMata.visus_kanan" placeholder="Plano 6/6" class="w-full rounded-xl border-slate-200 bg-white text-sm focus:border-blue-500">
                        @error('dataMata.visus_kanan') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2">Visus Kiri</label>
                        <input type="text" wire:model.defer="dataMata.visus_kiri" placeholder="Plano 6/6" class="w-full rounded-xl border-slate-200 bg-white text-sm focus:border-blue-500">
                        @error('dataMata.visus_kiri') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    {{-- ADD & PD --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2">ADD</label>
                        <input type="text" wire:model.defer="dataMata.add" placeholder="+2.00" class="w-full rounded-xl border-slate-200 bg-white text-sm focus:border-blue-500">
                        @error('dataMata.add') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-2">PD (Pupillary Distance)</label>
                        <input type="text" wire:model.defer="dataMata.pd" placeholder="-/60" class="w-full rounded-xl border-slate-200 bg-white text-sm focus:border-blue-500">
                        @error('dataMata.pd') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- DIAGNOSA & KESIMPULAN --}}
        <div class="bg-amber-50 rounded-2xl border border-amber-200 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-amber-200/50 bg-amber-100/50">
                <h3 class="text-base font-bold text-amber-900">Kesimpulan Mata</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-amber-800 mb-2">Kesimpulan</label>
                    <textarea wire:model.defer="kesimpulan" rows="3" class="w-full rounded-xl border-amber-300 bg-white text-sm focus:border-amber-500 resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-amber-800 mb-2">Saran & Keterangan</label>
                    <textarea wire:model.defer="keterangan" rows="3" class="w-full rounded-xl border-amber-300 bg-white text-sm focus:border-amber-500 resize-none"></textarea>
                </div>
            </div>
        </div>

        {{-- TOMBOL --}}
        <div class="flex justify-end pt-4 border-t border-slate-200">
            <button type="submit" class="px-8 py-3 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl shadow-lg hover:-translate-y-0.5 transition-all">
                <span wire:loading.remove wire:target="simpanHasil"><i class="fas fa-save mr-2"></i> Simpan Pemeriksaan Mata</span>
                <span wire:loading wire:target="simpanHasil"><i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...</span>
            </button>
        </div>
    </form>
</div>