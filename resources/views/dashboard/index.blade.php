@extends('layouts.app') 
{{-- Baris ini memberitahu Laravel bahwa tampilan ini akan "mewarisi" atau menggunakan template dasar dari file 'layouts/app.blade.php'. --}}
{{-- Ini berarti semua konten di dalam 'layouts/app.blade.php' (seperti header, sidebar, footer) akan otomatis disertakan. --}}

@section('content')
{{-- Bagian ini mendefinisikan konten spesifik untuk tampilan ini. --}}
{{-- Konten di dalam @section('content') akan ditempatkan di mana @yield('content') berada di 'layouts/app.blade.php'. --}}

    <!-- Kontainer utama untuk konten dashboard -->
    <!-- Kelas Tailwind CSS:
         bg-gray-100: Warna latar belakang abu-abu terang.
         min-h-screen: Tinggi minimum setinggi layar (viewport).
         p-6: Padding (jarak dalam) 24px di semua sisi. -->
    <div class="bg-gray-100 min-h-screen p-6">
        <!-- Kontainer dengan lebar terbatas dan margin otomatis untuk penempatan di tengah -->
        <div class="container mx-auto">
            <!-- Judul halaman dashboard -->
            <!-- text-3xl: Ukuran font besar.
                 font-bold: Tebal.
                 text-gray-800: Warna teks abu-abu gelap.
                 mb-8: Margin bawah 32px. -->
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Dashboard Admin</h1>

            <!-- Bagian untuk menampilkan kartu-kartu statistik (Cards) -->
            <!-- grid: Menggunakan CSS Grid untuk tata letak.
                 grid-cols-1: Satu kolom di layar kecil.
                 md:grid-cols-2: Dua kolom di layar menengah (tablet).
                 lg:grid-cols-4: Empat kolom di layar besar (desktop).
                 gap-6: Jarak antar kolom dan baris sebesar 24px.
                 mb-8: Margin bawah 32px. -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                
                <!-- Card Statistik: Total Karyawan -->
                <!-- bg-white: Latar belakang putih.
                     rounded-xl: Sudut sangat membulat.
                     shadow-md: Bayangan sedang.
                     p-6: Padding 24px.
                     flex items-center justify-between: Menggunakan Flexbox untuk menata konten (ikon dan teks) secara horizontal dengan jarak di antara keduanya.
                     transition-transform duration-200 hover:scale-105: Efek transisi saat hover (sedikit membesar). -->
                <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between transition-transform duration-200 hover:scale-105">
                    <div>
                        <!-- Teks label statistik -->
                        <p class="text-sm font-medium text-gray-500 mb-1">Total Karyawan</p>
                        <!-- Nilai statistik. {{ $totalKaryawan }} adalah Blade syntax untuk menampilkan variabel PHP. -->
                        <!-- text-3xl: Ukuran font besar.
                             font-bold: Tebal.
                             text-gray-900: Warna teks abu-abu sangat gelap. -->
                        <p class="text-3xl font-bold text-gray-900">{{ $totalKaryawan }}</p> {{-- Ganti dengan {{ $totalKaryawan }} jika data dari controller --}}
                    </div>
                    <!-- Icon untuk card ini -->
                    <!-- bg-red-100: Latar belakang merah muda.
                         p-3: Padding 12px.
                         rounded-full: Bentuk lingkaran sempurna. -->
                    <div class="bg-red-100 p-3 rounded-full">
                        <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>

                <!-- Card Statistik: Unit Kerja Aktif -->
                <!-- Struktur dan styling mirip dengan card sebelumnya -->
                <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between transition-transform duration-200 hover:scale-105">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Unit Kerja Aktif</p>
                        <p class="text-3xl font-bold text-gray-900">{{$totalUnitKerja}}</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    </div>
                </div>

                <!-- Card Statistik: Proyek Selesai -->
                <!-- Struktur dan styling mirip dengan card sebelumnya -->
                <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between transition-transform duration-200 hover:scale-105">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Total Pasien</p>
                        <p class="text-3xl font-bold text-gray-900">42</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between transition-transform duration-200 hover:scale-105">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Pasien PTST</p>
                        <p class="text-3xl font-bold text-gray-900">3</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between transition-transform duration-200 hover:scale-105">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Pasien Non PTST</p>
                        <p class="text-3xl font-bold text-gray-900">3</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Bagian untuk Grafik dan Aktivitas Terbaru -->
            <!-- grid: Menggunakan CSS Grid.
                 grid-cols-1: Satu kolom di layar kecil.
                 lg:grid-cols-3: Tiga kolom di layar besar (desktop).
                 gap-6: Jarak antar kolom dan baris 24px. -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Bagian Grafik -->
                <div class="bg-white rounded-xl shadow-md p-6 lg:col-span-2">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Statistik Karyawan</h2>
                    <!-- Placeholder untuk Chart.js atau library grafik lainnya -->
                    <div class="bg-gray-100 h-64 flex items-center justify-center rounded-lg text-gray-500">
                        Grafik akan ditampilkan di sini
                    </div>
                </div>

                <!-- Bagian Aktivitas Terbaru -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas Terbaru</h2>
                    <ul class="divide-y divide-gray-200">
                        <li class="py-3">
                            <p class="text-sm font-medium text-gray-900">John Doe telah menambahkan karyawan baru.</p>
                            <p class="text-xs text-gray-500 mt-1">10 menit yang lalu</p>
                        </li>
                        <li class="py-3">
                            <p class="text-sm font-medium text-gray-900">Jane Smith telah mengedit data karyawan.</p>
                            <p class="text-xs text-gray-500 mt-1">1 jam yang lalu</p>
                        </li>
                        <li class="py-3">
                            <p class="text-sm font-medium text-gray-900">Admin mengunggah laporan bulanan.</p>
                            <p class="text-xs text-gray-500 mt-1">1 hari yang lalu</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
