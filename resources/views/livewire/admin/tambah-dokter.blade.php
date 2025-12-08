@section('title', 'Tambah Dokter')

{{-- OUTER CONTAINER: Padding vertikal dan horizontal yang disesuaikan --}}
<div class="py-4 px-2 sm:px-4 md:py-8 md:px-6 lg:max-w-6xl lg:mx-auto">
    
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4">
        @if ($isEditing)
            Edit Dokter
        @else
            Tambah Dokter Baru
        @endif
    </h2>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 text-sm" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    {{-- FORM TAMBAH/EDIT DOKTER --}}
    <form wire:submit.prevent="{{ $isEditing ? 'update' : 'save' }}" class="space-y-6">
        <div class="p-4 sm:p-6 bg-white rounded-xl shadow-lg border border-gray-100">
            
            <h4 class="text-lg font-bold text-red-600 mb-4 border-b pb-3">
                Informasi Akun & Data Dokter
            </h4>
            {{-- Mengubah grid-cols-3 menjadi grid-cols-1 md:grid-cols-2 --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                
                <div class="space-y-1 md:col-span-2">
                    <label for="nama_lengkap" class="block text-xs font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" id="nama_lengkap" wire:model="nama_lengkap" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('nama_lengkap') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                
                <div class="space-y-1">
                    <label for="nik" class="block text-xs font-medium text-gray-700">NIK Dokter</label>
                    <input type="text" id="nik" wire:model="nik" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('nik') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                
                <div class="space-y-1">
                    <label for="spesialisasi" class="block text-xs font-medium text-gray-700">Spesialisasi</label>
                    <select id="spesialisasi" wire:model="spesialisasi" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        <option value="">Pilih Spesialisasi</option>
                        @foreach ($daftarSpesialisasi as $spesialisasi)
                            <option value="{{ $spesialisasi }}">{{ $spesialisasi }}</option>
                        @endforeach
                    </select>
                    @error('spesialisasi') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                
                <div class="space-y-1">
                    <label for="tanggal_lahir" class="block text-xs font-medium text-gray-700">Tanggal Lahir</label>
                    <input type="date" id="tanggal_lahir" wire:model="tanggal_lahir" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('tanggal_lahir') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                
                <div class="space-y-1">
                    <label for="golongan_darah" class="block text-xs font-medium text-gray-700">Golongan Darah</label>
                    <input type="text" id="golongan_darah" wire:model="golongan_darah" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('golongan_darah') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                
                <div class="space-y-1">
                    <label for="no_hp" class="block text-xs font-medium text-gray-700">No. HP</label>
                    <input type="text" id="no_hp" wire:model="no_hp" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('no_hp') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                
                <div class="space-y-1">
                    <label for="email" class="block text-xs font-medium text-gray-700">Email</label>
                    <input type="email" id="email" wire:model="email" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                @if (!$isEditing)
                    <div class="space-y-1">
                        <label for="password" class="block text-xs font-medium text-gray-700">Password</label>
                        <input type="password" id="password" wire:model="password" class="block w-full px-3 py-2 text-sm rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                @endif
            </div>
        </div>

        {{-- TOMBOL AKSI --}}
        <div class="flex justify-end mt-8">
            @if ($isEditing)
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-md shadow-lg transition duration-150 ease-in-out mr-2 text-sm w-full md:w-auto">
                    <span wire:loading.remove>Update Dokter</span>
                    <span wire:loading>Memperbarui...</span>
                </button>
                <button type="button" wire:click="cancelEdit" class="bg-gray-400 hover:bg-gray-500 text-gray-800 font-bold py-2 px-6 rounded-md shadow-lg transition duration-150 ease-in-out text-sm w-full md:w-auto mt-2 md:mt-0">
                    Batal
                </button>
            @else
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-md shadow-lg transition duration-150 ease-in-out text-sm w-full md:w-auto">
                    <span wire:loading.remove>Simpan Dokter</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            @endif
        </div>
    </form>

    {{-- DAFTAR DOKTER --}}
    <div class="p-4 sm:p-6 bg-white rounded-xl shadow-lg border border-gray-100 mt-8">
        <h4 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Daftar Dokter</h4>
        
        {{-- MOBILE CARD VIEW --}}
        <div class="md:hidden space-y-3">
            @forelse ($this->dokterUsers as $dokter)
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 shadow-sm text-xs space-y-1">
                    <p class="font-bold text-gray-900">{{ $dokter->nama_lengkap }} ({{ $dokter->spesialisasi }})</p>
                    <p>NIK: {{ $dokter->nik }}</p>
                    <p>Tgl Lahir: {{ \Carbon\Carbon::parse($dokter->tanggal_lahir)->format('d-m-Y') }}</p>
                    <p>Email: {{ $dokter->email }}</p>
                    
                    <div class="flex flex-wrap gap-2 pt-2 border-t mt-2">
                        <button type="button" wire:click="edit({{ $dokter->id }})" class="text-xs text-indigo-600 hover:text-indigo-900">Edit</button>
                        <button type="button" wire:click="delete({{ $dokter->id }})" onclick="return confirm('Apakah Anda yakin ingin menghapus dokter ini?')" class="text-xs text-red-600 hover:text-red-900">Hapus</button>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 text-sm p-4">Belum ada dokter yang terdaftar.</p>
            @endforelse
        </div>

        {{-- DESKTOP TABLE VIEW --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK Dokter</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spesialisasi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Lahir</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Golongan Darah</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. HP</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($this->dokterUsers as $dokter)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $dokter->nik }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $dokter->nama_lengkap }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $dokter->spesialisasi }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($dokter->tanggal_lahir)->format('d-m-Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $dokter->golongan_darah }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $dokter->no_hp }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $dokter->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $dokter->role }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button type="button" wire:click="edit({{ $dokter->id }})" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</button>
                            <button type="button" wire:click="delete({{ $dokter->id }})" class="text-red-600 hover:text-red-900">Hapus</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Belum ada dokter yang terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $this->dokterUsers->links() }}
        </div>
    </div>
</div>