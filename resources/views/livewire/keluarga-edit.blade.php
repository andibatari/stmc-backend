<div>
    {{-- Tombol Kembali --}}
    <div class="mb-6 lg:mb-8">
        <a href="javascript:history.back()" class="inline-flex items-center px-4 py-2 text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-xl shadow-sm hover:bg-slate-50 hover:text-slate-800 hover:-translate-x-1 transition-all duration-200">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    {{-- KARTU UTAMA --}}
    <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden p-6 md:p-10 max-w-7xl mx-auto">
        <div class="mb-8 border-b border-slate-100 pb-6">
            <h1 class="text-2xl lg:text-3xl font-black text-slate-800 flex items-center">
                <div class="w-10 h-10 bg-red-100 text-red-600 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-user-edit text-xl"></i>
                </div>
                Edit Data Pasien
            </h1>
            <p class="text-sm font-medium text-slate-500 mt-2 ml-14">Perbarui informasi identitas, fisik, kontak, dan alamat pasien.</p>
        </div>

        <form wire:submit.prevent="updateKeluarga" class="space-y-8">
            
            {{-- 1. IDENTITAS DASAR --}}
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-5 flex items-center"><i class="fas fa-id-badge mr-2 text-slate-400"></i> 1. Identitas Dasar</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Nama Lengkap</label>
                        <input type="text" wire:model.live="nama_lengkap" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 transition-colors" required>
                        @error('nama_lengkap') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">NIK (KTP)</label>
                        <input type="text" wire:model.live="nik_pasien" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 transition-colors font-mono">
                        @error('nik_pasien') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">NIP / SAP</label>
                        <input type="text" wire:model.live="no_sap" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 transition-colors font-mono">
                        @error('no_sap') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Perusahaan Asal</label>
                        <input type="text" wire:model.live="perusahaan_asal" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 transition-colors">
                        @error('perusahaan_asal') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Pekerjaan</label>
                        <input type="text" wire:model.live="pekerjaan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 transition-colors">
                        @error('pekerjaan') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- 2. KELAHIRAN & FISIK --}}
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-5 flex items-center"><i class="fas fa-heartbeat mr-2 text-slate-400"></i> 2. Kelahiran & Fisik</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Tempat Lahir</label>
                        <input type="text" wire:model.live="tempat_lahir" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 transition-colors">
                        @error('tempat_lahir') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Tanggal Lahir</label>
                        <input type="date" wire:model.live="tanggal_lahir" id="tanggal_lahir" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 transition-colors">
                        @error('tanggal_lahir') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Umur</label>
                        <input type="text" wire:model="umur" id="umur" class="block w-full px-4 py-3 text-sm font-bold rounded-xl border-slate-200 bg-slate-200 text-slate-600 cursor-not-allowed" readonly>
                        @error('umur') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Jenis Kelamin</label>
                        <select wire:model.live="jenis_kelamin" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white cursor-pointer focus:border-red-500">
                            <option value="">Pilih</option><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option>
                        </select>
                        @error('jenis_kelamin') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Gol. Darah</label>
                        <select wire:model.live="golongan_darah" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white cursor-pointer focus:border-red-500">
                            <option value="">Pilih</option><option value="A">A</option><option value="B">B</option><option value="AB">AB</option><option value="O">O</option>
                        </select>
                        @error('golongan_darah') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Tinggi (cm)</label>
                        <input type="number" wire:model.live="tinggi_badan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500">
                        @error('tinggi_badan') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Berat (kg)</label>
                        <input type="number" wire:model.live="berat_badan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500">
                        @error('berat_badan') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Pendidikan</label>
                        <select wire:model.live="pendidikan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white cursor-pointer focus:border-red-500">
                            <option value="">Pilih</option><option value="SD">SD</option><option value="SMP">SMP</option><option value="SMA/SMK">SMA/SMK</option><option value="D3">D3</option><option value="S1">S1</option><option value="S2">S2</option><option value="S3">S3</option>
                        </select>
                        @error('pendidikan') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- 3. DEMOGRAFI SOSIAL --}}
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-5 flex items-center"><i class="fas fa-users mr-2 text-slate-400"></i> 3. Demografi Sosial</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Agama</label>
                        <select wire:model.live="agama" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white cursor-pointer focus:border-red-500">
                            <option value="">Pilih</option><option value="Islam">Islam</option><option value="Kristen">Kristen</option><option value="Katolik">Katolik</option><option value="Hindu">Hindu</option><option value="Buddha">Buddha</option><option value="Konghucu">Konghucu</option>
                        </select>
                        @error('agama') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Status Pernikahan</label>
                        <select wire:model.live="status_pernikahan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white cursor-pointer focus:border-red-500">
                            <option value="">Pilih</option><option value="Menikah">Menikah</option><option value="Belum Menikah">Belum Menikah</option><option value="Cerai Hidup">Cerai Hidup</option><option value="Cerai Mati">Cerai Mati</option>
                        </select>
                        @error('status_pernikahan') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Kebangsaan</label>
                        <select wire:model.live="kebangsaan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white cursor-pointer focus:border-red-500">
                            <option value="">Pilih</option><option value="WNI">WNI</option><option value="WNA">WNA</option>
                        </select>
                        @error('kebangsaan') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- 4. ORGANISASI (HANYA JIKA BUKAN NON-KARYAWAN) --}}
            @if ($keluarga->tipe_anggota != 'Non-Karyawan')
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-5 flex items-center"><i class="fas fa-sitemap mr-2 text-slate-400"></i> 4. Organisasi Karyawan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Departemen</label>
                        <select wire:model.live="departemens_id" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white cursor-pointer focus:border-red-500">
                            <option value="">Pilih Departemen</option>
                            @foreach($departemens as $departemen)
                                <option value="{{ $departemen->id }}">{{ $departemen->nama_departemen }}</option>
                            @endforeach
                        </select>
                        @error('departemens_id') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Unit Kerja</label>
                        <select wire:model.live="unit_kerjas_id" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white cursor-pointer focus:border-red-500">
                            <option value="">Pilih Unit Kerja</option>
                            @foreach($unitKerjas as $unitKerja)
                                <option value="{{ $unitKerja->id }}">{{ $unitKerja->nama_unit_kerja }}</option>
                            @endforeach
                        </select>
                        @error('unit_kerjas_id') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            @endif

            {{-- 5. ALAMAT & KONTAK --}}
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-5 flex items-center"><i class="fas fa-map-marker-alt mr-2 text-slate-400"></i> 5. Alamat & Kontak</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Provinsi</label>
                        <select wire:model.live="provinsi_id" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white cursor-pointer focus:border-red-500">
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinsis as $provinsi)
                                <option value="{{ $provinsi->id }}">{{ $provinsi->nama_provinsi }}</option>
                            @endforeach
                        </select>
                        @error('provinsi_id') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Kabupaten/Kota</label>
                        <input type="text" wire:model.live="nama_kabupaten" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500">
                        @error('nama_kabupaten') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Kecamatan</label>
                        <input type="text" wire:model.live="nama_kecamatan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500">
                        @error('nama_kecamatan') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Nomor Handphone</label>
                        <input type="text" wire:model.live="no_hp" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 font-mono">
                        @error('no_hp') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Email Akses</label>
                        <input type="email" wire:model.live="email" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500">
                        @error('email') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Alamat Lengkap Domisili (Jalan, RT/RW)</label>
                        <textarea wire:model.live="alamat" rows="2" class="block w-full rounded-xl border border-slate-200 bg-white text-sm focus:border-red-500 p-4 resize-none"></textarea>
                        @error('alamat') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- 6. AKUN LOGIN (PASSWORD) --}}
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-2 flex items-center"><i class="fas fa-lock mr-2 text-slate-400"></i> 6. Keamanan Akun Login</h3>
                <p class="text-xs text-slate-500 mb-5">Kosongkan kolom di bawah ini jika tidak ingin mengubah password.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Password Baru</label>
                        <input type="password" wire:model.live="password" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500">
                        @error('password') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Konfirmasi Password Baru</label>
                        <input type="password" wire:model.live="password_confirmation" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500">
                        @error('password_confirmation') <span class="text-red-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- TOMBOL SIMPAN --}}
            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto px-8 py-3.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg hover:shadow-red-500/30 hover:-translate-y-0.5 transition-all duration-200 text-sm">
                    <span wire:loading.remove><i class="fas fa-save mr-2"></i> Simpan Perubahan Pasien</span>
                    <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> Memperbarui...</span>
                </button>
            </div>
        </form>
    </div>
</div>