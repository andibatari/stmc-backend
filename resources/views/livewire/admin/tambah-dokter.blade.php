@section('title', 'Manajemen Layanan / Tambah Dokter')

<div>
    <h2 class="text-2xl font-bold mx-4 md:mx-8 text-gray-800">
        @if ($isEditing)
            Edit Dokter
        @else
            Tambah Dokter Baru
        @endif
    </h2>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 mx-4 md:mx-8" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <form wire:submit.prevent="{{ $isEditing ? 'update' : 'save' }}" class="space-y-6">
        <div class="mx-4 md:mx-8 p-6 bg-white rounded-xl shadow-lg border border-gray-100">
            <h4 class="text-xl font-bold text-gray-800 mb-6 border-b pb-4">
                Informasi Akun & Data Dokter
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Input Nama Lengkap -->
                <div>
                    <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" id="nama_lengkap" wire:model="nama_lengkap" class="block w-full px-4 py-2.5 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('nama_lengkap') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <!-- Input NIK Dokter -->
                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK Dokter</label>
                    <input type="text" id="nik" wire:model="nik" class="block w-full px-4 py-2.5 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('nik') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <!-- Dropdown Spesialisasi -->
                <div>
                    <label for="spesialisasi" class="block text-sm font-medium text-gray-700 mb-1">Spesialisasi</label>
                    <select id="spesialisasi" wire:model="spesialisasi" class="block w-full px-4 py-2.5 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        <option value="">Pilih Spesialisasi</option>
                        @foreach ($daftarSpesialisasi as $spesialisasi)
                            <option value="{{ $spesialisasi }}">{{ $spesialisasi }}</option>
                        @endforeach
                    </select>
                    @error('spesialisasi') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <!-- Input Tanggal Lahir -->
                <div>
                    <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                    <input type="date" id="tanggal_lahir" wire:model="tanggal_lahir" class="block w-full px-4 py-2.5 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('tanggal_lahir') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <!-- Input Golongan Darah -->
                <div>
                    <label for="golongan_darah" class="block text-sm font-medium text-gray-700 mb-1">Golongan Darah</label>
                    <input type="text" id="golongan_darah" wire:model="golongan_darah" class="block w-full px-4 py-2.5 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('golongan_darah') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <!-- Input No. HP -->
                <div>
                    <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                    <input type="text" id="no_hp" wire:model="no_hp" class="block w-full px-4 py-2.5 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('no_hp') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <!-- Input Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" wire:model="email" class="block w-full px-4 py-2.5 rounded-md border border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

            </div>
        </div>

        <div class="flex justify-end mt-8 mx-4 md:mx-8">
            @if ($isEditing)
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-md shadow-lg transition duration-150 ease-in-out mr-2">
                    <span wire:loading.remove>Update Dokter</span>
                    <span wire:loading>Memperbarui...</span>
                </button>
                <button type="button" wire:click="cancelEdit" class="bg-gray-400 hover:bg-gray-500 text-gray-800 font-bold py-3 px-8 rounded-md shadow-lg transition duration-150 ease-in-out">
                    Batal
                </button>
            @else
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-md shadow-lg transition duration-150 ease-in-out">
                    <span wire:loading.remove>Simpan Dokter</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            @endif
        </div>
    </form>

    <div class="mx-4 md:mx-8 p-6 bg-white rounded-xl shadow-lg border border-gray-100 mt-8">
        <h4 class="text-xl font-bold text-gray-800 mb-4">Daftar Dokter</h4>
        <div class="overflow-x-auto">
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