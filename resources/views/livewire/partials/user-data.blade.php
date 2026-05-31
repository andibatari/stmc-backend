<div class="space-y-8 animate-fade-in">
    {{-- 1. IDENTITAS & PEKERJAAN --}}
    <div>
        <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-4 flex items-center border-b border-slate-100 pb-2">
            <div class="w-6 h-6 rounded bg-blue-50 text-blue-500 flex items-center justify-center mr-2"><i class="fas fa-id-badge"></i></div> Identitas & Pekerjaan
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tipe Anggota</span>
                <p class="text-sm font-black text-slate-800">{{ $user->tipe_anggota ?? 'Karyawan' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">NIK (Nomor Induk KTP)</span>
                <p class="text-sm font-black text-slate-800 font-mono">{{ $user->nik_karyawan ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pekerjaan</span>
                <p class="text-sm font-black text-slate-800">{{ $user->pekerjaan ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Jabatan</span>
                <p class="text-sm font-black text-slate-800">{{ $user->jabatan ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Eselon</span>
                <p class="text-sm font-black text-slate-800">{{ $user->eselon ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Departemen</span>
                <p class="text-sm font-black text-slate-800">{{ $user->departemen->nama_departemen ?? 'N/A' }}</p>
            </div>
            <div class="md:col-span-2 lg:col-span-3 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Unit Kerja</span>
                <p class="text-sm font-black text-slate-800">{{ $user->unitKerja->nama_unit_kerja ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    {{-- 2. DEMOGRAFI FISIK & SOSIAL --}}
    <div>
        <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-4 flex items-center border-b border-slate-100 pb-2">
            <div class="w-6 h-6 rounded bg-emerald-50 text-emerald-500 flex items-center justify-center mr-2"><i class="fas fa-heartbeat"></i></div> Demografi Pribadi
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal Lahir</span>
                <p class="text-sm font-black text-slate-800">{{ $user->tanggal_lahir ? \Carbon\Carbon::parse($user->tanggal_lahir)->format('d M Y') : 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tempat Lahir</span>
                <p class="text-sm font-black text-slate-800">{{ $user->tempat_lahir ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Umur</span>
                <p class="text-sm font-black text-slate-800">{{ $user->umur ?? 'N/A' }} Thn</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Jenis Kelamin</span>
                <p class="text-sm font-black text-slate-800">{{ $user->jenis_kelamin ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tinggi Badan</span>
                <p class="text-sm font-black text-slate-800">{{ $user->tinggi_badan ?? '-' }} cm</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Berat Badan</span>
                <p class="text-sm font-black text-slate-800">{{ $user->berat_badan ?? '-' }} kg</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Golongan Darah</span>
                <p class="text-sm font-black text-red-600">{{ $user->golongan_darah ?? '-' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kebangsaan</span>
                <p class="text-sm font-black text-slate-800">{{ $user->kebangsaan ?? 'N/A' }}</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Agama</span>
                <p class="text-sm font-black text-slate-800">{{ $user->agama ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status Nikah</span>
                <p class="text-sm font-black text-slate-800">{{ $user->status_pernikahan ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pasangan (Suami/Istri)</span>
                <p class="text-sm font-black text-slate-800">{{ $user->suami_istri ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pekerjaan Pasangan</span>
                <p class="text-sm font-black text-slate-800">{{ $user->pekerjaan_suami_istri ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    {{-- 3. ALAMAT & KONTAK --}}
    <div>
        <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-4 flex items-center border-b border-slate-100 pb-2">
            <div class="w-6 h-6 rounded bg-amber-50 text-amber-500 flex items-center justify-center mr-2"><i class="fas fa-map-marker-alt"></i></div> Domisili & Kontak
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Provinsi</span>
                <p class="text-sm font-black text-slate-800">{{ $user->provinsi->nama_provinsi ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kabupaten/Kota</span>
                <p class="text-sm font-black text-slate-800">{{ $user->nama_kabupaten ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kecamatan</span>
                <p class="text-sm font-black text-slate-800">{{ $user->nama_kecamatan ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Alamat Lengkap</span>
                <p class="text-sm font-black text-slate-800">{{ $user->alamat ?? 'N/A' }}</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nomor Handphone</span>
                <p class="text-sm font-black text-slate-800 font-mono">{{ $user->no_hp ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Email Akses</span>
                <p class="text-sm font-black text-slate-800">{{ $user->email ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
    
    {{-- Tombol Edit Dinamis --}}
    <div class="pt-6 border-t border-slate-100 flex justify-end">
        @if ($user->id === $karyawan->id)
            <a href="{{ route('karyawan.edit', ['karyawan' => $karyawan->id]) }}" class="inline-flex items-center px-8 py-3.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl shadow-lg hover:-translate-y-0.5 transition-all text-sm">
                <i class="fas fa-user-edit mr-2"></i> Edit Data Karyawan
            </a>
        @else
            <a href="{{ route('keluarga.edit', ['keluarga' => $user->id]) }}" class="inline-flex items-center px-8 py-3.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl shadow-lg hover:-translate-y-0.5 transition-all text-sm">
                <i class="fas fa-user-edit mr-2"></i> Edit Data Keluarga
            </a>
        @endif
    </div>
</div>