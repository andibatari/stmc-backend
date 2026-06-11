@section('title', 'Edit Data Karyawan')

<div>
    <div class="mb-4 md:mb-6">
        <a href="javascript:history.back()" class="inline-flex items-center px-3 py-1.5 text-xs md:text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 transition-all">
            <i class="fas fa-arrow-left mr-1.5"></i> Kembali
        </a>
    </div>

    {{-- KARTU UTAMA FORM (Desain Compact) --}}
    <div class="bg-white rounded-2xl md:rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden p-4 md:p-8 max-w-7xl mx-auto">
        
        <div class="mb-5 md:mb-8 border-b border-slate-100 pb-4 md:pb-5 flex items-center">
            <div class="w-8 h-8 md:w-10 md:h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3 shrink-0">
                <i class="fas fa-user-edit text-sm md:text-xl"></i>
            </div>
            <div>
                <h1 class="text-lg md:text-2xl font-black text-slate-800 leading-tight">Edit Data Karyawan</h1>
                <p class="text-[10px] md:text-xs font-medium text-slate-500 mt-0.5">Perbarui informasi profil dan domisili.</p>
            </div>
        </div>

        <form wire:submit.prevent="updateKaryawan" class="space-y-4 md:space-y-6">
            
            {{-- SECTION 1: Informasi Identitas (Menggunakan input responsif dan ringkas p-2) --}}
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 flex items-center"><i class="fas fa-id-badge mr-1.5 text-slate-400"></i> Identitas</h3>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
                    <div>
                        <label class="block text-[9px] font-bold text-slate-600 mb-1">No SAP <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.live="no_sap" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-600 mb-1">NIK KTP <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.live="nik_karyawan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500" required>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-[9px] font-bold text-slate-600 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.live="nama_karyawan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500" required>
                    </div>
                </div>
            </div>

            {{-- SECTION 2: Demografi Pribadi --}}
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 flex items-center"><i class="fas fa-birthday-cake mr-1.5 text-slate-400"></i> Demografi</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-3">
                    <div>
                        <label class="block text-[9px] font-bold text-slate-600 mb-1">Tempat Lahir</label>
                        <input type="text" wire:model.live="tempat_lahir" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-600 mb-1">Tanggal Lahir</label>
                        <input type="date" wire:model.live="tanggal_lahir" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-600 mb-1">Jenis Kelamin</label>
                        <select wire:model.live="jenis_kelamin" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 cursor-pointer">
                            <option value="">Pilih</option><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-600 mb-1">Agama</label>
                        <select wire:model.live="agama" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 cursor-pointer">
                            <option value="">Pilih</option><option value="Islam">Islam</option><option value="Kristen">Kristen</option><option value="Katolik">Katolik</option><option value="Hindu">Hindu</option><option value="Buddha">Buddha</option><option value="Konghucu">Konghucu</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 md:grid-cols-5 gap-3 md:gap-4">
                    <div>
                        <label class="block text-[9px] font-bold text-slate-600 mb-1">Tinggi (cm)</label>
                        <input type="number" wire:model.live="tinggi_badan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-600 mb-1">Berat (kg)</label>
                        <input type="number" wire:model.live="berat_badan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-600 mb-1">Gol. Darah</label>
                        <select wire:model.live="golongan_darah" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 cursor-pointer"><option value="">Pilih</option><option value="A">A</option><option value="B">B</option><option value="AB">AB</option><option value="O">O</option></select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-[9px] font-bold text-slate-600 mb-1">Status Pernikahan</label>
                        <select wire:model.live="status_pernikahan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 cursor-pointer">
                            <option value="">Pilih Status</option><option value="Menikah">Menikah</option><option value="Belum Menikah">Belum Menikah</option><option value="Cerai Hidup">Cerai Hidup</option><option value="Cerai Mati">Cerai Mati</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- SECTION 3: Pekerjaan --}}
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 flex items-center"><i class="fas fa-sitemap mr-1.5 text-slate-400"></i> Organisasi</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-3">
                    <div class="col-span-2">
                        <label class="block text-[9px] font-bold text-slate-600 mb-1">Jabatan & Eselon</label>
                        <div class="flex gap-2">
                            <select wire:model.live="jabatan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 cursor-pointer"><option value="">Jabatan</option><option value="Senior Manager">Senior Manager</option><option value="Manager">Manager</option><option value="Associate">Associate</option><option value="Supervisor">Supervisor</option><option value="Staff">Staff</option></select>
                            <select wire:model.live="eselon" class="block w-1/3 px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 cursor-pointer"><option value="">Eselon</option><option value="I">I</option><option value="II">II</option><option value="III">III</option><option value="IV">IV</option><option value="V">V</option></select>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-[9px] font-bold text-slate-600 mb-1">Pendidikan Terakhir</label>
                        <select wire:model.live="pendidikan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 cursor-pointer"><option value="">Pilih Pendidikan</option><option value="SD">SD</option><option value="SMP">SMP</option><option value="SMA/SMK">SMA/SMK</option><option value="D1">D1</option><option value="D2">D2</option><option value="D3">D3</option><option value="D4">D4</option><option value="S1">S1</option><option value="S2">S2</option><option value="S3">S3</option></select>
                    </div>
                </div>
                
                {{-- Modul dinamis untuk memilih data hirarki perusahaan (Departemen->Unit) --}}
                @livewire('searchable-departemen', ['initialDepartemenId' => $departemens_id, 'initialUnitKerjaId' => $unit_kerjas_id])
                {{-- Tambahkan 2 baris ini --}}
                @error('departemens_id') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                @error('unit_kerjas_id') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- SECTION 4: Alamat & Kontak --}}
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 flex items-center"><i class="fas fa-map-marker-alt mr-1.5 text-slate-400"></i> Kontak & Domisili</h3>
                
                {{-- Ganti grid menjadi 2 kolom saja karena password sudah dipindah --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4 mb-3">
                    <div>
                        <label class="block text-[9px] font-bold text-slate-600 mb-1">No Handphone</label>
                        <input type="text" wire:model.live="no_hp" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-600 mb-1">Email Akses</label>
                        <input type="email" wire:model.live="email" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2 mt-4 pt-4 border-t border-slate-200">
                    <div>
                        <label class="block text-[9px] font-bold text-slate-600 mb-1">Provinsi</label>
                        <select wire:model.live="provinsi_id" class="block w-full px-2 py-2 text-[10px] md:text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 cursor-pointer">
                            <option value="">Provinsi</option> @foreach($provinsis as $provinsi) <option value="{{ $provinsi->id }}">{{ $provinsi->nama_provinsi }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-600 mb-1">Kabupaten/Kota</label>
                        <input type="text" wire:model.live="nama_kabupaten" class="block w-full px-2 py-2 text-[10px] md:text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-600 mb-1">Kecamatan</label>
                        <input type="text" wire:model.live="nama_kecamatan" class="block w-full px-2 py-2 text-[10px] md:text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500">
                    </div>
                    <div class="col-span-3">
                        <label class="block text-[9px] font-bold text-slate-600 mb-1">Alamat Lengkap</label>
                        <input type="text" wire:model.live="alamat" placeholder="Jalan, RT/RW, Perumahan" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500">
                    </div>
                </div>
            </div>

            {{-- SECTION 5: Keamanan Akun Login (BARU) --}}
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1 flex items-center">
                    <i class="fas fa-lock mr-1.5 text-slate-400"></i> 4. Keamanan Akun Login
                </h3>
                <p class="text-[10px] text-slate-400 mb-4 font-medium">Biarkan kolom kosong jika Anda tidak berencana mengubah kata sandi.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                    {{-- Input Password Baru --}}
                    <div>
                        <label class="block text-[9px] font-bold text-slate-600 mb-1">Password Baru</label>
                        <div class="relative">
                            <input type="password" id="pass_edit_kar" wire:model.live="password" placeholder="••••••••" class="block w-full px-3 py-2 pr-10 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 shadow-sm transition-all">
                            <button type="button" onclick="togglePasswordVisibility('pass_edit_kar', 'eyeOpen_ek', 'eyeClosed_ek')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-blue-600 focus:outline-none">
                                <img id="eyeOpen_ek" src="{{ asset('images/eye-open.png') }}" class="h-4 w-4 opacity-70">
                                <img id="eyeClosed_ek" src="{{ asset('images/eye-closed.png') }}" class="h-4 w-4 hidden opacity-70">
                            </button>
                        </div>
                        @error('password') <span class="text-red-500 text-[10px] font-bold mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Input Konfirmasi Password Baru --}}
                    <div>
                        <label class="block text-[9px] font-bold text-slate-600 mb-1">Konfirmasi Password Baru</label>
                        <div class="relative">
                            {{-- ID dibuat berbeda (pass_edit_kar_confirm dll) agar toggle mata JS tidak bentrok --}}
                            <input type="password" id="pass_edit_kar_confirm" wire:model.live="password_confirmation" placeholder="••••••••" class="block w-full px-3 py-2 pr-10 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-blue-500 shadow-sm transition-all">
                            <button type="button" onclick="togglePasswordVisibility('pass_edit_kar_confirm', 'eyeOpen_ek_conf', 'eyeClosed_ek_conf')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-blue-600 focus:outline-none">
                                <img id="eyeOpen_ek_conf" src="{{ asset('images/eye-open.png') }}" class="h-4 w-4 opacity-70">
                                <img id="eyeClosed_ek_conf" src="{{ asset('images/eye-closed.png') }}" class="h-4 w-4 hidden opacity-70">
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol aksi dieksekusi via AJAX menggunakan Livewire (wire:click / submit.prevent) --}}
            <div class="flex justify-end pt-2 border-t border-slate-100">
                <button type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto px-8 py-3 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-lg shadow-md hover:-translate-y-0.5 transition-all text-xs active:scale-95">
                    <span wire:loading.remove><i class="fas fa-save mr-1.5"></i> Simpan Perubahan</span>
                    <span wire:loading><i class="fas fa-circle-notch fa-spin mr-1.5"></i> Memperbarui...</span>
                </button>
            </div>
        </form>
    </div>
</div>