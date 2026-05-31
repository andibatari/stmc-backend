@section('title', 'Edit Data Karyawan')

<div>
    {{-- Tombol Kembali --}}
    <div class="mb-6 lg:mb-8">
        <a href="javascript:history.back()" class="inline-flex items-center px-4 py-2 text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-xl shadow-sm hover:bg-slate-50 hover:text-slate-800 hover:-translate-x-1 transition-all duration-200">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    {{-- KARTU UTAMA FORM --}}
    <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden p-6 md:p-10 max-w-7xl mx-auto">
        
        {{-- Header Form --}}
        <div class="mb-8 border-b border-slate-100 pb-6">
            <h1 class="text-2xl lg:text-3xl font-black text-slate-800 flex items-center">
                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-user-edit text-xl"></i>
                </div>
                Edit Data Karyawan
            </h1>
            <p class="text-sm font-medium text-slate-500 mt-2 ml-14">Perbarui informasi profil, domisili, dan demografi karyawan yang terdaftar.</p>
        </div>

        <form wire:submit.prevent="updateKaryawan" class="space-y-8">
            
            {{-- SECTION 1: Informasi Identitas --}}
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-5 flex items-center"><i class="fas fa-id-badge mr-2 text-slate-400"></i> 1. Informasi Dasar</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">No SAP <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.live="no_sap" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors" required>
                        @error('no_sap') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">NIK Karyawan <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.live="nik_karyawan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors" required>
                        @error('nik_karyawan') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Nama Lengkap Karyawan <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.live="nama_karyawan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors" required>
                        @error('nama_karyawan') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Pekerjaan</label>
                        <input type="text" wire:model.live="pekerjaan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors">
                        @error('pekerjaan') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Hubungan</label>
                        <input type="text" wire:model.live="hubungan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors">
                        @error('hubungan') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- SECTION 2: Demografi Pribadi & Fisik --}}
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-5 flex items-center"><i class="fas fa-birthday-cake mr-2 text-slate-400"></i> 2. Data Pribadi & Fisik</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Tempat Lahir</label>
                        <input type="text" wire:model.live="tempat_lahir" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors">
                        @error('tempat_lahir') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Tanggal Lahir</label>
                        <input type="date" wire:model.live="tanggal_lahir" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors">
                        @error('tanggal_lahir') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Umur</label>
                        <input type="text" wire:model="umur" class="block w-full px-4 py-3 text-sm font-bold rounded-xl border border-slate-200 bg-slate-200 text-slate-600 cursor-not-allowed" readonly>
                        @error('umur') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Jenis Kelamin</label>
                        <select wire:model.live="jenis_kelamin" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors cursor-pointer">
                            <option value="">Pilih Kelamin</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                        @error('jenis_kelamin') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Tinggi Badan (cm)</label>
                        <input type="number" wire:model.live="tinggi_badan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors">
                        @error('tinggi_badan') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Berat Badan (kg)</label>
                        <input type="number" wire:model.live="berat_badan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors">
                        @error('berat_badan') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Golongan Darah</label>
                        <select wire:model.live="golongan_darah" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors cursor-pointer">
                            <option value="">Pilih Golongan Darah</option>
                            <option value="A">A</option><option value="B">B</option><option value="AB">AB</option><option value="O">O</option>
                        </select>
                        @error('golongan_darah') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Agama</label>
                        <select wire:model.live="agama" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors cursor-pointer">
                            <option value="">Pilih Agama</option>
                            <option value="Islam">Islam</option><option value="Kristen">Kristen</option><option value="Katolik">Katolik</option>
                            <option value="Hindu">Hindu</option><option value="Buddha">Buddha</option><option value="Konghucu">Konghucu</option>
                        </select>
                        @error('agama') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Status Pernikahan</label>
                        <select wire:model.live="status_pernikahan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors cursor-pointer">
                            <option value="">Pilih Status</option>
                            <option value="Menikah">Menikah</option><option value="Belum Menikah">Belum Menikah</option>
                            <option value="Cerai Hidup">Cerai Hidup</option><option value="Cerai Mati">Cerai Mati</option>
                        </select>
                        @error('status_pernikahan') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Kebangsaan</label>
                        <select wire:model.live="kebangsaan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors cursor-pointer">
                            <option value="">Pilih Kebangsaan</option>
                            <option value="WNI">WNI</option><option value="WNA">WNA</option>
                        </select>
                        @error('kebangsaan') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- SECTION 3: Organisasi & Pendidikan --}}
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-5 flex items-center"><i class="fas fa-sitemap mr-2 text-slate-400"></i> 3. Organisasi & Pendidikan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
                    <div class="md:col-span-2 lg:col-span-4">
                        <label class="block text-xs font-bold text-slate-600 mb-2">Departemen & Unit Kerja</label>
                        {{-- Memanggil komponen pencarian Departemen --}}
                        @livewire('searchable-departemen', ['initialDepartemenId' => $departemens_id, 'initialUnitKerjaId' => $unit_kerjas_id])
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Jabatan</label>
                        <select wire:model.live="jabatan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors cursor-pointer">
                            <option value="">Pilih Jabatan</option>
                            <option value="Senior Manager">Senior Manager</option><option value="Manager">Manager</option>
                            <option value="Associate">Associate</option><option value="Supervisor">Supervisor</option><option value="Staff">Staff</option>
                        </select>
                        @error('jabatan') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Eselon</label>
                        <select wire:model.live="eselon" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors cursor-pointer">
                            <option value="">Pilih Eselon</option>
                            <option value="I">I</option><option value="II">II</option><option value="III">III</option><option value="IV">IV</option><option value="V">V</option>
                        </select>
                        @error('eselon') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Pendidikan Terakhir</label>
                        <select wire:model.live="pendidikan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors cursor-pointer">
                            <option value="">Pilih Pendidikan</option>
                            <option value="SD">SD</option><option value="SMP">SMP</option><option value="SMA/SMK">SMA/SMK</option>
                            <option value="D1">D1</option><option value="D2">D2</option><option value="D3">D3</option><option value="D4">D4</option>
                            <option value="S1">S1</option><option value="S2">S2</option><option value="S3">S3</option>
                        </select>
                        @error('pendidikan') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- SECTION 4: Alamat Domisili --}}
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-5 flex items-center"><i class="fas fa-map-marker-alt mr-2 text-slate-400"></i> 4. Alamat Domisili Lengkap</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Provinsi</label>
                        <select wire:model.live="provinsi_id" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors cursor-pointer">
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinsis as $provinsi)
                                <option value="{{ $provinsi->id }}">{{ $provinsi->nama_provinsi }}</option>
                            @endforeach
                        </select>
                        @error('provinsi_id') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Kabupaten / Kota</label>
                        <input type="text" wire:model.live="nama_kabupaten" placeholder="Contoh: Makassar" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors">
                        @error('nama_kabupaten') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Kecamatan</label>
                        <input type="text" wire:model.live="nama_kecamatan" placeholder="Contoh: Biringkanaya" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors">
                        @error('nama_kecamatan') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div >
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Detail Alamat (Jalan, RT/RW, Perumahan)</label>
                        <input type="text" wire:model.live="alamat" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors">
                        @error('alamat') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- SECTION 5: Kontak & Keluarga Lainnya --}}
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-5 flex items-center"><i class="fas fa-phone mr-2 text-slate-400"></i> 5. Kontak, Pasangan & Akun</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Nomor Handphone</label>
                        <input type="text" wire:model.live="no_hp" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors">
                        @error('no_hp') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Email Akses</label>
                        <input type="email" wire:model.live="email" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors">
                        @error('email') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Nama Suami/Istri</label>
                        <input type="text" wire:model.live="suami_istri" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors">
                        @error('suami_istri') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Pekerjaan Suami/Istri</label>
                        <input type="text" wire:model.live="pekerjaan_suami_istri" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors">
                        @error('pekerjaan_suami_istri') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2 lg:col-span-4 mt-2">
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Ubah Password Akun <span class="text-slate-400 font-normal ml-1">(Kosongkan jika tidak ingin mengubah password)</span></label>
                        <input type="password" wire:model.live="password" placeholder="Masukkan password baru..." class="block w-full lg:w-1/2 px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors">
                        @error('password') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- TOMBOL SIMPAN --}}
            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto px-8 py-3.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg hover:shadow-red-500/30 hover:-translate-y-0.5 transition-all duration-200 text-sm">
                    <span wire:loading.remove><i class="fas fa-save mr-2"></i> Update Data Karyawan</span>
                    <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> Memperbarui Data...</span>
                </button>
            </div>
        </form>
    </div>
</div>