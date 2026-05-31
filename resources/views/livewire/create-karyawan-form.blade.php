<div>
    <form wire:submit.prevent="saveKaryawan" class="space-y-8">
        
        {{-- SECTION 1: Informasi Dasar --}}
        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-5 flex items-center"><i class="fas fa-id-badge mr-2 text-slate-400"></i> Informasi Dasar</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach([
                    ['model' => 'no_sap', 'label' => 'No SAP', 'req' => true],
                    ['model' => 'nik_karyawan', 'label' => 'NIK Karyawan', 'req' => true],
                    ['model' => 'nama_karyawan', 'label' => 'Nama Lengkap', 'req' => true],
                    ['model' => 'pekerjaan', 'label' => 'Pekerjaan', 'req' => false],
                ] as $field)
                <div>
                    <label for="{{ $field['model'] }}" class="block text-xs font-bold text-slate-600 mb-1.5">{{ $field['label'] }} @if($field['req'])<span class="text-red-500">*</span>@endif</label>
                    <input type="text" wire:model.live="{{ $field['model'] }}" id="{{ $field['model'] }}" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors" @if($field['req']) required @endif>
                    @error($field['model']) <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                @endforeach
            </div>
        </div>

        {{-- SECTION 2: Demografi --}}
        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-5 flex items-center"><i class="fas fa-birthday-cake mr-2 text-slate-400"></i> Data Pribadi & Fisik</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-5">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Tempat Lahir</label>
                    <input type="text" wire:model.live="tempat_lahir" id="tempat_lahir" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors">
                    @error('tempat_lahir') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Tanggal Lahir</label>
                    <input type="date" wire:model.live="tanggal_lahir" id="tanggal_lahir" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors">
                    @error('tanggal_lahir') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Umur</label>
                    <input type="text" wire:model="umur" id="umur" class="block w-full px-4 py-3 text-sm font-bold rounded-xl border border-slate-200 bg-slate-100 text-slate-500 cursor-not-allowed" readonly>
                    @error('umur') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Jenis Kelamin</label>
                    <select wire:model.live="jenis_kelamin" id="jenis_kelamin" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 transition-colors cursor-pointer">
                        <option value="">Pilih</option><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option>
                    </select>
                    @error('jenis_kelamin') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Gol. Darah</label>
                    <select wire:model.live="golongan_darah" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 transition-colors cursor-pointer">
                        <option value="">Pilih</option><option value="A">A</option><option value="B">B</option><option value="AB">AB</option><option value="O">O</option>
                    </select>
                    @error('golongan_darah') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Agama</label>
                    <select wire:model.live="agama" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 transition-colors cursor-pointer">
                        <option value="">Pilih</option><option value="Islam">Islam</option><option value="Kristen">Kristen</option><option value="Katolik">Katolik</option><option value="Hindu">Hindu</option><option value="Buddha">Buddha</option><option value="Konghucu">Konghucu</option>
                    </select>
                    @error('agama') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Status Pernikahan</label>
                    <select wire:model.live="status_pernikahan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 transition-colors cursor-pointer">
                        <option value="">Pilih Status</option><option value="Menikah">Menikah</option><option value="Belum Menikah">Belum Menikah</option><option value="Cerai Hidup">Cerai Hidup</option><option value="Cerai Mati">Cerai Mati</option>
                    </select>
                    @error('status_pernikahan') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Hubungan</label>
                    <input type="text" wire:model.live="hubungan" id="hubungan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500 transition-colors">
                    @error('hubungan') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- SECTION 3: Organisasi & Alamat --}}
        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-5 flex items-center"><i class="fas fa-building mr-2 text-slate-400"></i> Organisasi & Lokasi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-5">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Kebangsaan</label>
                    <select wire:model.live="kebangsaan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white cursor-pointer"><option value="">Pilih</option><option value="WNI">WNI</option><option value="WNA">WNA</option></select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Jabatan</label>
                    <select wire:model.live="jabatan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white cursor-pointer"><option value="">Pilih Jabatan</option><option value="Senior Manager">Senior Manager</option><option value="Manager">Manager</option><option value="Associate">Associate</option><option value="Supervisor">Supervisor</option><option value="Staff">Staff</option></select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Eselon</label>
                    <select wire:model.live="eselon" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white cursor-pointer"><option value="">Pilih</option><option value="I">I</option><option value="II">II</option><option value="III">III</option><option value="IV">IV</option><option value="V">V</option></select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Pendidikan</label>
                    <select wire:model.live="pendidikan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white cursor-pointer"><option value="">Pilih</option><option value="SD">SD</option><option value="SMP">SMP</option><option value="SMA/SMK">SMA/SMK</option><option value="D1">D1</option><option value="D2">D2</option><option value="D3">D3</option><option value="D4">D4</option><option value="S1">S1</option><option value="S2">S2</option><option value="S3">S3</option></select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6 pt-6 border-t border-slate-200/60">
                <div class="col-span-1">
                    <label class="block text-xs font-bold text-slate-600 mb-2">Departemen & Unit Kerja</label>
                    @livewire('searchable-departemen', ['initialDepartemenId' => $departemens_id, 'initialUnitKerjaId' => $unit_kerjas_id])
                </div>
                <div class="col-span-1">
                    <label class="block text-xs font-bold text-slate-600 mb-2">Alamat Domisili</label>
                    <div class="grid grid-cols-3 gap-3">
                        <select wire:model.live="provinsi_id" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white cursor-pointer"><option value="">Pilih Provinsi</option> @foreach($provinsis as $provinsi) <option value="{{ $provinsi->id }}">{{ $provinsi->nama_provinsi }}</option> @endforeach </select>
                        <input type="text" wire:model.live="nama_kabupaten" placeholder="Kabupaten/Kota" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500">
                        <input type="text" wire:model.live="nama_kecamatan" placeholder="Kecamatan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500">
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 4: Kontak --}}
        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-5 flex items-center"><i class="fas fa-address-book mr-2 text-slate-400"></i> Data Kontak & Keluarga</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-5">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Email Akses</label>
                    <input type="email" wire:model.live="email" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Password</label>
                    <input type="password" wire:model.live="password" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">No Handphone</label>
                    <input type="text" wire:model.live="no_hp" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Tinggi Badan (cm)</label>
                    <input type="number" wire:model.live="tinggi_badan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Alamat Detail (Jalan, RT/RW)</label>
                    <input type="text" wire:model.live="alamat" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Nama Suami/Istri</label>
                    <input type="text" wire:model.live="suami_istri" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Berat Badan (kg)</label>
                    <input type="number" wire:model.live="berat_badan" class="block w-full px-4 py-3 text-sm font-medium rounded-xl border border-slate-200 bg-white focus:border-red-500">
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto px-8 py-3.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl shadow-lg hover:-translate-y-0.5 transition-all text-sm">
                <span wire:loading.remove><i class="fas fa-save mr-2"></i> Simpan Data Karyawan</span>
                <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> Menyimpan...</span>
            </button>
        </div>
    </form>
</div>