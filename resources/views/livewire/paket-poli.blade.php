@section('title', 'Kelola Paket MCU & Poli')

<div class="container mx-auto p-1 sm:p-6 bg-gray-100 min-h-screen">
    <div class="bg-white rounded-xl sm:rounded-3xl shadow-lg sm:shadow-2xl p-4 sm:p-8 border border-gray-200">
        <h1 class="text-xl sm:text-2xl font-bold mb-4 text-gray-800">Kelola Paket & Poli</h1>

        <div class="border-b border-gray-200 mb-6 sm:mb-8">
            <nav class="-mb-px flex flex-wrap justify-between sm:justify-start sm:space-x-6 space-x-2">
                <button wire:click="$set('activeTab', 'paket')" class="flex-1 text-center sm:flex-none py-2 px-1 text-xs sm:text-sm font-semibold border-b-2 focus:outline-none transition-colors duration-200 {{ $activeTab === 'paket' ? 'border-red-600 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Manajemen Paket MCU
                </button>
                <button wire:click="$set('activeTab', 'poli')" class="flex-1 text-center sm:flex-none py-2 px-1 text-xs sm:text-sm font-semibold border-b-2 focus:outline-none transition-colors duration-200 {{ $activeTab === 'poli' ? 'border-red-600 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Manajemen Poli
                </button>
                <button wire:click="$set('activeTab', 'hubungkan')" class="flex-1 text-center sm:flex-none py-2 px-1 text-xs sm:text-sm font-semibold border-b-2 focus:outline-none transition-colors duration-200 {{ $activeTab === 'hubungkan' ? 'border-red-600 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Hubungkan
                </button>
            </nav>
        </div>

        <div>
            @if ($activeTab === 'paket')
                <h2 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 text-gray-800">Manajemen Paket MCU</h2>
                
                <div class="p-4 sm:p-6 bg-gray-50 rounded-xl border border-gray-200 mb-6 sm:mb-8 shadow-sm">
                    <h3 class="font-semibold text-gray-700 mb-4">Tambah Paket Baru</h3>
                    <form wire:submit.prevent="savePaket">
                        <div class="flex flex-col md:flex-row items-end gap-3 sm:gap-4"> 
                            <div class="flex-1 w-full">
                                <label for="nama_paket" class="block text-sm font-medium text-gray-700 mb-1">Nama Paket</label>
                                <input type="text" wire:model="nama_paket" id="nama_paket" class="block w-full px-4 py-2 rounded-lg sm:rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 placeholder-gray-400 text-sm">
                                @error('nama_paket') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <button type="submit" class="w-full md:w-auto px-6 py-2 bg-red-600 text-white rounded-lg sm:rounded-xl font-medium hover:bg-red-700 transition-colors duration-200 shadow text-sm">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>

                <div class="p-4 sm:p-6 bg-gray-50 rounded-xl border border-gray-200 shadow-sm">
                    <h3 class="font-semibold text-gray-700 mb-4">Daftar Paket MCU</h3>
                    <div class="overflow-x-auto rounded-lg sm:rounded-xl shadow-md">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 sm:px-6 sm:py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Paket</th>
                                    <th class="px-4 py-2 sm:px-6 sm:py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($paketList as $paket)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-4 py-3 sm:px-6 sm:py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $paket->nama_paket }}</td>
                                        <td class="px-4 py-3 sm:px-6 sm:py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <button wire:click="deletePaket({{ $paket->id }})" wire:confirm="Apakah Anda yakin ingin menghapus paket ini?" class="text-red-600 hover:text-red-900 transition-colors duration-200">Hapus</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-4 py-3 sm:px-6 sm:py-4 text-center text-sm text-gray-500 italic">Belum ada paket yang terdaftar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            @elseif ($activeTab === 'poli')
                <h2 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 text-gray-800">Manajemen Poli</h2>
                
                <div class="p-4 sm:p-6 bg-gray-50 rounded-xl border border-gray-200 mb-6 sm:mb-8 shadow-sm">
                    <h3 class="font-semibold text-gray-700 mb-4">Tambah Poli Baru</h3>
                    <form wire:submit.prevent="savePoli">
                        <div class="flex flex-col md:flex-row items-end gap-3 sm:gap-4"> 
                            <div class="flex-1 w-full">
                                <label for="nama_poli" class="block text-sm font-medium text-gray-700 mb-1">Nama Poli</label>
                                <input type="text" wire:model="nama_poli" id="nama_poli" class="block w-full px-4 py-2 rounded-lg sm:rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 placeholder-gray-400 text-sm">
                                @error('nama_poli') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <button type="submit" class="w-full md:w-auto px-6 py-2 bg-red-600 text-white rounded-lg sm:rounded-xl font-medium hover:bg-red-700 transition-colors duration-200 shadow text-sm">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>

                <div class="p-4 sm:p-6 bg-gray-50 rounded-xl border border-gray-200 shadow-sm">
                    <h3 class="font-semibold text-gray-700 mb-4">Daftar Poli</h3>
                    <div class="space-y-2">
                        @forelse ($poliList as $poli)
                            <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-gray-200 shadow-sm hover:bg-gray-50 transition-colors duration-150">
                                <span class="text-sm font-medium text-gray-900">{{ $poli->nama_poli }}</span>
                                <button wire:click="deletePoli({{ $poli->id }})" wire:confirm="Apakah Anda yakin ingin menghapus poli ini?" class="text-red-600 hover:text-red-900 transition-colors duration-200 text-sm py-1 px-2 rounded-md">Hapus</button>
                            </div>
                        @empty
                            <p class="text-center text-sm text-gray-500 italic p-3">Belum ada poli yang terdaftar.</p>
                        @endforelse
                    </div>
                </div>

            @elseif ($activeTab === 'hubungkan')
                <h2 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 text-gray-800">Hubungkan Paket dengan Poli</h2>
                
                <div class="p-4 sm:p-6 bg-gray-50 rounded-xl border border-gray-200 mb-6 sm:mb-8 shadow-sm">
                    <h3 class="font-semibold text-gray-700 mb-4">Hubungkan Paket dengan Poli</h3>
                    <form wire:submit.prevent="attachPoliToPaket">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label for="paket_mcus" class="block text-sm font-medium text-gray-700 mb-1">Pilih Paket MCU</label>
                                <select wire:model="paket_mcus_id" id="paket_mcus" class="block w-full px-4 py-2 rounded-lg sm:rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm">
                                    <option value="">-- Pilih Paket --</option>
                                    @foreach ($daftarPaket as $paket)
                                        <option value="{{ $paket->id }}">{{ $paket->nama_paket }}</option>
                                    @endforeach
                                </select>
                                @error('paket_mcus_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Poli</label>
                                <div class="bg-white p-3 sm:p-4 rounded-lg sm:rounded-xl border border-gray-300 grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-48 overflow-y-auto">
                                    @forelse ($daftarPoli as $poli)
                                        <label class="flex items-center text-gray-700 cursor-pointer text-sm">
                                            <input type="checkbox" wire:model="poli_ids" value="{{ $poli->id }}" class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                            <span class="ml-2">{{ $poli->nama_poli }}</span>
                                        </label>
                                    @empty
                                         <p class="text-center text-sm text-gray-500 italic p-3 col-span-1 sm:col-span-2">Belum ada poli yang terdaftar.</p>
                                    @endforelse
                                </div>
                                @error('poli_ids') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex justify-start items-end mt-4 md:mt-0 col-span-1">
                                <button type="submit" class="w-full md:w-auto px-6 py-2 bg-red-600 text-white rounded-lg sm:rounded-xl font-medium hover:bg-red-700 transition-colors duration-200 shadow text-sm">
                                    Hubungkan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="p-4 sm:p-6 bg-gray-50 rounded-xl border border-gray-200 shadow-sm">
                    <h3 class="font-semibold text-gray-700 mb-4">Daftar Poli per Paket</h3>
                    <div class="space-y-4">
                        @forelse ($daftarPaket as $paket)
                            <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-200">
                                <h4 class="font-bold text-base text-gray-800">{{ $paket->nama_paket }}</h4>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @forelse ($paket->poli as $poli)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-800 border border-gray-300">
                                            {{ $poli->nama_poli }}
                                            <button wire:click="detachPoliFromPaket({{ $paket->id }}, {{ $poli->id }})" class="ml-1 -mr-0.5 h-4 w-4 text-gray-500 hover:text-gray-800" wire:confirm="Apakah Anda yakin ingin menghapus poli ini dari paket?">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </span>
                                    @empty
                                        <span class="text-sm text-gray-500 italic">Belum ada poli yang terhubung.</span>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 italic">Belum ada paket yang terdaftar.</p>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@livewireScripts
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('dataSaved', (event) => {
            const { message } = event[0];
            Swal.fire({
                title: 'Berhasil! 🎉',
                text: message,
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc2626'
            });
        });

        Livewire.on('dataGagal', (event) => {
            const { message } = event[0];
            Swal.fire({
                title: 'Gagal! 😟',
                text: message,
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc2626'
            });
        });
        Livewire.on('confirmDelete', (event) => {
            const { message, method, params } = event[0];
            Swal.fire({
                title: 'Konfirmasi Penghapusan ⚠️',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.emit(method, ...params);
                }
            });
        });
    });
</script>