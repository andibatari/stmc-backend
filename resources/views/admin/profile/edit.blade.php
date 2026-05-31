@extends('layouts.app')

@section('title', 'Kelola Profil Admin')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    {{-- Header Halaman --}}
    <div class="mb-8">
        <h1 class="text-2xl font-black text-slate-800 tracking-tight">Pengaturan Profil</h1>
        <p class="text-sm text-slate-500 mt-1">Kelola informasi pribadi, email, dan keamanan kata sandi akun Anda.</p>
    </div>

    {{-- Pesan Sukses --}}
    @if (session('success'))
        <div class="mb-6 flex items-center bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-sm" role="alert">
            <i class="fas fa-check-circle text-emerald-500 text-xl mr-3"></i>
            <div>
                <p class="text-sm font-bold text-emerald-800">Berhasil!</p>
                <p class="text-sm text-emerald-600">{{ session('success') }}</p>
            </div>
        </div>
    @endif
    
    {{-- Pesan Error --}}
    @if ($errors->any())
        <div class="mb-6 bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-xl shadow-sm" role="alert">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-triangle text-rose-500 text-lg mr-2"></i>
                <p class="text-sm font-bold text-rose-800">Gagal menyimpan perubahan!</p>
            </div>
            <ul class="text-sm text-rose-600 list-disc list-inside ml-6">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Card Profil Utama --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-12">
                
                {{-- KIRI: Bagian Foto Profil --}}
                <div class="lg:col-span-4 bg-slate-50/50 border-r border-slate-100 p-8 flex flex-col items-center justify-center text-center">
                    <h2 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-6">Foto Profil</h2>
                    
                    <div class="relative inline-block mb-4 group">
                        {{-- Preview Foto --}}
                        @php
                            $imageUrl = null;
                            if (isset($admin) && $admin->foto_profil) {
                                if (str_starts_with($admin->foto_profil, 'admin_photos/')) {
                                    $imageUrl = asset($admin->foto_profil) . '?t=' . now()->timestamp;
                                } else {
                                    $imageUrl = asset('storage/' . $admin->foto_profil) . '?t=' . now()->timestamp;
                                }
                            } else {
                                $imageUrl = 'https://ui-avatars.com/api/?name=' . urlencode($admin->nama_lengkap ?? 'Admin') . '&background=fecaca&color=b91c1c&size=200';
                            }
                        @endphp
                        
                        <div class="w-36 h-36 rounded-full p-1 bg-white border-2 border-slate-200 shadow-md">
                            <img src="{{ $imageUrl }}" 
                                alt="Foto Profil" 
                                id="profileImagePreview"
                                class="w-full h-full object-cover rounded-full">
                        </div>

                        {{-- Tombol Edit Mengambang (Hidden File Input) --}}
                        <label for="foto_profil" class="absolute bottom-2 right-2 bg-red-600 hover:bg-red-700 text-white p-2.5 rounded-full shadow-lg cursor-pointer transition-all duration-200 hover:scale-110 border-2 border-white ring-2 ring-transparent focus-within:ring-red-500">
                            <i class="fas fa-camera text-sm"></i>
                        </label>
                        <input type="file" name="foto_profil" id="foto_profil" class="hidden" accept="image/jpeg,image/png,image/jpg">
                    </div>
                    
                    <h3 class="text-lg font-bold text-slate-800">{{ $admin->nama_lengkap ?? 'Administrator' }}</h3>
                    <p class="text-xs text-slate-500 mt-1">Admin STMC</p>
                    <p class="text-[10px] font-semibold text-slate-400 mt-4 bg-white px-3 py-1.5 rounded-lg border border-slate-200 shadow-sm">
                        JPG, JPEG, PNG (Maks 2MB)
                    </p>
                    
                    @error('foto_profil')
                        <p class="text-rose-500 text-xs font-bold mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- KANAN: Form Detail Akun --}}
                <div class="lg:col-span-8 p-8 lg:p-10">
                    
                    {{-- Section 1: Informasi Dasar --}}
                    <div class="mb-10">
                        <div class="flex items-center mb-6">
                            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center mr-3">
                                <i class="fas fa-id-badge text-red-600 text-sm"></i>
                            </div>
                            <h2 class="text-lg font-bold text-slate-800">Informasi Dasar</h2>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <label for="nama_lengkap" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Lengkap</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-slate-400"></i>
                                    </div>
                                    <input type="text" name="nama_lengkap" id="nama_lengkap" 
                                        class="block w-full pl-11 pr-4 py-3 bg-slate-50 border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all"
                                        value="{{ old('nama_lengkap', $admin->nama_lengkap) }}" required>
                                </div>
                                @error('nama_lengkap') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Alamat Email (Username)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-slate-400"></i>
                                    </div>
                                    <input type="email" name="email" id="email" 
                                        class="block w-full pl-11 pr-4 py-3 bg-slate-50 border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all"
                                        value="{{ old('email', $admin->email) }}" required>
                                </div>
                                @error('email') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="border-slate-100 my-8">

                    {{-- Section 2: Keamanan Akun --}}
                    <div>
                        <div class="flex items-center mb-6">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center mr-3">
                                <i class="fas fa-lock text-slate-600 text-sm"></i>
                            </div>
                            <h2 class="text-lg font-bold text-slate-800">Keamanan Akun</h2>
                        </div>
                        
                        <p class="text-xs text-slate-500 mb-5">Kosongkan kolom kata sandi jika Anda tidak ingin mengubahnya saat ini.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kata Sandi Baru</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-key text-slate-400"></i>
                                    </div>
                                    <input type="password" name="password" id="password" placeholder="••••••••"
                                        class="block w-full pl-11 pr-4 py-3 bg-slate-50 border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all">
                                </div>
                                @error('password') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Konfirmasi Sandi Baru</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-check-double text-slate-400"></i>
                                    </div>
                                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="••••••••"
                                        class="block w-full pl-11 pr-4 py-3 bg-slate-50 border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="mt-10 flex justify-end">
                        <button type="submit" class="inline-flex items-center justify-center px-8 py-3.5 bg-red-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-red-200 hover:bg-red-700 hover:-translate-y-0.5 transition-all duration-200">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan Profil
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Fungsionalitas preview gambar yang smooth
    document.getElementById('foto_profil').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            // Validasi ukuran file di sisi klien (Opsional, 2MB = 2097152 bytes)
            if(file.size > 2097152){
                alert('Ukuran foto terlalu besar. Maksimal 2MB.');
                this.value = ""; // Reset input
                return;
            }

            const reader = new FileReader();
            const preview = document.getElementById('profileImagePreview');
            
            // Efek transisi opacity saat ganti gambar
            preview.style.opacity = '0.5';
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                setTimeout(() => {
                    preview.style.opacity = '1';
                }, 150);
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection