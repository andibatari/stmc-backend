<div class="px-4 md:px-8 py-8 min-h-screen" x-data="{ activeTab: 'laporan' }">
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Pengaturan Sistem STMC</h1>
            <p class="text-sm text-slate-500 mt-1">Konfigurasi laporan PDF, antrean pendaftaran, dan keamanan sistem.</p>
        </div>
        <button wire:click="simpanPengaturan" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg transition-all">
            <i class="fas fa-save mr-2"></i> Simpan Pengaturan
        </button>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-sm text-emerald-800 font-bold">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Menu Tab --}}
    <div class="flex space-x-2 mb-6 border-b border-slate-200">
        <button @click="activeTab = 'laporan'" :class="activeTab === 'laporan' ? 'border-b-4 border-red-500 text-red-600 font-bold' : 'text-slate-500 hover:bg-slate-50'" class="px-6 py-3 transition-colors flex items-center">
            <i class="fas fa-file-pdf mr-2"></i> Kop & Laporan
        </button>
        <button @click="activeTab = 'pendaftaran'" :class="activeTab === 'pendaftaran' ? 'border-b-4 border-red-500 text-red-600 font-bold' : 'text-slate-500 hover:bg-slate-50'" class="px-6 py-3 transition-colors flex items-center">
            <i class="fas fa-qrcode mr-2"></i> Pendaftaran QR
        </button>
        <button @click="activeTab = 'sistem'" :class="activeTab === 'sistem' ? 'border-b-4 border-red-500 text-red-600 font-bold' : 'text-slate-500 hover:bg-slate-50'" class="px-6 py-3 transition-colors flex items-center">
            <i class="fas fa-shield-alt mr-2"></i> Keamanan & Backup
        </button>
    </div>

    {{-- TAB 1: LAPORAN --}}
    <div x-show="activeTab === 'laporan'" class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100" x-transition>
        <h3 class="text-lg font-bold text-slate-800 mb-6 border-b pb-2">Kustomisasi Template PDF</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Nama Kepala Klinik</label>
                <input type="text" wire:model.defer="nama_kepala_klinik" class="w-full border-slate-200 rounded-xl focus:border-red-500 focus:ring-red-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Teks Disclaimer PDF</label>
                <textarea wire:model.defer="teks_disclaimer" rows="3" class="w-full border-slate-200 rounded-xl focus:border-red-500 focus:ring-red-500 text-sm"></textarea>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Ganti Logo STMC (Kiri)</label>
                <img src="{{ asset($current_logo_stmc) }}" class="h-16 mb-3 bg-white p-2 rounded border">
                <input type="file" wire:model="logo_stmc" class="text-sm">
            </div>
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Ganti Logo Tonasa (Kanan)</label>
                <img src="{{ asset($current_logo_tonasa) }}" class="h-16 mb-3 bg-white p-2 rounded border">
                <input type="file" wire:model="logo_tonasa" class="text-sm">
            </div>
        </div>
    </div>

    {{-- TAB 2: PENDAFTARAN --}}
    <div x-show="activeTab === 'pendaftaran'" style="display: none;" class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100" x-transition>
        <h3 class="text-lg font-bold text-slate-800 mb-6 border-b pb-2">Aturan Pendaftaran via QR Code</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Batas Kuota Harian</label>
                <input type="number" wire:model.defer="kuota_harian" class="w-full border-slate-200 rounded-xl focus:border-red-500 focus:ring-red-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Jam Buka</label>
                <input type="time" wire:model.defer="jam_buka" class="w-full border-slate-200 rounded-xl focus:border-red-500 focus:ring-red-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Jam Tutup</label>
                <input type="time" wire:model.defer="jam_tutup" class="w-full border-slate-200 rounded-xl focus:border-red-500 focus:ring-red-500 text-sm">
            </div>
        </div>
    </div>

    {{-- TAB 3: SISTEM --}}
    <div x-show="activeTab === 'sistem'" style="display: none;" class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100" x-transition>
        <h3 class="text-lg font-bold text-slate-800 mb-6 border-b pb-2">Kontrol Keamanan Sistem</h3>
        <div class="flex flex-col md:flex-row gap-8">
            <div class="bg-rose-50 border border-rose-200 p-6 rounded-2xl flex-1">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-bold text-rose-800"><i class="fas fa-power-off mr-2"></i> Mode Pemeliharaan</h4>
                    <input type="checkbox" wire:model.defer="maintenance_mode" class="w-6 h-6 text-rose-600 border-gray-300 rounded focus:ring-rose-500">
                </div>
                <p class="text-xs text-rose-600">Jika dicentang, halaman QR Code pendaftaran pasien akan diblokir sementara.</p>
            </div>
            <div class="bg-blue-50 border border-blue-200 p-6 rounded-2xl flex-1 text-center">
                <h4 class="font-bold text-blue-800 mb-2"><i class="fas fa-database mr-2"></i> Backup Database</h4>
                <p class="text-xs text-blue-600 mb-4">Unduh seluruh rekaman data (.sql) ke komputer.</p>
                <button wire:click="backupDatabase" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg text-sm transition-all">
                    Mulai Backup
                </button>
            </div>
        </div>
    </div>
</div>