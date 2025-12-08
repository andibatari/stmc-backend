@section('title', 'Tambah Admin')

{{-- OUTER CONTAINER: Menghapus margin horizontal di mobile --}}
<div class="py-4 px-2 sm:px-4 md:py-8 md:px-6 lg:max-w-7xl lg:mx-auto">
    
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4">
        @if ($isEditing)
            Edit Admin
        @else
            Tambah Admin
        @endif
    </h2>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 text-sm" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    {{-- FORM TAMBAH/EDIT AKUN --}}
    <form wire:submit.prevent="{{ $isEditing ? 'update' : 'save' }}" class="space-y-6">
        <div class="p-4 sm:p-6 bg-white rounded-xl shadow-lg border border-gray-100">
            
            <h4 class="text-lg font-bold text-gray-800 mb-4 border-b pb-3">Informasi Akun</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                
                <div class="space-y-1">
                    <label for="role" class="block text-xs font-medium text-gray-700">Role</label>
                    <select id="role" wire:model.live="role" class="mt-1 block w-full px-3 py-2 text-sm rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        <option value="admin">Admin</option>
                        <option value="dokter">Dokter</option>
                        <option value="karyawan">Karyawan</option>
                    </select>
                    @error('role') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div class="relative space-y-1">
                    <label for="nama_lengkap" class="block text-xs font-medium text-gray-700">Nama Lengkap</label>
                    
                    @if ($role === 'dokter')
                        <select id="nama_lengkap" wire:model.live="selectedDokterId" class="block w-full px-3 py-2 text-sm rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            <option value="">Pilih Dokter</option>
                            @foreach ($listDokter as $id => $nama)
                                <option value="{{ $id }}">{{ $nama }}</option>
                            @endforeach
                        </select>
                    @elseif ($role === 'karyawan')
                        <input type="text" id="searchQuery" wire:model.live.debounce.300ms="searchQuery" placeholder="Cari No. SAP atau Nama Karyawan..."
                                class="block w-full px-3 py-2 text-sm rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        
                        @if (!empty($searchQuery) && count($searchedKaryawans) > 0)
                            <div class="absolute z-10 w-full bg-white border border-gray-300 mt-1 rounded-md shadow-lg max-h-48 overflow-y-auto">
                                @foreach ($searchedKaryawans as $karyawan)
                                    <p wire:click="selectKaryawan({{ $karyawan->id }})" class="p-2 cursor-pointer hover:bg-gray-100 text-sm">
                                        {{ $karyawan->no_sap }} - {{ $karyawan->nama_karyawan }}
                                    </p>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <input type="text" id="nama_lengkap" wire:model="nama_lengkap" class="block w-full px-3 py-2 text-sm rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @endif
                    
                    {{-- Pesan Status Terpilih --}}
                    @if ($selectedKaryawanId || $selectedDokterId)
                         <p class="text-xs text-green-600 mt-1">Data terpilih: {{ $nama_lengkap }} ({{ $no_sap }})</p>
                    @endif
                    @error('nama_lengkap') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                
                <div class="space-y-1">
                    <label for="no_sap" class="block text-xs font-medium text-gray-700">No. SAP</label>
                    <input type="text" id="no_sap" wire:model="no_sap" 
                            class="block w-full px-3 py-2 text-sm rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 
                            {{ ($role !== 'admin' && $selectedKaryawanId || $selectedDokterId) ? 'bg-gray-100' : '' }}" 
                            {{ ($role !== 'admin' && $selectedKaryawanId || $selectedDokterId) ? 'disabled' : '' }}>
                    @error('no_sap') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                
                <div class="space-y-1">
                    <label for="nik" class="block text-xs font-medium text-gray-700">NIK</label>
                    <input type="text" id="nik" wire:model="nik" 
                            class="block w-full px-3 py-2 text-sm rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 bg-gray-100" disabled>
                    @error('nik') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                
                <div class="space-y-1">
                    <label for="email" class="block text-xs font-medium text-gray-700">Email</label>
                    <input type="email" id="email" wire:model="email" 
                            class="block w-full px-3 py-2 text-sm rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 {{ ($role !== 'admin') ? 'bg-gray-100' : '' }}" 
                            {{ ($role !== 'admin') ? 'disabled' : '' }}>
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
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-md shadow-lg transition duration-150 ease-in-out mr-2 text-sm">
                    <span wire:loading.remove>Update Admin</span>
                    <span wire:loading>Memperbarui...</span>
                </button>
                <button type="button" wire:click="cancelEdit" class="bg-gray-400 hover:bg-gray-500 text-gray-800 font-bold py-2 px-6 rounded-md shadow-lg transition duration-150 ease-in-out text-sm">
                    Batal
                </button>
            @else
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-md shadow-lg transition duration-150 ease-in-out text-sm">
                    <span wire:loading.remove>Simpan Admin</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            @endif
        </div>
    </form>
    
    {{-- DAFTAR ADMIN (Diubah menjadi Card View Sederhana di Mobile) --}}
    <div class="p-4 sm:p-6 bg-white rounded-xl shadow-lg border border-gray-100 mt-8">
        <h4 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Daftar Admin</h4>
        
        {{-- MOBILE CARD VIEW --}}
        <div class="md:hidden space-y-3">
            @forelse ($this->adminUsers as $admin)
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 shadow-sm text-xs space-y-1">
                    <p class="font-bold text-gray-900">{{ $admin->nama_lengkap }}</p>
                    <p>Role: <span class="font-semibold text-red-600">{{ $admin->role }}</span></p>
                    <p>Email: {{ $admin->email }}</p>
                    <p>SAP: {{ $admin->no_sap ?? 'N/A' }}</p>
                    <p class="text-gray-500">Bergabung: {{ $admin->created_at->format('d-m-Y') }}</p>
                    
                    <div class="flex flex-wrap gap-2 pt-2 border-t mt-2">
                        <button wire:click="edit({{ $admin->id }})" class="text-xs text-indigo-600 hover:text-indigo-900">Edit</button>
                        <button wire:click="editPassword({{ $admin->id }})" class="text-xs text-indigo-600 hover:text-indigo-900">Edit Password</button>
                        <button onclick="return confirm('Apakah Anda yakin ingin menghapus admin ini?')" wire:click="delete({{ $admin->id }})" class="text-xs text-red-600 hover:text-red-900">Hapus</button>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 text-sm p-4">Tidak ada data admin.</p>
            @endforelse
        </div>

        {{-- DESKTOP TABLE VIEW --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. SAP</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bergabung</th>
                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($this->adminUsers as $admin)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $admin->nama_lengkap }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $admin->no_sap ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $admin->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $admin->role }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $admin->created_at->format('d-m-Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="edit({{ $admin->id }})" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</button>
                                <button wire:click="editPassword({{ $admin->id }})" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit Password</button>
                                <button onclick="return confirm('Apakah Anda yakin ingin menghapus admin ini?')" wire:click="delete({{ $admin->id }})" class="text-red-600 hover:text-red-900">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data admin.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $this->adminUsers->links() }}
        </div>
    </div>
    
    {{-- Modal Edit Password (Disesuaikan untuk Mobile) --}}
    @if ($editPasswordId)
        <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full sm:my-8 sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="updatePassword">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4 border-b pb-2">
                                Edit Password Admin
                            </h3>
                            <div>
                                <label for="newPassword" class="block text-sm font-medium text-gray-700">Password Baru</label>
                                <input type="password" id="newPassword" wire:model="newPassword" class="mt-1 block w-full px-3 py-2 text-sm rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                @error('newPassword') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-sm font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto">
                                Update Password
                            </button>
                            <button type="button" wire:click="$set('editPasswordId', null)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>