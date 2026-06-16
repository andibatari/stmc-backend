@extends('layouts.app')
@section('title', 'Daftar Jadwal')

@section('content')
<style>
    /* x-cloak menyembunyikan elemen HTML hingga Alpine.js selesai menginisialisasi DOM untuk mencegah layout melompat (flicker) */
    [x-cloak] { display: none !important; }
    /* Utilitas CSS kustom untuk menghilangkan scrollbar default browser yang memakan ruang visual */
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    /* Overlay loading */
    #loading-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background-color: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(4px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }
</style>

{{-- Spinner Loading Full Screen --}}
<div id="loading-overlay">
    <i class="fas fa-circle-notch fa-spin text-red-600 text-4xl mb-4"></i>
    <p class="text-sm font-bold text-slate-700 tracking-wider">Memproses Data...</p>
</div>

{{-- Container utama mendeklarasikan Alpine.js component. State isOpen mengontrol visibilitas dropdown menu aksi baris tabel --}}
<div x-data="{
        isOpen: false,
        currentId: null,
        currentStatus: '',
        top: 0,
        left: 0,
        // Fungsi ini bertugas mengkalkulasi koordinat X dan Y klik pengguna agar dropdown menu muncul tepat di sebelah tombol
        openMenu(event, id, status) {
            this.currentId = id;
            this.currentStatus = status;
            
            // getBoundingClientRect mengambil posisi relatif tombol terhadap viewport (layar browser)
            const rect = event.currentTarget.getBoundingClientRect();
            
            // Menentukan posisi horizontal (X). Dikurangi 208px (estimasi lebar menu) agar menu mengarah ke kiri
            let x = rect.right - 208;
            if (x < 10) x = 10; // Cegah menu terpotong batas kiri layar
            if (x + 208 > window.innerWidth) x = window.innerWidth - 218; // Cegah menu terpotong batas kanan layar
            
            // Menentukan posisi vertikal (Y).
            let y = rect.bottom + 5;
            if (y + 250 > window.innerHeight) y = rect.top - 260; // Jika tidak muat di bawah, menu muncul ke arah atas
            
            this.left = x;
            this.top = y;
            this.isOpen = true; // Trigger render CSS transisi pada menu
        }
    }" 
    class="px-2 md:px-6 py-4 md:py-6 min-h-screen relative">
    
    {{-- Bagian Header halaman --}}
    <div class="flex items-center justify-between mb-4 md:mb-6">
        <div>
            <h1 class="text-xl md:text-2xl font-black text-slate-800">Manajemen Jadwal</h1>
            <p class="text-[10px] md:text-sm font-medium text-slate-500 mt-0.5">Kelola dan pantau antrean MCU.</p>
        </div>
        <a href="{{ route('jadwal.create') }}" class="hidden md:inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm text-xs transition-colors">
            <i class="fas fa-plus mr-1.5"></i> Tambah Jadwal
        </a>
    </div>

    {{-- Wrapper konten utama dengan efek shadow minimalis --}}
    <div class="bg-white rounded-2xl md:rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-4 md:p-6">
            
            {{-- Tombol tambah khusus mobile, mengambil lebar penuh layar (w-full) --}}
            <a href="{{ route('jadwal.create') }}" class="md:hidden w-full flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-sm mb-4 text-xs transition-colors">
                <i class="fas fa-plus mr-1.5"></i> Buat Jadwal Baru
            </a>

            {{-- Komponen Livewire untuk statistik antrean, diletakkan dalam grid abu-abu agar terpisah dari konten utama --}}
            <div class="mb-5 bg-slate-50 rounded-xl p-3 md:p-4 border border-slate-100">
                <h2 class="text-[10px] md:text-xs font-bold text-slate-700 mb-3 flex items-center uppercase tracking-widest">
                    <i class="fas fa-users text-red-500 mr-2"></i> Pantauan Antrean Poli Hari Ini
                </h2>
                <livewire:admin.card-antrean-poli />
            </div>

            {{-- Indikator Kapasitas Kuota --}}
            <div class="bg-white border border-slate-200 rounded-xl p-3 md:p-4 mb-5 flex items-center justify-between shadow-sm">
                <div>
                    <h2 class="text-[9px] md:text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-0.5">Status Kuota Hari Ini</h2>
                    <p class="text-xs md:text-sm font-black text-slate-800">{{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-lg md:text-xl font-black leading-none {{ $sisaKuota == 0 ? 'text-red-600' : 'text-emerald-600' }}">
                        {{ $kuotaTerisi }} <span class="text-[10px] md:text-xs font-medium text-slate-400">/ 30</span>
                    </p>
                    <p class="text-[8px] md:text-[9px] font-bold uppercase tracking-wider {{ $sisaKuota == 0 ? 'text-red-500' : 'text-emerald-500' }}">
                        {{ $sisaKuota == 0 ? 'Kuota Penuh' : 'Tersisa: ' . $sisaKuota }}
                    </p>
                </div>
            </div>

            {{-- Form Pencarian dan Pemfilteran Data. Menggunakan metode GET agar parameter filter tersimpan di URL --}}
            <form id="filter-form" method="GET" action="{{ route('jadwal.index') }}" class="bg-slate-50 border border-slate-100 rounded-xl p-3 mb-5 shadow-inner">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3 items-end">
                    <div class="col-span-2 md:col-span-1">
                        <label for="search_sap" class="block text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1">Cari SAP / Nama</label>
                        <input type="text" name="search_sap" id="search_sap" value="{{ $search_sap }}" placeholder="Cari di semua tanggal..."
                            class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500">
                    </div>
                    <div>
                        <label for="tanggal_filter" class="block text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1">Filter Tgl</label>
                        {{-- Input ini akan otomatis terisi tanggal hari ini saat pertama kali buka --}}
                        <input type="date" name="tanggal_filter" id="tanggal_filter" value="{{ $tanggal_filter }}" 
                            class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500">
                    </div>
                    <div>
                        <label for="status_jadwal" class="block text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1">Status</label>
                        <select id="status_jadwal" name="status" class="block w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="Scheduled" {{ $status == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="Present" {{ $status == 'Present' ? 'selected' : '' }}>Present</option>
                            <option value="Finished" {{ $status == 'Finished' ? 'selected' : '' }}>Finished</option>
                            <option value="Canceled" {{ $status == 'Canceled' ? 'selected' : '' }}>Canceled</option>
                        </select>
                    </div>
                    <div class="col-span-2 md:col-span-1 flex gap-2 pt-2 md:pt-0">
                        <button type="submit" class="flex-1 px-4 py-2 text-xs font-bold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        {{-- Tombol Reset akan mengembalikan ke halaman index tanpa parameter (otomatis kembali ke 'Hari Ini') --}}
                        <a href="{{ route('jadwal.index') }}" class="px-4 py-2 text-xs font-bold text-slate-600 border border-slate-200 bg-white rounded-lg hover:bg-slate-50 transition-colors flex items-center justify-center">
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            {{-- 
              TAMPILAN DATA DESKTOP 
              Disembunyikan saat layar di bawah ukuran 'md' (768px). Menggunakan display:table murni untuk kejelasan kolom.
            --}}
            <div class="hidden md:block overflow-x-auto border border-slate-200 rounded-xl hide-scrollbar">
                <table class="min-w-full bg-white text-left border-collapse whitespace-nowrap">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase">No</th>
                            <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase">Antrean</th>
                            <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase">SAP / Pasien</th>
                            <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase">Tgl Daftar & MCU</th>
                            <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase">Dokter & Paket</th>
                            <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase">Status</th>
                            <th class="py-3 px-3 text-[10px] font-bold text-slate-500 uppercase text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($jadwals as $jadwalMcu)
                            {{-- @click memicu event JavaScript biasa untuk navigasi halaman jika baris tabel diklik --}}
                            <tr class="hover:bg-slate-50 transition-colors cursor-pointer" @click="window.location.href='{{ route('qr-patient-detail', $jadwalMcu->id) }}'">
                                <td class="py-3 px-3 text-xs text-slate-500">{{ ($jadwals->currentPage() - 1) * $jadwals->perPage() + $loop->iteration }}</td>
                                <td class="py-3 px-3 text-xs font-black text-slate-700">{{ $jadwalMcu->no_antrean ?? '-' }}</td>
                                <td class="py-3 px-3">
                                    <div class="text-xs font-black text-slate-800">
                                        @if ($jadwalMcu->karyawan_id) {{ $jadwalMcu->karyawan->nama_karyawan ?? '-' }}
                                        @elseif ($jadwalMcu->peserta_mcus_id) {{ $jadwalMcu->pesertaMcu->nama_lengkap ?? '-' }}
                                        @else {{ $jadwalMcu->nama_pasien ?? '-' }} @endif
                                    </div>
                                    <div class="text-[10px] font-mono text-slate-500 mt-0.5">SAP: {{ $jadwalMcu->no_sap ?? '-' }} | Tipe: {{ $jadwalMcu->karyawan_id ? 'Karyawan' : 'Non-PTST' }}</div>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="text-[10px] text-slate-500">Daftar: {{ \Carbon\Carbon::parse($jadwalMcu->tanggal_pendaftaran)->format('d/m/y') }}</div>
                                    <div class="text-xs font-bold text-slate-700">MCU: {{ \Carbon\Carbon::parse($jadwalMcu->tanggal_mcu)->format('d M Y') }}</div>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="text-xs font-bold text-blue-600 truncate max-w-[120px]"><i class="fas fa-user-md text-[10px] mr-1"></i>{{ $jadwalMcu->dokter->nama_lengkap ?? '-' }}</div>
                                    <div class="text-[10px] font-bold text-slate-500 mt-0.5 bg-slate-100 inline-block px-1.5 py-0.5 rounded">{{ $jadwalMcu->paketMcu->nama_paket ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 inline-flex text-[9px] font-black uppercase tracking-wider rounded border
                                        @if($jadwalMcu->status === 'Pending') bg-slate-50 text-slate-600 border-slate-200 
                                        @elseif($jadwalMcu->status === 'Scheduled') bg-amber-50 text-amber-600 border-amber-200 
                                        @elseif($jadwalMcu->status === 'Present') bg-blue-50 text-blue-600 border-blue-200 
                                        @elseif($jadwalMcu->status === 'Finished') bg-emerald-50 text-emerald-600 border-emerald-200 
                                        @elseif($jadwalMcu->status === 'Canceled') bg-red-50 text-red-600 border-red-200 @endif">
                                        {{ $jadwalMcu->status }}
                                    </span>
                                </td>
                                {{-- @click.stop mencegah event propagasi (klik baris ke halaman detail) agar tombol aksi dapat ditekan secara mandiri --}}
                                <td class="py-3 px-3 text-center relative" @click.stop>
                                    <div class="flex items-center justify-center gap-1.5">
                                        @if($jadwalMcu->status === 'Present')
                                            <form action="{{ route('jadwal.update-status', $jadwalMcu->id) }}" method="POST">
                                                @csrf <input type="hidden" name="status" value="Finished">
                                                <button type="submit" class="bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white w-7 h-7 rounded flex items-center justify-center transition-colors border border-emerald-100" title="Tandai Selesai"><i class="fas fa-check-double text-[10px]"></i></button>
                                            </form>
                                        @endif
                                        {{-- Tombol Ellipsis. Memicu fungsi openMenu() di Alpine.js root component dengan melempar parameter ID dan Status --}}
                                        <button @click.prevent="openMenu($event, {{ $jadwalMcu->id }}, '{{ $jadwalMcu->status }}')" type="button" class="w-7 h-7 rounded border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors focus:outline-none bg-white">
                                            <i class="fas fa-ellipsis-v pointer-events-none text-[10px]"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-8 text-center text-slate-400 text-xs font-medium">Belum ada jadwal yang sesuai filter.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 
              TAMPILAN DATA MOBILE (KARTU) 
              Menggantikan tabel saat dibuka di HP agar antarmuka tidak terpotong (overflow horizontal).
            --}}
            <div class="md:hidden flex flex-col gap-3">
                @forelse ($jadwals as $jadwalMcu)
                    <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm relative" @click="window.location.href='{{ route('qr-patient-detail', $jadwalMcu->id) }}'">
                        
                        {{-- Tombol Aksi di sudut kanan atas kartu --}}
                        <div class="absolute top-3 right-3 flex gap-1" @click.stop>
                            @if($jadwalMcu->status === 'Present')
                                <form action="{{ route('jadwal.update-status', $jadwalMcu->id) }}" method="POST">
                                    @csrf <input type="hidden" name="status" value="Finished">
                                    <button type="submit" class="bg-emerald-50 text-emerald-600 w-6 h-6 rounded flex items-center justify-center border border-emerald-100"><i class="fas fa-check-double text-[9px]"></i></button>
                                </form>
                            @endif
                            <button @click.prevent="openMenu($event, {{ $jadwalMcu->id }}, '{{ $jadwalMcu->status }}')" type="button" class="w-6 h-6 rounded border border-slate-200 text-slate-500 bg-white flex items-center justify-center"><i class="fas fa-ellipsis-v pointer-events-none text-[9px]"></i></button>
                        </div>

                        <div class="flex items-center gap-2 mb-2">
                            <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-[9px] font-black tracking-wider">NO: {{ $jadwalMcu->no_antrean ?? '-' }}</span>
                            <span class="px-1.5 py-0.5 text-[8px] font-black uppercase rounded border
                                @if($jadwalMcu->status === 'Pending') bg-slate-50 text-slate-600 border-slate-200 
                                @elseif($jadwalMcu->status === 'Scheduled') bg-amber-50 text-amber-600 border-amber-200 
                                @elseif($jadwalMcu->status === 'Present') bg-blue-50 text-blue-600 border-blue-200 
                                @elseif($jadwalMcu->status === 'Finished') bg-emerald-50 text-emerald-600 border-emerald-200 
                                @elseif($jadwalMcu->status === 'Canceled') bg-red-50 text-red-600 border-red-200 @endif">{{ $jadwalMcu->status }}</span>
                        </div>
                        
                        <h4 class="text-xs font-black text-slate-800 pr-16 truncate">
                            @if ($jadwalMcu->karyawan_id) {{ $jadwalMcu->karyawan->nama_karyawan ?? '-' }}
                            @elseif ($jadwalMcu->peserta_mcus_id) {{ $jadwalMcu->pesertaMcu->nama_lengkap ?? '-' }}
                            @else {{ $jadwalMcu->nama_pasien ?? '-' }} @endif
                        </h4>
                        <p class="text-[10px] font-mono text-slate-500 mb-2">SAP: {{ $jadwalMcu->no_sap ?? '-' }} | Tipe: {{ $jadwalMcu->karyawan_id ? 'Karyawan' : 'Non-PTST' }}</p>
                        
                        <div class="grid grid-cols-2 gap-2 border-t border-slate-100 pt-2 mt-2">
                            <div>
                                <span class="block text-[8px] font-bold text-slate-400 uppercase">Tanggal MCU</span>
                                <span class="text-[11px] font-bold text-slate-700">{{ \Carbon\Carbon::parse($jadwalMcu->tanggal_mcu)->format('d M Y') }}</span>
                            </div>
                            <div>
                                <span class="block text-[8px] font-bold text-slate-400 uppercase">Dokter</span>
                                <span class="text-[10px] font-bold text-blue-600 truncate block"><i class="fas fa-user-md text-[8px] mr-1"></i>{{ $jadwalMcu->dokter->nama_lengkap ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-slate-50 p-6 text-center rounded-xl border border-slate-100 text-xs font-bold text-slate-500">Belum ada jadwal.</div>
                @endforelse
            </div>
        
            {{-- Paginasi data menggunakan appends() untuk mempertahankan query string pencarian pada URL halaman berikutnya --}}
            <div class="mt-4 border-t border-slate-100 pt-4">
                {{ $jadwals->appends(request()->input())->links() }}
            </div>
        </div>
    </div>

    {{-- Overlay Latar Belakang Tembus Pandang --}}
    {{-- Mencegah bug interaksi ganda, ketika menu dropdown terbuka, klik di manapun (atau scroll) akan menutup menu tersebut --}}
    <div x-show="isOpen" 
         @click="isOpen = false"
         @scroll.window="isOpen = false"
         x-cloak 
         class="fixed inset-0 z-[9998]"></div>

    {{-- KOTAK DROPDOWN AKSI TUNGGAL --}}
    {{-- Elemen ini dirender secara absolut dan posisi top/left diinjeksikan secara dinamis via x-bind:style berdasarkan posisi kursor event --}}
    <div x-show="isOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-100" 
         x-transition:enter-start="transform opacity-0 scale-95" 
         x-transition:enter-end="transform opacity-100 scale-100" 
         x-transition:leave="transition ease-in duration-75" 
         x-transition:leave-start="transform opacity-100 scale-100" 
         x-transition:leave-end="transform opacity-0 scale-95" 
         x-bind:style="`top: ${top}px; left: ${left}px;`"
         class="fixed w-48 rounded-xl shadow-[0_10px_40px_rgb(0,0,0,0.15)] bg-white border border-slate-100 focus:outline-none z-[9999] py-1.5 overflow-hidden">

        {{-- Menggunakan element <template> Alpine.js untuk merender opsi aksi secara kondisional bergantung status data saat ini --}}
        <template x-if="currentStatus === 'Pending'">
            <form :action="`{{ route('jadwal.update-status', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', currentId)" method="POST">
                @csrf
                <input type="hidden" name="status" value="Scheduled">
                <button type="submit" class="flex items-center px-4 py-2 w-full text-left text-xs text-white bg-emerald-600 hover:bg-emerald-700 font-bold transition-colors border-b border-emerald-700">
                    <i class="fas fa-check-circle w-4 mr-2"></i> Approve Jadwal
                </button>
            </form>
        </template>

        <template x-if="currentStatus !== 'Pending' && currentStatus !== 'Finished' && currentStatus !== 'Canceled'">
            <div class="border-b border-slate-100">
                <form :action="`{{ route('jadwal.update-status', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', currentId)" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="Present">
                    <button type="submit" class="flex items-center px-4 py-2.5 w-full text-left text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                        <i class="fas fa-user-check text-blue-500 w-4 mr-2"></i> Set Present (Hadir)
                    </button>
                </form>
                <form :action="`{{ route('jadwal.update-status', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', currentId)" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="Finished">
                    <button type="submit" class="flex items-center px-4 py-2.5 w-full text-left text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                        <i class="fas fa-check-double text-emerald-500 w-4 mr-2"></i> Set Finished
                    </button>
                </form>
            </div>
        </template>

        <div class="py-1">
            <template x-if="currentStatus !== 'Finished' && currentStatus !== 'Canceled'">
                <form :action="`{{ route('jadwal.update-status', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', currentId)" method="POST" class="border-b border-slate-100 pb-1 mb-1">
                    @csrf
                    <input type="hidden" name="status" value="Canceled">
                    <button type="submit" class="flex items-center px-4 py-2 w-full text-left text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors">
                        <i class="fas fa-times-circle w-4 mr-2"></i> Batalkan Jadwal
                    </button>
                </form>
            </template>

            <a x-bind:href="`{{ route('qr-patient-detail', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', currentId)" class="flex items-center px-4 py-2.5 w-full text-left text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                <i class="fas fa-eye text-blue-500 w-4 mr-2"></i> Lihat Detail Pasien
            </a>
            <a x-bind:href="`{{ route('jadwal.edit', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', currentId)" class="flex items-center px-4 py-2.5 w-full text-left text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                <i class="fas fa-edit text-amber-500 w-4 mr-2"></i> Edit Data Jadwal
            </a>
            <form :action="`{{ route('jadwal.destroy', ['jadwal' => 'ID_PLACEHOLDER']) }}`.replace('ID_PLACEHOLDER', currentId)" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="flex items-center px-4 py-2.5 w-full text-left text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors mt-1 border-t border-slate-100" onclick="return confirm('Tindakan ini permanen. Lanjutkan penghapusan?');">
                    <i class="fas fa-trash-alt w-4 mr-2"></i> Hapus Permanen
                </button>
            </form>
        </div>
    </div>
</div>
{{-- SCRIPT JAVASCRIPT UNTUK LOADING OVERLAY --}}
<script>
    function showLoading() {
        document.getElementById('loading-overlay').style.display = 'flex';
    }
</script>
@endsection