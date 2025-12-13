@extends('layouts.app')

@section('title', 'Kelola Profil Admin')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Kelola Profil Anda</h1>
    <p class="text-gray-600 mb-8">Perbarui informasi akun, termasuk nama, email, kata sandi, dan foto profil.</p>

    {{-- Pesan Sukses --}}
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    
    {{-- Pesan Error --}}
    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg" role="alert">
            <p class="font-bold">Gagal menyimpan perubahan!</p>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Card Profil --}}
    <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-200">
        {{-- KRITIS: Tambahkan method PUT dan enctype untuk file upload --}}
        <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Kolom Kiri: Foto Profil --}}
                <div class="lg:col-span-1 border-r lg:pr-8 pr-0 border-gray-100">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Foto Profil</h2>
                    <div class="flex flex-col items-center">
                        {{-- Tampilkan Foto Profil Saat Ini atau Inisial --}}
                        <div class="mb-4">
                            @php
                                // use Illuminate\Support\Facades\Storage; // Tambahkan ini di sini atau di bagian atas file

                                // // Tentukan URL foto profil
                                // $imageUrl = $admin->foto_profil
                                //     // Menggunakan Storage::url() yang menangani awalan 'public/' secara otomatis
                                //     ? Storage::url($admin->foto_profil) . '?t=' . now()->timestamp // Tambah cache busting
                                //     // Fallback ke UI Avatar jika tidak ada foto
                                //     : 'https://ui-avatars.com/api/?name=' . urlencode($admin->nama_lengkap ?? 'Admin') . '&color=FFFFFF&background=DC2626&size=128';

                                $rawPath = $admin->foto_profil;

                                $imageUrl = $rawPath
                                    // Gunakan asset() dan tambahkan awalan 'storage/' secara manual
                                    // Contoh: asset('storage/admin_photos/nama_file.jpg')
                                    ? asset('storage/' . $rawPath) . '?t=' . now()->timestamp 
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($admin->nama_lengkap ?? 'Admin') . '&color=FFFFFF&background=DC2626&size=128';
                            @endphp
                            
                            <img src="{{ $imageUrl }}" 
                                alt="Foto Profil" 
                                id="profileImagePreview"
                                class="h-32 w-32 object-cover rounded-full border-4 border-red-200 shadow-md">
                        </div>
                        
                        {{-- Input Unggah File --}}
                        <div class="w-full max-w-xs">
                            <label for="foto_profil" class="block text-sm font-medium text-gray-700 mb-1">Pilih Foto Baru (Max 2MB)</label>
                            <input type="file" name="foto_profil" id="foto_profil" 
                                class="w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-red-50 file:text-red-700
                                hover:file:bg-red-100
                            ">
                            @error('foto_profil')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Informasi Dasar & Password --}}
                <div class="lg:col-span-2">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Detail Akun</h2>

                    {{-- Nama Lengkap --}}
                    <div class="mb-4">
                        <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                value="{{ old('nama_lengkap', $admin->nama_lengkap) }}" required>
                        @error('nama_lengkap')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email (Username Login)</label>
                        <input type="email" name="email" id="email" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                value="{{ old('email', $admin->email) }}" required>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <h2 class="text-xl font-semibold text-gray-800 mb-4 border-t pt-4">Ganti Kata Sandi</h2>

                    {{-- Password Baru --}}
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi Baru (Kosongkan jika tidak ingin diubah)</label>
                        <input type="password" name="password" id="password" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    </div>
                </div>
            </div>

            {{-- Tombol Simpan --}}
            <div class="mt-8 pt-4 border-t border-gray-200 flex justify-end">
                <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Fungsionalitas preview gambar
    document.getElementById('foto_profil').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profileImagePreview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
