@section('title', 'Manajemen Dokter')

<div class="px-2 md:px-6 py-6 min-h-screen">
    <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6 md:p-10 border border-slate-100 max-w-7xl mx-auto mb-8">
        
        <h2 class="text-2xl lg:text-3xl font-black text-slate-800 mb-8 border-b border-slate-100 pb-6 flex items-center">
            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mr-3"><i class="fas fa-user-md text-xl"></i></div>
            {{ $isEditing ? 'Edit Profil Dokter' : 'Registrasi Dokter Baru' }}
        </h2>

        @if (session()->has('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl flex items-center shadow-sm mb-6 animate-fade-in font-bold text-sm">
                <i class="fas fa-check-circle text-xl mr-3"></i> {{ session('success') }}
            </div>
        @endif

        <form wire:submit.prevent="{{ $isEditing ? 'update' : 'save' }}" class="space-y-6">
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-widest mb-1.5">Nama Lengkap & Gelar</label>
                        <input type="text" wire:model="nama_lengkap" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500">
                        @error('nama_lengkap') <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-widest mb-1.5">NIK (KTP)</label>
                        <input type="text" wire:model="nik" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 font-mono">
                        @error('nik') <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-widest mb-1.5">Spesialisasi</label>
                        <select wire:model="spesialisasi" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 cursor-pointer">
                            <option value="">Pilih Spesialisasi</option>
                            @foreach ($daftarSpesialisasi as $spesialisasi) <option value="{{ $spesialisasi }}">{{ $spesialisasi }}</option> @endforeach
                        </select>
                        @error('spesialisasi') <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-widest mb-1.5">Tanggal Lahir</label>
                        <input type="date" wire:model="tanggal_lahir" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-widest mb-1.5">Gol. Darah</label>
                        <select wire:model="golongan_darah" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500"><option value="">Pilih</option><option value="A">A</option><option value="B">B</option><option value="AB">AB</option><option value="O">O</option></select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-widest mb-1.5">No Handphone</label>
                        <input type="text" wire:model="no_hp" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-widest mb-1.5">Email Login</label>
                        <input type="email" wire:model="email" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500">
                    </div>
                    @if (!$isEditing)
                        <div class="md:col-span-2 lg:col-span-4 border-t border-slate-200 pt-4 mt-2">
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-widest mb-1.5">Password Akun Baru</label>
                            <input type="password" wire:model="password" class="block w-full md:w-1/2 lg:w-1/4 px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500">
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex justify-end pt-4">
                @if ($isEditing)
                    <button type="button" wire:click="cancelEdit" class="mr-3 px-6 py-3.5 bg-white border-2 border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition-all text-sm">Batal Edit</button>
                    <button type="submit" class="px-8 py-3.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl shadow-lg hover:-translate-y-0.5 transition-all text-sm">Update Data Dokter</button>
                @else
                    <button type="submit" class="px-8 py-3.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg hover:-translate-y-0.5 transition-all text-sm"><i class="fas fa-save mr-2"></i> Simpan Dokter</button>
                @endif
            </div>
        </form>
    </div>

    {{-- TABEL DOKTER --}}
    <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6 md:p-10 border border-slate-100 max-w-7xl mx-auto">
        <h4 class="text-xl font-black text-slate-800 mb-6 border-b border-slate-100 pb-4">Direktori Dokter MCU</h4>
        
        <div class="overflow-x-auto border border-slate-100 rounded-2xl hidden md:block">
            <table class="min-w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Identitas Dokter</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Kontak</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-center">Aksi Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($this->dokterUsers as $dokter)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-6 py-4">
                            <p class="font-black text-sm text-slate-800">{{ $dokter->nama_lengkap }}</p>
                            <p class="text-xs font-bold text-blue-600 mt-0.5">{{ $dokter->spesialisasi }} <span class="text-slate-400 font-normal">| NIK: {{ $dokter->nik }}</span></p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-slate-600"><i class="fas fa-envelope mr-1 opacity-50"></i> {{ $dokter->email }}</p>
                            <p class="text-sm font-medium text-slate-600 mt-0.5"><i class="fas fa-phone mr-1 opacity-50"></i> {{ $dokter->no_hp ?? '-' }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button wire:click="edit({{ $dokter->id }})" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm mr-2"><i class="fas fa-pen text-xs"></i></button>
                            <button wire:click="delete({{ $dokter->id }})" onclick="return confirm('Hapus dokter ini?')" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm"><i class="fas fa-trash text-xs"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-6 py-8 text-center text-slate-400 text-sm font-medium">Belum ada dokter yang terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Mobile Cards --}}
        <div class="md:hidden space-y-4">
            @foreach ($this->dokterUsers as $dokter)
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <p class="font-black text-slate-800">{{ $dokter->nama_lengkap }}</p>
                <p class="text-xs font-bold text-blue-600 mb-2">{{ $dokter->spesialisasi }}</p>
                <div class="text-xs text-slate-500 font-medium space-y-1 mb-4">
                    <p>NIK: {{ $dokter->nik }}</p>
                    <p>Email: {{ $dokter->email }}</p>
                </div>
                <div class="flex gap-2">
                    <button wire:click="edit({{ $dokter->id }})" class="flex-1 bg-white border border-slate-200 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Edit</button>
                    <button wire:click="delete({{ $dokter->id }})" onclick="return confirm('Hapus?')" class="flex-1 bg-red-50 text-red-600 border border-red-100 py-2 rounded-xl text-xs font-bold hover:bg-red-100">Hapus</button>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6 border-t border-slate-100 pt-4">{{ $this->dokterUsers->links() }}</div>
    </div>
</div>