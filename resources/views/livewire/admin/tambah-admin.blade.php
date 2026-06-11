<div> 
    @section('title', 'Manajemen Admin & Sistem')

    <div class="px-3 md:px-6 py-4 md:py-6 min-h-screen">
        <div class="bg-white rounded-2xl md:rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-5 md:p-10 border border-slate-100 max-w-7xl mx-auto mb-6 md:mb-8">
            
            <h2 class="text-xl md:text-2xl lg:text-3xl font-black text-slate-800 mb-6 md:mb-8 border-b border-slate-100 pb-4 md:pb-6 flex items-center">
                <div class="w-8 h-8 md:w-10 md:h-10 bg-slate-800 text-white rounded-lg md:rounded-xl flex items-center justify-center mr-3 shrink-0"><i class="fas fa-shield-alt text-lg md:text-xl"></i></div>
                {{ $isEditing ? 'Edit Akun Administrator' : 'Registrasi Akun Sistem' }}
            </h2>

            @if (session()->has('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 md:px-5 py-3 md:py-4 rounded-xl md:rounded-2xl flex items-center shadow-sm mb-6 animate-fade-in font-bold text-xs md:text-sm">
                    <i class="fas fa-check-circle text-lg md:text-xl mr-3"></i> {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 md:px-5 py-3 md:py-4 rounded-xl md:rounded-2xl flex items-center shadow-sm mb-6 animate-fade-in font-bold text-xs md:text-sm">
                    <i class="fas fa-exclamation-circle text-lg md:text-xl mr-3"></i> {{ session('error') }}
                </div>
            @endif

            <form wire:submit.prevent="{{ $isEditing ? 'update' : 'save' }}" class="space-y-4 md:space-y-6">
                <div class="bg-slate-50 p-5 md:p-6 rounded-2xl md:rounded-[2rem] border border-slate-100 relative z-20 shadow-inner">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
                        
                        {{-- 🌟 1. DROPDOWN SUMBER PERSONEL --}}
                        <div>
                            <label class="block text-[10px] md:text-xs font-bold text-slate-600 uppercase tracking-widest mb-1.5">Sumber Data Personel</label>
                            <select wire:model.live="tipe_personel" class="block w-full px-4 py-3.5 md:py-3 text-sm font-black rounded-xl border border-slate-200 bg-white focus:border-red-500 text-slate-700 cursor-pointer shadow-sm" {{ $isEditing ? 'disabled' : '' }}>
                                <option value="manual">Admin Luar (Ketik Manual)</option>
                                <option value="karyawan">Karyawan PT. Semen Tonasa</option>
                                <option value="dokter">Dokter Pemeriksa (Klinik)</option>
                            </select>
                        </div>

                        {{-- 🌟 2. DROPDOWN HAK AKSES/ROLE --}}
                        <div>
                            <label class="block text-[10px] md:text-xs font-bold text-slate-600 uppercase tracking-widest mb-1.5">Hak Akses Sistem (Role)</label>
                            <select wire:model.live="role" class="block w-full px-4 py-3.5 md:py-3 text-sm font-black rounded-xl border border-slate-200 bg-white focus:border-red-500 text-red-600 cursor-pointer shadow-sm">
                                <option value="superadmin">Superadmin (Akses Penuh)</option>
                                <option value="admin">Administrator Biasa</option>
                                <option value="dokter">Dokter</option>
                                <option value="karyawan">Karyawan</option>
                            </select>
                        </div>

                        {{-- 🌟 3. BLOK PENCARIAN/INPUT (Dinamis berubah lebar menyesuaikan tipe) --}}
                        <div class="relative {{ $tipe_personel === 'manual' ? '' : 'md:col-span-2' }}">
                            <label class="block text-[10px] md:text-xs font-bold text-slate-600 uppercase tracking-widest mb-1.5">
                                {{ $tipe_personel === 'manual' ? 'Nama Lengkap' : 'Pencarian & Penautan Akun' }}
                            </label>
                            
                            @if ($tipe_personel === 'dokter')
                                <select wire:model.live="selectedDokterId" class="block w-full px-4 py-3.5 md:py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 cursor-pointer shadow-sm" {{ $isEditing ? 'disabled' : '' }}>
                                    <option value="">-- Pilih Dokter Terdaftar --</option>
                                    @foreach ($listDokter as $id => $nama) <option value="{{ $id }}">{{ $nama }}</option> @endforeach
                                </select>
                            @elseif ($tipe_personel === 'karyawan')
                                <div class="relative">
                                    <i class="fas fa-search absolute left-4 top-4 md:top-3.5 text-slate-400 text-sm"></i>
                                    <input type="text" wire:model.live.debounce.300ms="searchQuery" placeholder="Cari Karyawan by NIK, SAP, atau Nama..." class="block w-full pl-10 pr-4 py-3.5 md:py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 shadow-sm" {{ $isEditing ? 'disabled' : '' }}>
                                </div>
                                
                                @if (!empty($searchQuery) && count($searchedKaryawans) > 0 && !$karyawanFound)
                                    <div class="absolute z-[100] w-full bg-white border border-slate-100 mt-2 rounded-2xl shadow-[0_15px_40px_rgba(0,0,0,0.12)] max-h-60 overflow-y-auto overflow-hidden divide-y divide-slate-50">
                                        @foreach ($searchedKaryawans as $k)
                                            <p wire:click="selectKaryawan({{ $k->id }})" class="p-4 cursor-pointer hover:bg-slate-50 hover:text-red-600 text-sm font-bold text-slate-700 transition-colors">
                                                <span class="font-mono text-xs text-slate-400 mr-2">{{ $k->no_sap }}</span> {{ $k->nama_karyawan }}
                                            </p>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <input type="text" wire:model="nama_lengkap" placeholder="Ketik nama admin baru..." class="block w-full px-4 py-3.5 md:py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 shadow-sm">
                            @endif

                            @if ($karyawanFound)
                                 <p class="text-[10px] md:text-xs font-bold text-emerald-600 mt-2"><i class="fas fa-check-circle mr-1"></i> Data ditautkan: {{ $nama_lengkap }} ({{ $no_sap ?? 'Tidak Ada SAP' }})</p>
                            @endif
                            @error('nama_lengkap') <p class="mt-1 text-[10px] md:text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- 4. KOLOM SISA --}}
                        <div>
                            <label class="block text-[10px] md:text-xs font-bold text-slate-600 uppercase tracking-widest mb-1.5">No. SAP / ID Karyawan</label>
                            <input type="text" wire:model="no_sap" class="block w-full px-4 py-3.5 md:py-3 text-sm font-medium rounded-xl border border-slate-200 focus:border-red-500 font-mono shadow-sm {{ ($tipe_personel !== 'manual' && $karyawanFound) ? 'bg-slate-100 text-slate-500 cursor-not-allowed border-transparent' : 'bg-white' }}" {{ ($tipe_personel !== 'manual' && $karyawanFound) ? 'disabled' : '' }}>
                            @error('no_sap') <p class="mt-1 text-[10px] md:text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-[10px] md:text-xs font-bold text-slate-600 uppercase tracking-widest mb-1.5">Alamat Email (Login)</label>
                            <input type="email" wire:model="email" class="block w-full px-4 py-3.5 md:py-3 text-sm font-medium rounded-xl border border-slate-200 focus:border-red-500 shadow-sm {{ ($tipe_personel !== 'manual' && $karyawanFound) ? 'bg-slate-100 text-slate-500 cursor-not-allowed border-transparent' : 'bg-white' }}" {{ ($tipe_personel !== 'manual' && $karyawanFound) ? 'disabled' : '' }}>
                            @error('email') <p class="mt-1 text-[10px] md:text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                        </div>

                        @if (!$isEditing)
                            <div class="md:col-span-2 border-t border-slate-200 pt-4 mt-2">
                                <label class="block text-[10px] md:text-xs font-bold text-slate-600 uppercase tracking-widest mb-1.5">Buat Password Default</label>
                                <div class="relative w-full md:w-1/2">
                                    <input type="password" id="pass_add_admin" wire:model="password" class="block w-full px-4 py-3.5 md:py-3 pr-10 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 shadow-sm transition-all">
                                    <button type="button" onclick="togglePasswordVisibility('pass_add_admin', 'eyeOpen_aa', 'eyeClosed_aa')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-red-600 focus:outline-none">
                                        <img id="eyeOpen_aa" src="{{ asset('images/eye-open.png') }}" class="h-4 w-4 opacity-70">
                                        <img id="eyeClosed_aa" src="{{ asset('images/eye-closed.png') }}" class="h-4 w-4 hidden opacity-70">
                                    </button>
                                </div>
                                @error('password') <p class="mt-1 text-[10px] md:text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Tombol Responsif --}}
                <div class="flex flex-col-reverse sm:flex-row justify-end pt-2 md:pt-4 gap-3">
                    @if ($isEditing)
                        <button type="button" wire:click="cancelEdit" class="w-full sm:w-auto px-6 py-4 md:py-3.5 bg-white border border-slate-300 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition-all text-sm">Batal Edit</button>
                        <button type="submit" class="w-full sm:w-auto px-8 py-4 md:py-3.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl shadow-lg hover:-translate-y-0.5 transition-all text-sm">Update Akun</button>
                    @else
                        <button type="submit" class="w-full sm:w-auto px-8 py-4 md:py-3.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg shadow-red-500/30 hover:-translate-y-0.5 transition-all text-sm"><i class="fas fa-save mr-2"></i> Daftarkan Akun</button>
                    @endif
                </div>
            </form>
        </div>

        {{-- TABEL ADMIN --}}
        <div class="bg-white rounded-2xl md:rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-5 md:p-10 border border-slate-100 max-w-7xl mx-auto">
            <h4 class="text-lg md:text-xl font-black text-slate-800 mb-5 md:mb-6 border-b border-slate-100 pb-4">Database Akun Sistem</h4>
            
            <div class="hidden md:block overflow-x-auto border border-slate-100 rounded-2xl">
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
                                
                                {{-- Warna Indikator Superadmin Baru --}}
                                <span class="px-2 py-0.5 font-black text-[10px] rounded uppercase border 
                                    {{ $admin->role === 'superadmin' ? 'bg-purple-50 text-purple-600 border-purple-100' : 
                                      ($admin->role === 'admin' ? 'bg-red-50 text-red-600 border-red-100' : 
                                      ($admin->role === 'dokter' ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100')) }}">
                                    {{ $admin->role }}
                                </span>
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
                        <tr><td colspan="3" class="px-6 py-8 text-center text-slate-400 text-sm font-medium">Tidak ada data akun.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards View --}}
            <div class="md:hidden space-y-4">
                @forelse ($this->adminUsers as $admin)
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-2 h-full 
                        {{ $admin->role === 'superadmin' ? 'bg-purple-500' : 
                          ($admin->role === 'admin' ? 'bg-red-500' : 
                          ($admin->role === 'dokter' ? 'bg-blue-500' : 'bg-emerald-500')) }}">
                    </div>
                    <p class="font-black text-slate-800 text-base leading-tight">{{ $admin->nama_lengkap }}</p>
                    <div class="mt-1 mb-3">
                        <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded border 
                            {{ $admin->role === 'superadmin' ? 'bg-purple-50 border-purple-100 text-purple-600' : 
                              ($admin->role === 'admin' ? 'bg-red-50 border-red-100 text-red-600' : 
                              ($admin->role === 'dokter' ? 'bg-blue-50 border-blue-100 text-blue-600' : 'bg-emerald-50 border-emerald-100 text-emerald-600')) }}">
                            {{ $admin->role }}
                        </span>
                    </div>
                    
                    <div class="text-xs text-slate-600 font-medium space-y-1.5 mb-5 bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <p><i class="fas fa-id-card text-slate-400 w-4"></i> SAP: <span class="font-mono">{{ $admin->no_sap ?? '-' }}</span></p>
                        <p><i class="fas fa-envelope text-slate-400 w-4"></i> {{ $admin->email }}</p>
                    </div>
                    
                    <div class="flex gap-2">
                        <button wire:click="editPassword({{ $admin->id }})" class="flex-1 bg-amber-50 border border-amber-100 py-2.5 rounded-xl text-xs font-bold text-amber-600 hover:bg-amber-100 transition-colors"><i class="fas fa-key mr-1"></i> Pass</button>
                        <button wire:click="edit({{ $admin->id }})" class="flex-1 bg-white border border-slate-200 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors"><i class="fas fa-pen mr-1"></i> Edit</button>
                        <button wire:click="delete({{ $admin->id }})" onclick="return confirm('Hapus?')" class="flex-1 bg-red-50 text-red-600 border border-red-100 py-2.5 rounded-xl text-xs font-bold hover:bg-red-100 transition-colors"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-sm font-medium bg-slate-50 rounded-2xl border border-slate-100">Tidak ada akun.</div>
                @endforelse
            </div>

            <div class="mt-6 border-t border-slate-100 pt-4">{{ $this->adminUsers->links() }}</div>
        </div>

        {{-- Modal Ganti Password Modern --}}
        @if ($editPasswordId)
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in">
                <div class="bg-white rounded-[2rem] shadow-2xl max-w-sm w-full p-6 md:p-8 text-center relative overflow-hidden">
                    <div class="w-14 h-14 md:w-16 md:h-16 bg-amber-100 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-4 border border-amber-200"><i class="fas fa-key text-xl md:text-2xl"></i></div>
                    <h3 class="text-xl md:text-2xl font-black text-slate-800 mb-1">Ganti Password</h3>
                    <p class="text-[11px] md:text-xs text-slate-500 font-medium mb-6">Ubah kata sandi keamanan untuk admin ini.</p>
                    
                    <form wire:submit.prevent="updatePassword" class="text-left">
                        <div class="mb-6">
                            <label class="block text-[10px] md:text-xs font-bold text-slate-600 uppercase tracking-widest mb-1.5">Password Baru</label>
                            <div class="relative">
                                <input type="password" id="pass_edit_admin" wire:model="newPassword" class="block w-full px-4 py-3 md:py-3.5 pr-10 text-sm font-bold rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-amber-500 shadow-sm transition-all">
                                <button type="button" onclick="togglePasswordVisibility('pass_edit_admin', 'eyeOpen_ea', 'eyeClosed_ea')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-amber-600 focus:outline-none">
                                    <img id="eyeOpen_ea" src="{{ asset('images/eye-open.png') }}" class="h-5 w-5 opacity-70">
                                    <img id="eyeClosed_ea" src="{{ asset('images/eye-closed.png') }}" class="h-5 w-5 hidden opacity-70">
                                </button>
                            </div>
                            @error('newPassword') <p class="mt-1 text-[10px] font-bold text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex flex-col gap-2">
                            <button type="submit" class="w-full py-3.5 md:py-3 rounded-xl font-bold text-white bg-slate-800 hover:bg-slate-700 shadow-lg transition-all text-sm md:text-xs">Simpan Password</button>
                            <button type="button" wire:click="$set('editPasswordId', null)" class="w-full py-3.5 md:py-3 rounded-xl font-bold text-slate-500 bg-white border border-slate-200 hover:bg-slate-50 transition-colors text-sm md:text-xs">Batalkan</button>
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
</div>