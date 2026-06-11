<form wire:submit.prevent="save" class="space-y-4 md:space-y-6">
    
    {{-- SECTION 1: Identitas --}}
    <div class="bg-slate-50 p-4 md:p-6 rounded-xl border border-slate-100">
        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 flex items-center"><i class="fas fa-id-card mr-1.5 text-slate-400"></i> Identitas</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
            <div class="col-span-2">
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" wire:model="nama_lengkap" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500" required>
            </div>
            <div>
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Tipe Pasien <span class="text-red-500">*</span></label>
                <select wire:model.live="tipe_anggota" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 cursor-pointer" required>
                    <option value="">Pilih</option>
                    @if ($karyawan) <option value="Istri">Istri</option><option value="Suami">Suami</option><option value="Anak">Anak</option> 
                    @else <option value="Non-Karyawan">Umum</option> @endif
                </select>
            </div>
            <div>
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">NIK (KTP) <span class="text-red-500">*</span></label>
                <input type="text" wire:model="nik_pasien" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500" required>
                {{-- TAMBAHKAN INI --}}
                @error('nik_pasien') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Perusahaan / Instansi <span class="text-red-500">*</span></label>
                <input type="text" wire:model="perusahaan_asal" placeholder="Ketik Umum jika tidak ada" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500" required>
                @error('perusahaan_asal') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
    
    {{-- SECTION 2: Kelahiran & Fisik --}}
    <div class="bg-slate-50 p-4 md:p-6 rounded-xl border border-slate-100">
        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 flex items-center"><i class="fas fa-heartbeat mr-1.5 text-slate-400"></i> Biometrik</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
            <div>
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Jenis Kelamin</label>
                <select wire:model="jenis_kelamin" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white cursor-pointer"><option value="">Pilih...</option><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select>
            </div>
            <div>
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Tempat Lahir</label>
                <input type="text" wire:model="tempat_lahir" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white">
            </div>
            <div>
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Tanggal Lahir</label>
                <input type="date" wire:model.live="tanggal_lahir" id="tanggal_lahir" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white">
            </div>
            <div>
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Umur</label>
                <input type="number" wire:model="umur" id="umur" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-slate-100 text-slate-500" readonly>
            </div>
        </div>
    </div>

    {{-- SECTION 3: Kontak & Keamanan --}}
    <div class="bg-slate-50 p-4 md:p-6 rounded-xl border border-slate-100">
        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 flex items-center"><i class="fas fa-lock mr-1.5 text-slate-400"></i> Akun Akses</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4">
            <div>
                <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">No Handphone</label>
                <input type="text" wire:model="no_hp" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white">
            </div>
            @if ($tipe_anggota == 'Non-Karyawan' || in_array($tipe_anggota, ['Istri', 'Suami']))
                <div>
                    <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Buat Password Login</label>
                    <div class="relative">
                        <input type="password" id="pass_add_kel" wire:model="password" class="block w-full px-3 py-2 pr-10 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm transition-all">
                        <button type="button" onclick="togglePasswordVisibility('pass_add_kel', 'eyeOpen_ak1', 'eyeClosed_ak1')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-red-600 focus:outline-none">
                            <img id="eyeOpen_ak1" src="{{ asset('images/eye-open.png') }}" class="h-4 w-4 opacity-70">
                            <img id="eyeClosed_ak1" src="{{ asset('images/eye-closed.png') }}" class="h-4 w-4 hidden opacity-70">
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-[9px] md:text-xs font-bold text-slate-600 mb-1">Ulangi Password</label>
                    <div class="relative">
                        <input type="password" id="pass_add_kel_conf" wire:model="password_confirmation" class="block w-full px-3 py-2 pr-10 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 shadow-sm transition-all">
                        <button type="button" onclick="togglePasswordVisibility('pass_add_kel_conf', 'eyeOpen_ak2', 'eyeClosed_ak2')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-red-600 focus:outline-none">
                            <img id="eyeOpen_ak2" src="{{ asset('images/eye-open.png') }}" class="h-4 w-4 opacity-70">
                            <img id="eyeClosed_ak2" src="{{ asset('images/eye-closed.png') }}" class="h-4 w-4 hidden opacity-70">
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <div class="flex justify-end pt-2">
        <button type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto px-8 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg shadow-md hover:-translate-y-0.5 transition-all text-xs active:scale-95">
            <span wire:loading.remove><i class="fas fa-save mr-1.5"></i> Registrasi Pasien</span>
            <span wire:loading><i class="fas fa-circle-notch fa-spin mr-1.5"></i> Menyimpan...</span>
        </button>
    </div>
</form>