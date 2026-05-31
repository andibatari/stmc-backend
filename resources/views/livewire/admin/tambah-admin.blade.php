<div> {{-- ROOT ELEMENT LIVEWIRE WAJIB DI SINI --}}
    @section('title', 'Manajemen Admin & Sistem')

    <div class="px-2 md:px-6 py-6 min-h-screen">
        <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6 md:p-10 border border-slate-100 max-w-7xl mx-auto mb-8">
            
            <h2 class="text-2xl lg:text-3xl font-black text-slate-800 mb-8 border-b border-slate-100 pb-6 flex items-center">
                <div class="w-10 h-10 bg-slate-800 text-white rounded-xl flex items-center justify-center mr-3"><i class="fas fa-shield-alt text-xl"></i></div>
                {{ $isEditing ? 'Edit Akun Administrator' : 'Registrasi Akun Sistem' }}
            </h2>

            @if (session()->has('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl flex items-center shadow-sm mb-6 animate-fade-in font-bold text-sm">
                    <i class="fas fa-check-circle text-xl mr-3"></i> {{ session('success') }}
                </div>
            @endif

            <form wire:submit.prevent="{{ $isEditing ? 'update' : 'save' }}" class="space-y-6">
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 relative z-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-widest mb-1.5">Pilih Role / Hak Akses</label>
                            <select wire:model.live="role" class="block w-full px-4 py-3 text-sm font-black rounded-xl border border-slate-200 bg-white focus:border-red-500 text-red-600 cursor-pointer shadow-sm">
                                <option value="admin">Administrator Root</option>
                                <option value="dokter">Dokter Pemeriksa</option>
                                <option value="karyawan">Karyawan PTST</option>
                            </select>
                        </div>

                        <div class="relative">
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-widest mb-1.5">Penautan Akun (Pilih Personel)</label>
                            
                            @if ($role === 'dokter')
                                <select wire:model.live="selectedDokterId" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 cursor-pointer shadow-sm">
                                    <option value="">-- Pilih Dokter Terdaftar --</option>
                                    @foreach ($listDokter as $id => $nama) <option value="{{ $id }}">{{ $nama }}</option> @endforeach
                                </select>
                            @elseif ($role === 'karyawan')
                                <div class="relative">
                                    <i class="fas fa-search absolute left-4 top-3.5 text-slate-400 text-sm"></i>
                                    <input type="text" wire:model.live.debounce.300ms="searchQuery" placeholder="Cari by SAP atau Nama..." class="block w-full pl-10 pr-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 shadow-sm">
                                </div>
                                
                                @if (!empty($searchQuery) && count($searchedKaryawans) > 0)
                                    <div class="absolute z-50 w-full bg-white border border-slate-100 mt-2 rounded-2xl shadow-[0_15px_40px_rgba(0,0,0,0.12)] max-h-60 overflow-y-auto overflow-hidden divide-y divide-slate-50">
                                        @foreach ($searchedKaryawans as $k)
                                            <p wire:click="selectKaryawan({{ $k->id }})" class="p-4 cursor-pointer hover:bg-slate-50 hover:text-red-600 text-sm font-bold text-slate-700 transition-colors">
                                                <span class="font-mono text-xs text-slate-400 mr-2">{{ $k->no_sap }}</span> {{ $k->nama_karyawan }}
                                            </p>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <input type="text" wire:model="nama_lengkap" placeholder="Ketik nama admin baru..." class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 shadow-sm">
                            @endif

                            @if ($selectedKaryawanId || $selectedDokterId)
                                 <p class="text-[10px] font-bold text-emerald-600 mt-2"><i class="fas fa-check-circle mr-1"></i> Data ditautkan: {{ $nama_lengkap }} ({{ $no_sap }})</p>
                            @endif
                            @error('nama_lengkap') <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-widest mb-1.5">No. SAP / ID Karyawan</label>
                            <input type="text" wire:model="no_sap" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 focus:border-red-500 font-mono shadow-sm {{ ($role !== 'admin' && ($selectedKaryawanId || $selectedDokterId)) ? 'bg-slate-100 text-slate-500 cursor-not-allowed border-transparent' : 'bg-white' }}" {{ ($role !== 'admin' && ($selectedKaryawanId || $selectedDokterId)) ? 'disabled' : '' }}>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-widest mb-1.5">Alamat Email (Untuk Login)</label>
                            <input type="email" wire:model="email" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 focus:border-red-500 shadow-sm {{ ($role !== 'admin') ? 'bg-slate-100 text-slate-500 cursor-not-allowed border-transparent' : 'bg-white' }}" {{ ($role !== 'admin') ? 'disabled' : '' }}>
                            @error('email') <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                        </div>

                        @if (!$isEditing)
                            <div class="md:col-span-2 border-t border-slate-200 pt-4 mt-2">
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-widest mb-1.5">Buat Password Default</label>
                                <input type="password" wire:model="password" class="block w-full md:w-1/2 px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 shadow-sm">
                                @error('password') <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    @if ($isEditing)
                        <button type="button" wire:click="cancelEdit" class="mr-3 px-6 py-3.5 bg-white border-2 border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition-all text-sm">Batal Edit</button>
                        <button type="submit" class="px-8 py-3.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl shadow-lg hover:-translate-y-0.5 transition-all text-sm">Update Akun</button>
                    @else
                        <button type="submit" class="px-8 py-3.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg hover:-translate-y-0.5 transition-all text-sm"><i class="fas fa-save mr-2"></i> Daftarkan Akun</button>
                    @endif
                </div>
            </form>
        </div>

        {{-- TABEL ADMIN --}}
        <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6 md:p-10 border border-slate-100 max-w-7xl mx-auto">
            <h4 class="text-xl font-black text-slate-800 mb-6 border-b border-slate-100 pb-4">Database Administrator</h4>
            
            <div class="overflow-x-auto border border-slate-100 rounded-2xl hidden md:block">
                <table class="min-w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Identitas Personel</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Login & Akses</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-center">Keamanan & Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse ($this->adminUsers as $admin)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-6 py-4">
                                <p class="font-black text-sm text-slate-800">{{ $admin->nama_lengkap }}</p>
                                <p class="text-xs font-bold text-slate-500 mt-0.5 font-mono">SAP: {{ $admin->no_sap ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-slate-600 mb-1"><i class="fas fa-envelope mr-1 opacity-50"></i> {{ $admin->email }}</p>
                                <span class="px-2 py-0.5 bg-red-50 text-red-600 font-black text-[10px] rounded uppercase border border-red-100">{{ $admin->role }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <button wire:click="editPassword({{ $admin->id }})" title="Ganti Password" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white transition-all shadow-sm"><i class="fas fa-key text-xs"></i></button>
                                    <button wire:click="edit({{ $admin->id }})" title="Edit" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm"><i class="fas fa-pen text-xs"></i></button>
                                    <button wire:click="delete({{ $admin->id }})" title="Hapus" onclick="return confirm('Hapus admin ini?')" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm"><i class="fas fa-trash text-xs"></i></button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="px-6 py-8 text-center text-slate-400 text-sm font-medium">Tidak ada data admin.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile View --}}
            <div class="md:hidden space-y-4">
                @foreach ($this->adminUsers as $admin)
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <p class="font-black text-slate-800">{{ $admin->nama_lengkap }} <span class="text-xs bg-red-50 text-red-600 px-1.5 py-0.5 rounded ml-1">{{ $admin->role }}</span></p>
                    <div class="text-xs text-slate-500 font-medium space-y-1 mb-4 mt-2">
                        <p><i class="fas fa-id-card w-4"></i> SAP: {{ $admin->no_sap ?? '-' }}</p>
                        <p><i class="fas fa-envelope w-4"></i> {{ $admin->email }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button wire:click="editPassword({{ $admin->id }})" class="flex-1 bg-white border border-slate-200 py-2 rounded-xl text-[11px] font-bold text-amber-600 hover:bg-amber-50"><i class="fas fa-key mr-1"></i> Pass</button>
                        <button wire:click="edit({{ $admin->id }})" class="flex-1 bg-white border border-slate-200 py-2 rounded-xl text-[11px] font-bold text-blue-600 hover:bg-blue-50"><i class="fas fa-pen mr-1"></i> Edit</button>
                        <button wire:click="delete({{ $admin->id }})" onclick="return confirm('Hapus?')" class="flex-1 bg-red-50 border border-red-100 py-2 rounded-xl text-[11px] font-bold text-red-600 hover:bg-red-100"><i class="fas fa-trash mr-1"></i> Del</button>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-6 border-t border-slate-100 pt-4">{{ $this->adminUsers->links() }}</div>
        </div>

        {{-- Modal Ganti Password Modern --}}
        @if ($editPasswordId)
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in">
                <div class="bg-white rounded-[2rem] shadow-2xl max-w-sm w-full p-8 text-center relative overflow-hidden">
                    <div class="w-16 h-16 bg-amber-100 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-4 border border-amber-200"><i class="fas fa-key text-2xl"></i></div>
                    <h3 class="text-xl font-black text-slate-800 mb-1">Ganti Password</h3>
                    <p class="text-xs text-slate-500 font-medium mb-6">Ubah kata sandi keamanan untuk admin ini.</p>
                    
                    <form wire:submit.prevent="updatePassword" class="text-left">
                        <div class="mb-6">
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-widest mb-1.5">Password Baru</label>
                            <input type="password" wire:model="newPassword" class="block w-full px-4 py-3 text-sm font-bold rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-amber-500 shadow-sm">
                            @error('newPassword') <p class="mt-1 text-[10px] font-bold text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex flex-col gap-2">
                            <button type="submit" class="w-full py-3 rounded-xl font-bold text-white bg-slate-800 hover:bg-slate-700 shadow-lg transition-all text-xs">Simpan Password Baru</button>
                            <button type="button" wire:click="$set('editPasswordId', null)" class="w-full py-3 rounded-xl font-bold text-slate-500 bg-white border border-slate-200 hover:bg-slate-50 transition-colors text-xs">Batalkan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <style>
        .animate-fade-in { animation: fadeIn 0.2s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    </style>
</div> {{-- AKHIR ROOT ELEMENT --}}