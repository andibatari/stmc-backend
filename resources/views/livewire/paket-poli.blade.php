<div> {{-- ROOT ELEMENT LIVEWIRE: Semua elemen WAJIB berada di dalam div ini --}}
    @section('title', 'Kelola Paket & Poli MCU')

    <div class="px-2 md:px-6 py-6 min-h-screen">
        <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6 md:p-10 border border-slate-100 max-w-7xl mx-auto">
            <h1 class="text-2xl lg:text-3xl font-black text-slate-800 mb-8 border-b border-slate-100 pb-6 flex items-center">
                <div class="w-10 h-10 bg-red-100 text-red-600 rounded-xl flex items-center justify-center mr-3"><i class="fas fa-cubes text-xl"></i></div>
                Pengaturan Master Paket & Poli
            </h1>

            {{-- Custom Pill Tabs --}}
            <div class="flex p-1.5 mb-8 bg-slate-100 rounded-2xl w-full sm:w-max mx-auto sm:mx-0 overflow-x-auto shadow-inner">
                <button wire:click="$set('activeTab', 'paket')" class="flex-1 sm:flex-none px-6 py-2.5 text-sm font-bold rounded-xl transition-all duration-200 whitespace-nowrap {{ $activeTab === 'paket' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Manajemen Paket</button>
                <button wire:click="$set('activeTab', 'poli')" class="flex-1 sm:flex-none px-6 py-2.5 text-sm font-bold rounded-xl transition-all duration-200 whitespace-nowrap {{ $activeTab === 'poli' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Daftar Poli</button>
                <button wire:click="$set('activeTab', 'hubungkan')" class="flex-1 sm:flex-none px-6 py-2.5 text-sm font-bold rounded-xl transition-all duration-200 whitespace-nowrap {{ $activeTab === 'hubungkan' ? 'bg-red-600 text-white shadow-sm shadow-red-200' : 'text-slate-500 hover:text-slate-700' }}">Hubungkan Modul</button>
            </div>

            <div>
                @if ($activeTab === 'paket')
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-1">
                            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 sticky top-6">
                                <h3 class="font-bold text-slate-700 mb-4 flex items-center"><i class="fas fa-plus-circle text-emerald-500 mr-2"></i> Tambah Paket Baru</h3>
                                <form wire:submit.prevent="savePaket" class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Nama Paket</label>
                                        <input type="text" wire:model="nama_paket" class="block w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:border-red-500 focus:ring-red-500" placeholder="Ketik nama paket...">
                                        @error('nama_paket') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <button type="submit" class="w-full px-6 py-3 bg-slate-800 text-white rounded-xl font-bold hover:bg-slate-700 transition-all text-sm shadow-lg hover:-translate-y-0.5">Simpan Paket</button>
                                </form>
                            </div>
                        </div>
                        <div class="lg:col-span-2">
                            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                                <table class="min-w-full text-left">
                                    <thead class="bg-slate-50 border-b border-slate-100">
                                        <tr>
                                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Paket Tersedia</th>
                                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50">
                                        @forelse ($paketList as $paket)
                                            <tr class="hover:bg-slate-50/50">
                                                <td class="px-6 py-4 text-sm font-black text-slate-800">{{ $paket->nama_paket }}</td>
                                                <td class="px-6 py-4 text-right">
                                                    <button wire:click="deletePaket({{ $paket->id }})" wire:confirm="Hapus paket ini?" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all shadow-sm ml-auto"><i class="fas fa-trash text-xs"></i></button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="2" class="px-6 py-8 text-center text-sm text-slate-400 font-medium">Belum ada paket yang terdaftar.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                @elseif ($activeTab === 'poli')
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-1">
                            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 sticky top-6">
                                <h3 class="font-bold text-slate-700 mb-4 flex items-center"><i class="fas fa-plus-circle text-emerald-500 mr-2"></i> Tambah Poli Baru</h3>
                                <form wire:submit.prevent="savePoli" class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Nama Poli / Ruangan</label>
                                        <input type="text" wire:model="nama_poli" class="block w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:border-red-500 focus:ring-red-500" placeholder="Ketik nama poli...">
                                        @error('nama_poli') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <button type="submit" class="w-full px-6 py-3 bg-slate-800 text-white rounded-xl font-bold hover:bg-slate-700 transition-all text-sm shadow-lg hover:-translate-y-0.5">Simpan Poli</button>
                                </form>
                            </div>
                        </div>
                        <div class="lg:col-span-2">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @forelse ($poliList as $poli)
                                    <div class="bg-white border border-slate-200 rounded-2xl p-4 flex justify-between items-center shadow-sm hover:border-blue-200 hover:shadow-md transition-all group">
                                        <span class="text-sm font-black text-slate-800"><i class="fas fa-door-open text-slate-300 mr-2 group-hover:text-blue-500 transition-colors"></i> {{ $poli->nama_poli }}</span>
                                        <button wire:click="deletePoli({{ $poli->id }})" wire:confirm="Hapus poli ini?" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all"><i class="fas fa-trash text-xs"></i></button>
                                    </div>
                                @empty
                                    <div class="col-span-full text-center text-slate-400 font-medium py-10 bg-slate-50 rounded-2xl border border-slate-100">Belum ada poli terdaftar.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                @elseif ($activeTab === 'hubungkan')
                    <div class="bg-slate-50 p-6 md:p-8 rounded-2xl border border-slate-100 mb-8">
                        <form wire:submit.prevent="attachPoliToPaket" class="flex flex-col lg:flex-row gap-6 items-start">
                            <div class="w-full lg:w-1/3 shrink-0">
                                <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-widest"><i class="fas fa-box-open mr-1"></i> Pilih Paket Utama</label>
                                <select wire:model="paket_mcus_id" class="block w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm font-black focus:border-red-500 focus:ring-red-500 cursor-pointer shadow-sm">
                                    <option value="">-- Tentukan Paket MCU --</option>
                                    @foreach ($daftarPaket as $paket) <option value="{{ $paket->id }}">{{ $paket->nama_paket }}</option> @endforeach
                                </select>
                                @error('paket_mcus_id') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="w-full flex-1">
                                <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-widest"><i class="fas fa-list-check mr-1"></i> Checklist Modul Poli</label>
                                <div class="bg-white p-4 rounded-xl border border-slate-200 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 shadow-sm max-h-48 overflow-y-auto custom-scrollbar">
                                    @forelse ($daftarPoli as $poli)
                                        <label class="flex items-center p-2 rounded-lg hover:bg-slate-50 cursor-pointer border border-transparent hover:border-slate-100 transition-colors">
                                            <input type="checkbox" wire:model="poli_ids" value="{{ $poli->id }}" class="w-5 h-5 text-red-600 border-slate-300 rounded focus:ring-red-500 cursor-pointer">
                                            <span class="ml-3 text-sm font-bold text-slate-700">{{ $poli->nama_poli }}</span>
                                        </label>
                                    @empty
                                        <p class="text-sm text-slate-400 italic col-span-full p-2">Poli tidak tersedia.</p>
                                    @endforelse
                                </div>
                                @error('poli_ids') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="w-full lg:w-auto self-end lg:mt-6">
                                <button type="submit" class="w-full px-8 py-3.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold shadow-lg hover:-translate-y-0.5 transition-all text-sm whitespace-nowrap">
                                    <i class="fas fa-link mr-2"></i> Tautkan Modul
                                </button>
                            </div>
                        </form>
                    </div>

                    <h3 class="text-lg font-black text-slate-800 mb-4 ml-2">Pemetaan Paket Terdaftar</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @forelse ($daftarPaket as $paket)
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 relative overflow-hidden group hover:shadow-md transition-shadow">
                                <div class="absolute top-0 right-0 w-16 h-16 bg-red-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                                <h4 class="font-black text-lg text-slate-800 mb-4 border-b border-slate-100 pb-3 relative z-10">{{ $paket->nama_paket }}</h4>
                                <div class="flex flex-wrap gap-2 relative z-10">
                                    @forelse ($paket->poli as $poli)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                            {{ $poli->nama_poli }}
                                            <button wire:click="detachPoliFromPaket({{ $paket->id }}, {{ $poli->id }})" wire:confirm="Lepas poli ini dari paket?" class="ml-2 w-4 h-4 rounded bg-white border border-slate-200 text-red-500 hover:bg-red-500 hover:text-white hover:border-red-500 flex items-center justify-center transition-colors">
                                                <i class="fas fa-times" style="font-size: 8px;"></i>
                                            </button>
                                        </span>
                                    @empty
                                        <span class="text-xs text-slate-400 font-medium italic bg-slate-50 px-3 py-1.5 rounded-lg">Kosong (Tidak ada poli)</span>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center text-slate-400 py-8 bg-slate-50 rounded-2xl border border-slate-100">Belum ada pemetaan data.</div>
                        @endforelse
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- SCRIPTS & STYLES (Di dalam root div) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @livewireScripts
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('dataSaved', (event) => {
                const message = event[0]?.message || event.message;
                Swal.fire({ title: 'Berhasil! 🎉', text: message, icon: 'success', confirmButtonText: 'Tutup', confirmButtonColor: '#dc2626', customClass: { popup: 'rounded-[2rem]' }});
            });
            
            Livewire.on('dataGagal', (event) => {
                const message = event[0]?.message || event.message;
                Swal.fire({ title: 'Gagal! 😟', text: message, icon: 'error', confirmButtonText: 'Tutup', confirmButtonColor: '#dc2626', customClass: { popup: 'rounded-[2rem]' }});
            });
            
            Livewire.on('confirmDelete', (event) => {
                const data = event[0] || event;
                Swal.fire({
                    title: 'Konfirmasi Penghapusan ⚠️',
                    text: data.message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    customClass: { popup: 'rounded-[2rem]' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        if(typeof Livewire.emit === 'function') {
                            Livewire.emit(data.method, ...(data.params || []));
                        } else {
                            Livewire.dispatch(data.method, data.params);
                        }
                    }
                });
            });
        });
    </script>
    <style>.custom-scrollbar::-webkit-scrollbar{width:6px}.custom-scrollbar::-webkit-scrollbar-thumb{background-color:#cbd5e1;border-radius:10px}</style>
</div>