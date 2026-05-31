<div class="space-y-8 animate-fade-in">
    {{-- 1. IDENTITAS & DEMOGRAFI --}}
    <div>
        <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-4 flex items-center border-b border-slate-100 pb-2">
            <div class="w-6 h-6 rounded bg-blue-50 text-blue-500 flex items-center justify-center mr-2"><i class="fas fa-id-card"></i></div> Identitas Pasien
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 hover:border-blue-200 transition-colors">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tipe Pasien</span>
                <p class="text-sm font-black text-slate-800">{{ $pesertaMcu->tipe_anggota ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 hover:border-blue-200 transition-colors">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">NIK (Nomor Induk KTP)</span>
                <p class="text-sm font-black text-slate-800 font-mono">{{ $pesertaMcu->nik_pasien ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 hover:border-blue-200 transition-colors">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pekerjaan</span>
                <p class="text-sm font-black text-slate-800">{{ $pesertaMcu->pekerjaan ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 hover:border-blue-200 transition-colors">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Agama</span>
                <p class="text-sm font-black text-slate-800">{{ $pesertaMcu->agama ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 hover:border-blue-200 transition-colors">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pendidikan</span>
                <p class="text-sm font-black text-slate-800">{{ $pesertaMcu->pendidikan ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    {{-- 2. FISIK & KELAHIRAN --}}
    <div>
        <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-4 flex items-center border-b border-slate-100 pb-2">
            <div class="w-6 h-6 rounded bg-emerald-50 text-emerald-500 flex items-center justify-center mr-2"><i class="fas fa-heartbeat"></i></div> Biometrik & Kelahiran
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal Lahir</span>
                <p class="text-sm font-black text-slate-800">{{ $pesertaMcu->tanggal_lahir ? \Carbon\Carbon::parse($pesertaMcu->tanggal_lahir)->format('d M Y') : 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tempat Lahir</span>
                <p class="text-sm font-black text-slate-800">{{ $pesertaMcu->tempat_lahir ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Umur</span>
                <p class="text-sm font-black text-slate-800">{{ $pesertaMcu->umur ?? 'N/A' }} Thn</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Jenis Kelamin</span>
                <p class="text-sm font-black text-slate-800">{{ $pesertaMcu->jenis_kelamin ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tinggi Badan</span>
                <p class="text-sm font-black text-slate-800">{{ $pesertaMcu->tinggi_badan ?? '-' }} cm</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Berat Badan</span>
                <p class="text-sm font-black text-slate-800">{{ $pesertaMcu->berat_badan ?? '-' }} kg</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Golongan Darah</span>
                <p class="text-sm font-black text-red-600">{{ $pesertaMcu->golongan_darah ?? '-' }}</p>
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
                <p class="text-sm font-black text-slate-800">{{ $pesertaMcu->provinsi->nama_provinsi ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kabupaten/Kota</span>
                <p class="text-sm font-black text-slate-800">{{ $pesertaMcu->nama_kabupaten ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kecamatan</span>
                <p class="text-sm font-black text-slate-800">{{ $pesertaMcu->nama_kecamatan ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Alamat Lengkap</span>
                <p class="text-sm font-black text-slate-800">{{ $pesertaMcu->alamat ?? 'N/A' }}</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nomor Handphone</span>
                <p class="text-sm font-black text-slate-800 font-mono">{{ $pesertaMcu->no_hp ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Email Akses</span>
                <p class="text-sm font-black text-slate-800">{{ $pesertaMcu->email ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
    
    {{-- Tombol Edit --}}
    <div class="pt-6 border-t border-slate-100 flex justify-end">
        <a href="{{ route('keluarga.edit', ['keluarga' => $pesertaMcu->id]) }}" class="inline-flex items-center px-8 py-3.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl shadow-lg hover:-translate-y-0.5 transition-all text-sm">
            <i class="fas fa-edit mr-2"></i> Edit Data Pasien
        </a>
    </div>
</div>