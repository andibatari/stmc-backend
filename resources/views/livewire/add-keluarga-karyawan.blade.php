<form wire:submit.prevent="save" class="space-y-8">
    
    {{-- SECTION 1: Identitas --}}
    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
        <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-5 flex items-center"><i class="fas fa-id-card mr-2 text-slate-400"></i> Identitas Utama Pasien</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Tipe Anggota / Pasien <span class="text-red-500">*</span></label>
                <select wire:model.live="tipe_anggota" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 cursor-pointer" required>
                    <option value="">Pilih Tipe</option>
                    @if ($karyawan) <option value="Istri">Istri</option><option value="Suami">Suami</option> @else <option value="Non-Karyawan">Pasien Umum (Non-PTST)</option> @endif
                </select>
                @error('tipe_anggota') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" wire:model="nama_lengkap" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500" required>
                @error('nama_lengkap') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">NIK (KTP) <span class="text-red-500">*</span></label>
                <input type="text" wire:model="nik_pasien" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500" required>
                @error('nik_pasien') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Perusahaan Asal</label>
                <input type="text" wire:model="perusahaan_asal" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500" placeholder="Misal: Vendor PTST">
                @error('perusahaan_asal') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>
            {{-- SAP/NIP Khusus jika butuh --}}
            <div class="lg:col-span-4">
                <label class="block text-xs font-bold text-slate-600 mb-1.5">No. SAP / NIP / ID Khusus</label>
                <input type="text" wire:model="no_sap" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 lg:w-1/4">
            </div>
        </div>
    </div>
    
    {{-- SECTION 2: Kelahiran & Fisik --}}
    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
        <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-5 flex items-center"><i class="fas fa-heartbeat mr-2 text-slate-400"></i> Data Biologis & Fisik</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Jenis Kelamin</label>
                <select wire:model="jenis_kelamin" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white cursor-pointer"><option value="">Pilih...</option><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Tempat Lahir</label>
                <input type="text" wire:model="tempat_lahir" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Tanggal Lahir</label>
                <input type="date" wire:model.live="tanggal_lahir" id="tanggal_lahir" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Umur</label>
                <input type="number" wire:model="umur" id="umur" class="block w-full px-4 py-3 text-sm font-bold rounded-xl border border-slate-200 bg-slate-100 text-slate-500" readonly>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Gol. Darah</label>
                <select wire:model="golongan_darah" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white cursor-pointer"><option value="">Pilih...</option><option value="A">A</option><option value="B">B</option><option value="AB">AB</option><option value="O">O</option></select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Tinggi Badan (cm)</label>
                <input type="number" wire:model="tinggi_badan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Berat Badan (kg)</label>
                <input type="number" wire:model="berat_badan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Pendidikan</label>
                <select wire:model="pendidikan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white cursor-pointer"><option value="">Pilih...</option><option value="SD">SD</option><option value="SMP">SMP</option><option value="SMA/SMK">SMA/SMK</option><option value="D3">D3</option><option value="S1">S1</option><option value="S2">S2</option><option value="S3">S3</option></select>
            </div>
        </div>
    </div>

    {{-- SECTION 3: Kontak & Akun --}}
    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
        <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-5 flex items-center"><i class="fas fa-phone-alt mr-2 text-slate-400"></i> Kontak & Akun Layanan</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">No Handphone</label>
                <input type="text" wire:model="no_hp" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Email Pribadi</label>
                <input type="email" wire:model="email" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Agama</label>
                <select wire:model="agama" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white cursor-pointer"><option value="">Pilih...</option><option value="Islam">Islam</option><option value="Kristen">Kristen</option><option value="Katolik">Katolik</option><option value="Hindu">Hindu</option><option value="Buddha">Buddha</option><option value="Konghucu">Konghucu</option></select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Pekerjaan</label>
                <input type="text" wire:model="pekerjaan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white">
            </div>
            
            @if ($tipe_anggota == 'Non-Karyawan' || in_array($tipe_anggota, ['Istri', 'Suami']))
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Buat Password Login</label>
                    <input type="password" wire:model="password" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white">
                    @error('password') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Konfirmasi Password</label>
                    <input type="password" wire:model="password_confirmation" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white">
                </div>
            @endif
        </div>
        
        <div class="h-px bg-slate-200 w-full mb-6"></div>
        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4">Alamat Domisili</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Provinsi</label>
                <select wire:model.live="provinsi_id" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white cursor-pointer"><option value="">Pilih Provinsi</option> @foreach($provinsis as $provinsi) <option value="{{ $provinsi->id }}">{{ $provinsi->nama_provinsi }}</option> @endforeach </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Kabupaten/Kota</label>
                <input type="text" wire:model.live="nama_kabupaten" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Kecamatan</label>
                <input type="text" wire:model.live="nama_kecamatan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white">
            </div>
            <div class="lg:col-span-4">
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Alamat Lengkap (Jalan, RT/RW)</label>
                <textarea wire:model="alamat" rows="2" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white resize-none"></textarea>
            </div>
        </div>
    </div>
    
    <div class="flex justify-end pt-4">
        <button type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto px-8 py-3.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl shadow-lg hover:-translate-y-0.5 transition-all text-sm">
            <span wire:loading.remove><i class="fas fa-save mr-2"></i> Simpan Data Pasien</span>
            <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> Menyimpan...</span>
        </button>
    </div>
</form>