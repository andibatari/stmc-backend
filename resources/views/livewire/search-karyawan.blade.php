<div>
    {{-- TABS & ACTIONS HEADER --}}
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6 mb-8 border-b border-slate-100 pb-6">
        
        {{-- Modern Tabs (Segmented Control) --}}
        <div class="flex p-1 bg-slate-100 rounded-2xl self-start w-full sm:w-auto">
            <button wire:click="setActiveTab('ptst')" class="flex-1 sm:flex-none px-6 py-2.5 text-sm font-bold rounded-xl transition-all duration-200 {{ $activeTab === 'ptst' ? 'bg-white text-red-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                <i class="fas fa-id-badge mr-2"></i> Karyawan PTST
            </button>
            <button wire:click="setActiveTab('non-ptst')" class="flex-1 sm:flex-none px-6 py-2.5 text-sm font-bold rounded-xl transition-all duration-200 {{ $activeTab === 'non-ptst' ? 'bg-white text-red-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                <i class="fas fa-users mr-2"></i> Peserta Umum
            </button>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-wrap items-center gap-3 w-full xl:w-auto">
            @if ($activeTab === 'ptst')
                <button onclick="document.getElementById('excel-upload-input').click()" class="flex-1 sm:flex-none items-center justify-center bg-white border-2 border-emerald-500 text-emerald-600 font-bold py-2.5 px-5 rounded-xl hover:bg-emerald-50 transition-all text-sm shadow-sm"> 
                    <i class="fas fa-file-excel mr-2"></i> Import Excel
                </button>
                <form id="excel-upload-form" action="{{ route('karyawan.import') }}" method="POST" enctype="multipart/form-data" class="hidden">
                    @csrf <input type="file" name="file_karyawan" id="excel-upload-input" onchange="this.form.submit()">
                </form>
                <a href="{{ route('karyawan.create') }}" class="flex-1 sm:flex-none flex items-center justify-center bg-red-600 text-white font-bold py-2.5 px-5 rounded-xl shadow-lg shadow-red-600/30 hover:bg-red-700 hover:-translate-y-0.5 transition-all text-sm"> 
                    <i class="fas fa-user-plus mr-2"></i> Tambah Karyawan 
                </a> 
                <a href="{{ route('karyawan.download') }}" class="w-full sm:w-auto flex items-center justify-center bg-slate-800 text-white font-bold py-2.5 px-5 rounded-xl shadow-lg hover:bg-slate-700 hover:-translate-y-0.5 transition-all text-sm">
                    <i class="fas fa-download mr-2"></i> Ekspor
                </a>
            @else
                <button onclick="document.getElementById('excel-upload-input').click()" class="flex-1 sm:flex-none items-center justify-center bg-white border-2 border-emerald-500 text-emerald-600 font-bold py-2.5 px-5 rounded-xl hover:bg-emerald-50 transition-all text-sm shadow-sm"> 
                    <i class="fas fa-file-excel mr-2"></i> Import Excel
                </button>
                <form id="excel-upload-form" action="{{ route('peserta-mcu.import') }}" method="POST" enctype="multipart/form-data" class="hidden">
                    @csrf <input type="file" name="file_peserta_mcu" id="excel-upload-input" onchange="this.form.submit()">
                </form>
                <a href="{{ route('pasien.add.nonkaryawan') }}" class="flex-1 sm:flex-none flex items-center justify-center bg-red-600 text-white font-bold py-2.5 px-5 rounded-xl shadow-lg shadow-red-600/30 hover:bg-red-700 hover:-translate-y-0.5 transition-all text-sm"> 
                    <i class="fas fa-user-plus mr-2"></i> Tambah Umum 
                </a> 
                <a href="{{ route('peserta.mcu.download') }}" class="w-full sm:w-auto flex items-center justify-center bg-slate-800 text-white font-bold py-2.5 px-5 rounded-xl shadow-lg hover:bg-slate-700 hover:-translate-y-0.5 transition-all text-sm">
                    <i class="fas fa-download mr-2"></i> Ekspor
                </a>
            @endif
        </div>
    </div>

    {{-- SEARCH BAR --}}
    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4"> 
            @if ($activeTab === 'ptst')
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-3.5 text-slate-400"></i>
                    <input type="text" wire:model.live.debounce.500ms="searchSap" placeholder="Cari by SAP..." class="w-full pl-10 pr-4 py-3 text-sm font-medium bg-white border border-slate-200 rounded-xl focus:border-red-500 focus:ring-red-500">
                </div>
                <div class="relative">
                    <i class="fas fa-font absolute left-4 top-3.5 text-slate-400"></i>
                    <input type="text" wire:model.live.debounce.500ms="searchNama" placeholder="Cari by Nama..." class="w-full pl-10 pr-4 py-3 text-sm font-medium bg-white border border-slate-200 rounded-xl focus:border-red-500 focus:ring-red-500">
                </div>
                <div class="relative">
                    <i class="fas fa-building absolute left-4 top-3.5 text-slate-400"></i>
                    <input type="text" wire:model.live.debounce.500ms="searchUnitKerja" placeholder="Cari by Unit Kerja..." class="w-full pl-10 pr-4 py-3 text-sm font-medium bg-white border border-slate-200 rounded-xl focus:border-red-500 focus:ring-red-500">
                </div>
            @else
                <div class="relative md:col-span-2">
                    <i class="fas fa-search absolute left-4 top-3.5 text-slate-400"></i>
                    <input type="text" wire:model.live.debounce.500ms="searchNamaPasien" placeholder="Cari by Nama Pasien..." class="w-full pl-10 pr-4 py-3 text-sm font-medium bg-white border border-slate-200 rounded-xl focus:border-red-500 focus:ring-red-500">
                </div>
                <div class="relative">
                    <i class="fas fa-id-card absolute left-4 top-3.5 text-slate-400"></i>
                    <input type="text" wire:model.live.debounce.500ms="searchNik" placeholder="Cari by NIK..." class="w-full pl-10 pr-4 py-3 text-sm font-medium bg-white border border-slate-200 rounded-xl focus:border-red-500 focus:ring-red-500">
                </div>
            @endif
        </div>
    </div>

    {{-- TABLE KARYAWAN PTST --}}
    @if ($activeTab === 'ptst')
        <div class="overflow-x-auto border border-slate-100 rounded-2xl">
            <table class="min-w-full bg-white text-left">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase">No</th>
                        <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase">SAP</th>
                        <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase">Karyawan</th>
                        <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase">Unit Kerja</th>
                        <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase hidden lg:table-cell">NIK</th> 
                        <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase hidden sm:table-cell">L/P</th>
                        <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase hidden md:table-cell">Jabatan</th>
                        <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase hidden md:table-cell">No. HP</th>
                        <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($items as $karyawan)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-4 text-sm font-medium text-slate-500">{{ ($items->currentPage()-1) * $items->perPage() + $loop->iteration }}</td>
                            <td class="py-4 px-4 text-sm font-mono text-slate-600">{{ $karyawan->no_sap }}</td>
                            <td class="py-4 px-4 text-sm font-bold text-slate-800">{{ $karyawan->nama_karyawan }}</td>
                            <td class="py-4 px-4 text-sm text-slate-600">
                                <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded-lg text-xs font-semibold">{{ $karyawan->unitKerja->nama_unit_kerja ?? 'N/A' }}</span>
                            </td>
                            <td class="py-4 px-4 text-sm text-slate-500 hidden lg:table-cell font-mono">{{ $karyawan->nik_karyawan }}</td>
                            <td class="py-4 px-4 text-sm text-slate-600 hidden sm:table-cell">{{ $karyawan->jenis_kelamin }}</td>
                            <td class="py-4 px-4 text-sm text-slate-600 hidden md:table-cell">{{ $karyawan->jabatan ?? 'N/A' }}</td>
                            <td class="py-4 px-4 text-sm text-slate-600 hidden md:table-cell">{{ $karyawan->no_hp ?? '-' }}</td>
                            <td class="py-4 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('karyawan.show', $karyawan->id) }}" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all" title="Detail"><i class="fas fa-eye text-xs"></i></a>
                                    <a href="{{ route('karyawan.edit', $karyawan->id) }}" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white flex items-center justify-center transition-all" title="Edit"><i class="fas fa-pen text-xs"></i></a>
                                    <form action="{{ route('karyawan.destroy', $karyawan->id) }}" method="POST" onsubmit="return confirm('Hapus permanen data ini?');" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all" title="Hapus"><i class="fas fa-trash text-xs"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="py-12 text-center text-slate-400 font-medium">Data yang Anda cari tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6 border-t border-slate-100 pt-4">{{ $items->links() }}</div>
    @endif

    {{-- TABLE PESERTA UMUM (NON-PTST) --}}
    @if ($activeTab === 'non-ptst')
        <div class="overflow-x-auto border border-slate-100 rounded-2xl">
            <table class="min-w-full bg-white text-left">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase">No</th>
                        <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase">Nama Lengkap</th>
                        <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase">Perusahaan Asal</th>
                        <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase hidden md:table-cell">SAP</th>
                        <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase hidden sm:table-cell">NIK</th>
                        <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase hidden lg:table-cell">Tgl Lahir</th>
                        <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase hidden md:table-cell">No. HP</th>
                        <th class="py-4 px-4 text-xs font-bold text-slate-500 uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($items as $pesertaMcu)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-4 text-sm font-medium text-slate-500">{{ ($items->currentPage()-1) * $items->perPage() + $loop->iteration }}</td>
                            <td class="py-4 px-4 text-sm font-bold text-slate-800">{{ $pesertaMcu->nama_lengkap }}</td>
                            <td class="py-4 px-4 text-sm text-slate-600"><span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded-lg text-xs font-semibold">{{ $pesertaMcu->perusahaan_asal }}</span></td>
                            <td class="py-4 px-4 text-sm text-slate-500 hidden md:table-cell font-mono">{{ $pesertaMcu->no_sap ?? '-' }}</td>
                            <td class="py-4 px-4 text-sm text-slate-500 hidden sm:table-cell font-mono">{{ $pesertaMcu->nik_pasien }}</td>
                            <td class="py-4 px-4 text-sm text-slate-600 hidden lg:table-cell">{{ \Carbon\Carbon::parse($pesertaMcu->tanggal_lahir)->format('d M Y') }}</td>
                            <td class="py-4 px-4 text-sm text-slate-600 hidden md:table-cell">{{ $pesertaMcu->no_hp ?? '-' }}</td>
                            <td class="py-4 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('pasien.show', ['pesertaMcu' => $pesertaMcu->id]) }}" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all" title="Detail"><i class="fas fa-eye text-xs"></i></a>
                                    <a href="{{ route('keluarga.edit', ['keluarga' => $pesertaMcu->id]) }}" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white flex items-center justify-center transition-all" title="Edit"><i class="fas fa-pen text-xs"></i></a>
                                    <form action="{{ route('keluarga.destroy', ['keluarga' => $pesertaMcu->id]) }}" method="POST" onsubmit="return confirm('Hapus permanen data ini?');" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all" title="Hapus"><i class="fas fa-trash text-xs"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="py-12 text-center text-slate-400 font-medium">Data yang Anda cari tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6 border-t border-slate-100 pt-4">{{ $items->links() }}</div>
    @endif
</div>