<!DOCTYPE html>
<html lang="id">
<head>
    {{-- <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script> --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem MCU & Pemantauan</title>
    <!-- Tailwind CSS -->
    {{-- untuk memuat Tailwind CSS ke dalam halaman web --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" xintegrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .sidebar-link.active {
            background-color: #7f1d1d; /* bg-red-800 lighter */
        }
        .sub-menu {
            /* KRITIS: Hapus display: none; di sini agar JS bisa mengontrol style.display */
        }
        /* Menyesuaikan tinggi navigasi agar tidak menyebabkan scroll pada keseluruhan sidebar */
        .sidebar-nav-container {
            flex-grow: 1;
            overflow-y: auto; 
            overflow-x: hidden;
            height: 0; /* Penting: Setel tinggi awal ke 0 agar flex-grow dapat bekerja dengan benar */
        }

        /* Styling untuk sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 40; /* Lebih tinggi dari header mobile */
            transition: transform 0.3s ease-in-out;
            transform: translateX(-100%); /* Sembunyi di mobile secara default */
            width: 16rem; /* w-64 */
            flex-shrink: 0; /* Mencegah sidebar menyusut */
        }
        
        /* Sidebar terlihat ketika kelas 'sidebar-open' ditambahkan */
        .sidebar.sidebar-open {
            transform: translateX(0%);
        }
        
        /* Styling untuk header */
        .main-header {
            position: sticky;
            top: 0;
            z-index: 30; /* Di atas konten, di bawah sidebar mobile */
            width: 100%;
        }

        /* Menyesuaikan margin kiri pada main content agar tidak tertutup sidebar */
        .main-content-area {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            margin-left: 0; /* Awalnya 0, akan diatur oleh JS/media query */
            transition: margin-left 0.3s ease-in-out;
        }
        /* Margin kiri untuk main content di layar besar */
        @media (min-width: 1024px) { /* lg breakpoint */
            .main-content-area {
                margin-left: 16rem; /* Lebar sidebar (w-64 = 16rem) */
            }
        }

        /* Overlay di belakang sidebar saat terbuka di mobile */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 35; /* Di atas konten utama, di bawah sidebar */
            display: none; /* Awalnya tersembunyi */
        }
        /* Tampilkan overlay saat sidebar mobile terbuka */
        .sidebar-overlay.show {
            display: block;
        }
        /* Pastikan overlay tidak muncul di desktop */
        @media (min-width: 1024px) {
            .sidebar-overlay {
                display: none !important;
            }
        }
    </style>
    @livewireStyles
</head>
<body>
    <!-- Overlay untuk mobile, akan muncul saat sidebar terbuka -->
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <!-- Kontainer utama yang mengatur layout flexbox untuk sidebar dan konten utama -->
    <div class="flex min-h-screen bg-gray-100">
        <!-- Sidebar (Panel Navigasi Samping) -->
        <aside id="sidebar" class="bg-red-800 text-white shadow-xl border-r border-gray-200 flex flex-col sidebar">
            <!-- Bagian Logo dan Nama Aplikasi di Sidebar -->
            <div class="p-6 flex items-center border-b border-red-700">
                <img src="{{asset('images/LogoStmc.png')}}" alt="STMC Logo" class="h-12 w-12 mr-2 rounded">
                <div class="flex flex-col">
                    <span class="font-bold text-lg">STMC</span>
                    <span class="text-xs text-gray-300">Semen Tonasa Medical Centre</span>
                </div>
            </div>
            
            <!-- Bagian Navigasi Utama di Sidebar -->
            <div class="mt-4 sidebar-nav-container">
                <nav>
                    <ul>
                        <li class="my-1">
                            <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center p-4 text-gray-200 hover:bg-red-700 rounded-lg mx-3 transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <svg class="h-5 w-5 mr-3" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="my-1">
                            <button id="toggleKaryawan" class="w-full text-left sidebar-link flex items-center justify-between p-4 text-gray-200 hover:bg-red-700 rounded-lg mx-3 transition-colors duration-200 {{ request()->routeIs('karyawan.*') ? 'active bg-red-700' : '' }}">
                                <span class="flex items-center">
                                    <svg class="h-5 w-5 mr-3" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                    <span>Manajemen Pasien</span>
                                </span>
                                <svg id="arrowKaryawan" class="h-4 w-4 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <ul id="submenuKaryawan" class="sub-menu pl-8 mt-1 {{ request()->routeIs('karyawan.*') ? 'block' : 'hidden' }}">
                                <li class="my-1">
                                    <a href="{{ route('karyawan.index') }}" class="sidebar-link block p-3 text-sm text-gray-300 hover:bg-red-700 rounded-lg mx-1 transition-colors duration-200 {{ request()->routeIs('karyawan.index') ? 'active bg-red-700' : '' }}">
                                        Daftar Pasien
                                    </a>
                                </li>
                                <!-- Tambahkan submenu lain di sini jika perlu -->
                            </ul>
                        </li>
                        <li class="my-1">
                            <button id="toggleJadwal" class="w-full text-left sidebar-link flex items-center justify-between p-4 text-gray-200 hover:bg-red-700 rounded-lg mx-3 transition-colors duration-200 {{ request()->routeIs('jadwal.*') || request()->routeIs('scan.qr') ? 'active bg-red-700' : '' }}">
                                <span class="flex items-center">
                                    <svg class="h-5 w-5 mr-3" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/></svg>
                                    <span>Manajemen Jadwal</span>
                                </span>
                                <svg id="arrowJadwal" class="h-4 w-4 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>

                            <ul id="submenuJadwal" class="sub-menu pl-8 mt-1 {{ request()->routeIs('jadwal.*') || request()->routeIs('scan.qr') ? 'block' : 'hidden' }}">
                                <li class="my-1">
                                    <a href="{{ route('jadwal.index') }}" class="sidebar-link block p-3 text-sm text-gray-300 hover:bg-red-700 rounded-lg mx-1 transition-colors duration-200 {{ request()->routeIs('jadwal.index') ? 'active bg-red-700' : '' }}">
                                        Daftar Jadwal
                                    </a>
                                </li>
                                <li class="my-1">
                                    <a href="{{ route('scan.qr') }}" class="sidebar-link block p-3 text-sm text-gray-300 hover:bg-red-700 rounded-lg mx-1 transition-colors duration-200 {{ request()->routeIs('scan.qr') ? 'active bg-red-700' : '' }}">
                                        Registrasi Pasien via QR
                                    </a>
                                </li>
                            </ul>
                        </li>
                        
                        <li class="my-1">
                            <button id="toggleLingkungan" class="w-full text-left sidebar-link flex items-center justify-between p-4 text-gray-200 hover:bg-red-700 rounded-lg mx-3 transition-colors duration-200 {{ request()->routeIs('pemantauan.*') ? 'active bg-red-700' : '' }}">
                                <span class="flex items-center">
                                    <svg class="h-5 w-5 mr-3" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2L4 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-8-3z"/></svg>
                                    <span>Manajemen Lingkungan</span>
                                </span>
                                <svg id="arrowLingkungan" class="h-4 w-4 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <ul id="submenuLingkungan" class="sub-menu pl-8 mt-1 {{ request()->routeIs('pemantauan.*') ? 'block' : 'hidden' }}">
                                <li class="my-1">
                                    <a href="{{ route('pemantauan.index') }}" class="sidebar-link block p-3 text-sm text-gray-300 hover:bg-red-700 rounded-lg mx-1 transition-colors duration-200 {{ request()->routeIs('pemantauan.index') ? 'active bg-red-700' : '' }}">
                                        Pemantauan Lingkungan
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- START - MENU BARU UNTUK MANAJEMEN NOTIFIKASI --}}
                        <li class="my-1">
                            <button id="toggleNotifikasi" class="w-full text-left sidebar-link flex items-center justify-between p-4 text-gray-200 hover:bg-red-700 rounded-lg mx-3 transition-colors duration-200 
                                {{ request()->routeIs('notifications.*') ? 'active bg-red-700' : '' }}">
                                <span class="flex items-center">
                                    {{-- Menggunakan ikon Bell untuk Notifikasi --}}
                                    <svg class="h-5 w-5 mr-3" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-1.38-1.12-2.5-2.5-2.5S8.5 2.62 8.5 4v.68C5.63 5.36 4 7.93 4 11v5l-2 2v1h20v-1l-2-2z"/></svg>
                                    <span>Manajemen Notifikasi</span>
                                </span>
                                <svg id="arrowNotifikasi" class="h-4 w-4 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <ul id="submenuNotifikasi" class="sub-menu pl-8 mt-1 {{ request()->routeIs('notifications.*') ? 'block' : 'hidden' }}">
                                <li class="my-1">
                                    <a href="{{ route('notifications.dashboard') }}" class="sidebar-link block p-3 text-sm text-gray-300 hover:bg-red-700 rounded-lg mx-1 transition-colors duration-200 {{ request()->routeIs('notifications.dashboard') ? 'active bg-red-700' : '' }}">
                                        Dashboard Notif
                                    </a>
                                </li>
                                <li class="my-1">
                                    <a href="{{ route('notifications.history') }}" class="sidebar-link block p-3 text-sm text-gray-300 hover:bg-red-700 rounded-lg mx-1 transition-colors duration-200 {{ request()->routeIs('notifications.history') ? 'active bg-red-700' : '' }}">
                                        Riwayat Pengiriman
                                    </a>
                                </li>
                            </ul>
                        </li>
                        {{-- START - MENU BARU UNTUK MANAJEMEN ADMIN DAN DOKTER --}}
                        <li class="my-1">
                            <button id="toggleAdmin" class="w-full text-left sidebar-link flex items-center justify-between p-4 text-gray-200 hover:bg-red-700 rounded-lg mx-3 transition-colors duration-200 {{ request()->routeIs('admin.create') || request()->routeIs('admin.tambah-dokter') || request()->routeIs('paket-poli') ? 'active bg-red-700' : '' }}">
                                <span class="flex items-center">
                                    <svg class="h-5 w-5 mr-3" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                    <span>Manajemen Layanan</span>
                                </span>
                                <svg id="arrowAdmin" class="h-4 w-4 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <ul id="submenuAdmin" class="sub-menu pl-8 mt-1 {{ request()->routeIs('admin.create') || request()->routeIs('admin.tambah-dokter') || request()->routeIs('paket-poli') ? 'block' : 'hidden' }}">
                                <li class="my-1">
                                    <a href="{{ route('admin.create') }}" class="sidebar-link block p-3 text-sm text-gray-300 hover:bg-red-700 rounded-lg mx-1 transition-colors duration-200 
                                        {{ request()->routeIs('admin.create') ? 'active bg-red-700' : '' }}">
                                        Tambah Admin
                                    </a>
                                </li>
                                <li class="my-1">
                                    <a href="{{ route('admin.tambah-dokter') }}" class="sidebar-link block p-3 text-sm text-gray-300 hover:bg-red-700 rounded-lg mx-1 transition-colors duration-200 
                                        {{ request()->routeIs('admin.tambah-dokter') ? 'active bg-red-700' : '' }}">
                                        Tambah Dokter
                                    </a>
                                </li>
                                <li class="my-1">
                                    <a href="{{ route('paket-poli') }}" class="sidebar-link block p-3 text-sm text-gray-300 hover:bg-red-700 rounded-lg mx-1 transition-colors duration-200 {{ request()->routeIs('layanan.kelola') ? 'active bg-red-700' : '' }}">
                                        Kelola Paket & Poli
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>

            <!-- Tombol Logout di Sidebar Paling Bawah -->
            <div class="mt-auto p-4 border-t border-red-700">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center bg-red-700 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div id="mainContentArea" class="main-content-area">
            <!-- Header -->
            <header class="bg-white shadow-sm p-4 flex items-center justify-between border-b border-gray-200 main-header">
                <div class="flex items-center">
                    <!-- Tombol Toggle Sidebar (untuk tampilan mobile/tablet) -->
                    <button id="sidebarToggle" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 ">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <!-- Judul Halaman Saat Ini (diambil dari @yield('title')) -->
                    <div class="text-lg font-semibold text-gray-700 ml-4">
                        @yield('title', 'Dashboard')
                    </div>
                </div>
                
                <div class="flex items-center">
                    {{-- DROPDOWN NOTIFIKASI TUNGGAL --}}
                    <div class="relative">
                        <button id="notificationDropdownButton" class="p-2 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors duration-150 relative focus:outline-none">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            
                            {{-- Badge Notifikasi HANYA JIKA ADA UNREAD (ASUMSI LOGIKA LIVEWIRE/BACKEND SUDAH MENGAMBIL NILAI INI) --}}
                            @if (isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                                <span class="absolute top-0 right-0 h-4 w-4 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center -mt-1 -mr-1">
                                    {{ $unreadNotificationsCount }}
                                </span>
                            @else
                                {{-- Jika tidak ada notifikasi, tampilkan badge merah kecil jika Anda ingin menunjukkan status ON --}}
                                {{-- Jika Anda hanya ingin badge muncul ketika ada hitungan > 0, hapus span ini --}}
                                <span class="absolute top-0 right-0 h-2 w-2 bg-red-500 rounded-full"></span>
                            @endif
                        </button>

                        <div id="notificationDropdownMenu" class="absolute right-0 mt-2 w-72 bg-white rounded-lg shadow-xl overflow-hidden z-20 border border-gray-200 hidden">
                            <div class="p-4 border-b">
                                <h4 class="text-sm font-semibold text-gray-700">Notifikasi Baru</h4>
                            </div>
                            
                            {{-- Konten Notifikasi (ASUMSI INI DATANG DARI Livewire Component) --}}
                            <div class="max-h-64 overflow-y-auto">
                                <a href="#" class="block px-4 py-3 border-b hover:bg-gray-50 text-sm text-gray-600">
                                    <p class="font-medium text-red-600">5 Permintaan Jadwal Baru!</p>
                                    <p class="text-xs text-gray-500 mt-1">10 menit yang lalu</p>
                                </a>
                                <a href="#" class="block px-4 py-3 border-b hover:bg-gray-50 text-sm text-gray-600">
                                    <p class="font-medium">Jadwal MCU disetujui.</p>
                                    <p class="text-xs text-gray-500 mt-1">Kemarin</p>
                                </a>
                                <div class="p-4 text-center text-sm text-gray-500">Tidak ada notifikasi baru lainnya.</div>
                            </div>
                            
                            <div class="p-2 border-t text-center">
                                <a href="{{ route('jadwal.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">Lihat Semua</a>
                            </div>
                        </div>
                    </div>

                    {{-- Dropdown Profil Admin --}}
                    <div class="relative">
                        <button id="profileDropdownButton" class="flex items-center focus:outline-none">
                        {{-- Tampilkan gambar profil jika ada, jika tidak, tampilkan inisial --}}
                            @php
                                $user = Auth::guard('admin_users')->user();
                                $photoUrl = null;
                                
                                if ($user->foto_profil) {
                                    $basePath = str_replace('public/', 'storage/', $user->foto_profil);
                                    // Tambahkan parameter kueri 't' (timestamp) untuk memaksa refresh
                                    $photoUrl = asset($basePath) . '?t=' . now()->timestamp; 
                                } else {
                                    $photoUrl = 'https://ui-avatars.com/api/?name=' . urlencode($user->nama_lengkap ?? 'Admin') . '&color=FFFFFF&background=DC2626&size=40';
                                }
                            @endphp
                            <img src="{{ $photoUrl }}" alt="Admin" class="h-10 w-10 rounded-full mr-4">
                            {{-- <img src="{{ $photoUrl }}" alt="Admin" class="h-10 w-10 rounded-full mr-4"> --}}
                            <div class="flex flex-col text-sm">
                                <!-- Menampilkan nama lengkap admin yang login -->
                                <span class="font-semibold text-gray-900">{{ Auth::guard('admin_users')->user()->nama_lengkap ?? 'Admin' }}</span>
                                <!-- Menampilkan email admin yang login -->
                                <span class="text-gray-500">{{ Auth::guard('admin_users')->user()->no_sap ?? Auth::guard('admin_users')->user()->email ?? 'N/A'}}</span>
                            </div>
                            <svg class="h-4 w-4 ml-2 text-gray-500 lg:block hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div id="profileDropdownMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl overflow-hidden z-20 border border-gray-200 hidden">
                            <a href="{{ route('admin.profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 border-b">
                                Kelola Profil
                            </a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6 flex-1 overflow-y-auto">
                {{-- 🔥 TEMPATKAN KODE INI DI SINI 🔥 --}}
                @if (session('error'))
                    <div id="alert-error" class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
                        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 1 1 2 0v5Z"/>
                        </svg>
                        <span class="sr-only">Info</span>
                        <div>
                            <span class="font-medium">Kesalahan Operasi:</span> {{ session('error') }}
                        </div>
                    </div>
                @endif
                @if (session('success'))
                    <div id="alert-success" class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 dark:border-green-800" role="alert">
                        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                        </svg>
                        <span class="sr-only">Success</span>
                        <div>
                            <span class="font-medium">Berhasil:</span> {{ session('success') }}
                        </div>
                    </div>
                @endif

                @yield('content')
                @isset($slot)
                    {{ $slot }}
                @endisset
            </main>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toggleKaryawan = document.getElementById('toggleKaryawan');
            var submenuKaryawan = document.getElementById('submenuKaryawan');
            var toggleJadwal = document.getElementById('toggleJadwal');
            var submenuJadwal = document.getElementById('submenuJadwal');
            
            // Tambahkan variabel baru untuk menu Admin
            var toggleAdmin = document.getElementById('toggleAdmin');
            var submenuAdmin = document.getElementById('submenuAdmin');

            var sidebarToggle = document.getElementById('sidebarToggle');
            var sidebar = document.getElementById('sidebar');
            var sidebarOverlay = document.getElementById('sidebarOverlay');
            var mainContentArea = document.getElementById('mainContentArea');
            var toggleLingkungan = document.getElementById('toggleLingkungan');
            var submenuLingkungan = document.getElementById('submenuLingkungan');

            // KRITIS: Tambahkan variabel baru untuk Notifikasi
            var toggleNotifikasi = document.getElementById('toggleNotifikasi');
            var submenuNotifikasi = document.getElementById('submenuNotifikasi');

            // elemen dropdown notifikasi & profil
            var notificationDropdownButton = document.getElementById('notificationDropdownButton');
            var notificationDropdownMenu = document.getElementById('notificationDropdownMenu');
            var profileDropdownButton = document.getElementById('profileDropdownButton');
            var profileDropdownMenu = document.getElementById('profileDropdownMenu');
            
            // KRITIS: Panah
            var arrowKaryawan = document.getElementById('arrowKaryawan');
            var arrowJadwal = document.getElementById('arrowJadwal');
            var arrowAdmin = document.getElementById('arrowAdmin');
            var arrowLingkungan = document.getElementById('arrowLingkungan');
            var arrowNotifikasi = document.getElementById('arrowNotifikasi');


            /**
             * Fungsi untuk mengaktifkan/menonaktifkan dropdown.
             * @param {HTMLElement} button - Tombol yang diklik.
             * @param {HTMLElement} menu - Menu yang akan di-toggle.
             */
            function toggleDropdown(button, menu) {
                // Tutup dropdown lain sebelum membuka yang baru
                var allDropdowns = [notificationDropdownMenu, profileDropdownMenu];
                allDropdowns.forEach(function(item) {
                    if (item !== menu && !item.classList.contains('hidden')) {
                        item.classList.add('hidden');
                    }
                });

                // Toggle kelas 'hidden' pada menu yang dituju
                menu.classList.toggle('hidden');
            }
            
            /**
             * Fungsi untuk mengelola toggle submenu dan rotasi panah.
             * @param {HTMLElement} submenu - Elemen UL submenu.
             * @param {HTMLElement} arrow - Elemen SVG panah.
             * @param {boolean} forceOpen - Memaksa menu terbuka saat inisialisasi.
             */
            function toggleSubmenu(submenu, arrow, forceOpen = false) {
                // KRITIS: Periksa status display, bukan kelas 'hidden' dari Blade yang mungkin sudah dihilangkan.
                const isCurrentlyOpen = submenu.style.display === 'block';

                if (forceOpen || !isCurrentlyOpen) {
                    submenu.style.display = 'block'; // Tampilkan
                    arrow.classList.add('rotate-180');
                } else {
                    submenu.style.display = 'none'; // Sembunyikan
                    arrow.classList.remove('rotate-180');
                }
            }
            
            /**
             * Fungsi untuk mengelola klik menu utama (toggle)
             * @param {HTMLElement} submenu - Elemen UL submenu.
             * @param {HTMLElement} arrow - Elemen SVG panah.
             */
            function handleMenuClick(submenu, arrow) {
                // Hapus kelas 'hidden' jika ada (ini diberikan oleh Blade saat tidak aktif)
                // Ini memastikan JS mengambil alih kontrol display dari CSS/Blade
                if (submenu.classList.contains('hidden')) {
                    submenu.classList.remove('hidden');
                    submenu.style.display = 'none'; // Set display ke none jika sebelumnya disembunyikan oleh Blade
                }
                
                toggleSubmenu(submenu, arrow);
            }
            
            // --- INICIALISASI STATUS MENU SAAT LOAD ---
            // Kita tentukan menu mana yang harus terbuka saat halaman dimuat (berdasarkan route aktif)

            const menuConfigs = [
                { toggle: toggleKaryawan, submenu: submenuKaryawan, arrow: arrowKaryawan, routeActive: {{ request()->routeIs('karyawan.*') ? 'true' : 'false' }} },
                { toggle: toggleJadwal, submenu: submenuJadwal, arrow: arrowJadwal, routeActive: {{ request()->routeIs('jadwal.*') || request()->routeIs('scan.qr') ? 'true' : 'false' }} },
                { toggle: toggleAdmin, submenu: submenuAdmin, arrow: arrowAdmin, routeActive: {{ request()->routeIs('admin.create') || request()->routeIs('admin.tambah-dokter') || request()->routeIs('paket-poli') ? 'true' : 'false' }} },
                { toggle: toggleLingkungan, submenu: submenuLingkungan, arrow: arrowLingkungan, routeActive: {{ request()->routeIs('pemantauan.*') ? 'true' : 'false' }} },
                { toggle: toggleNotifikasi, submenu: submenuNotifikasi, arrow: arrowNotifikasi, routeActive: {{ request()->routeIs('notifications.*') ? 'true' : 'false' }} }
            ];

            menuConfigs.forEach(config => {
                // 1. Inisialisasi: Hapus kelas 'hidden' dari Blade jika rute aktif, dan atur display ke 'block'
                if (config.routeActive === 'true') {
                    config.submenu.classList.remove('hidden');
                    config.submenu.style.display = 'block';
                    config.arrow.classList.add('rotate-180');
                } else {
                    // Jika tidak aktif, pastikan display tetap none
                    config.submenu.style.display = 'none';
                }
                
                // 2. Tambahkan event listener untuk toggle manual
                config.toggle.addEventListener('click', () => handleMenuClick(config.submenu, config.arrow));
            });


            // Tambahkan event listener untuk tombol dropdown
            notificationDropdownButton.addEventListener('click', function(event) {
                event.stopPropagation(); // Mencegah klik menyebar ke window
                toggleDropdown(notificationDropdownButton, notificationDropdownMenu);
            });

            profileDropdownButton.addEventListener('click', function(event) {
                event.stopPropagation(); // Mencegah klik menyebar ke window
                toggleDropdown(profileDropdownButton, profileDropdownMenu);
            });

            // Tutup semua dropdown ketika mengklik di luar area dropdown
            window.addEventListener('click', function(event) {
                if (!notificationDropdownMenu.contains(event.target) && !notificationDropdownButton.contains(event.target)) {
                    notificationDropdownMenu.classList.add('hidden');
                }
                if (!profileDropdownMenu.contains(event.target) && !profileDropdownButton.contains(event.target)) {
                    profileDropdownMenu.classList.add('hidden');
                }
            });


            // fungsi sinkronisasi sidebar dengan margin
            function syncSidebarState() {
                if (sidebar.classList.contains('sidebar-open')) {
                    mainContentArea.style.marginLeft = '16rem';
                } else {
                    mainContentArea.style.marginLeft = '0';
                }
            }

            // toggle sidebar
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('sidebar-open');
                sidebarOverlay.classList.toggle('show');
                syncSidebarState();
            });

            // overlay klik → tutup sidebar
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('sidebar-open');
                sidebarOverlay.classList.remove('show');
                syncSidebarState();
            });

            // atur ulang ketika resize
            window.addEventListener('resize', syncSidebarState);

            // 🔥 panggil saat awal load halaman
            syncSidebarState();
        });

        // Di dalam <script> di layout Anda
        document.addEventListener('livewire:initialized', () => {
    
            Livewire.on('view-merged-pdf', (event) => { 
                // KRITIS: Ambil data payload dari array event[0]
                // Jika Livewire v3, payload bernama (jadwalId: ...) akan berada di event[0]
                const data = event[0]; 
                const jadwalId = data.jadwalId;

                if (jadwalId) {
                    const baseUrl = window.location.origin;
                    // Pastikan URL mencakup prefix '/admin'
                    const url = `${baseUrl}/admin/download-mcu-summary/${jadwalId}`;
                    
                    // 🔥 SOLUSI UTAMA: Paksa buka jendela baru
                    const newWindow = window.open(url, '_blank'); 
                    
                    // Pengecekan jika Pop-up Blocker aktif
                    if (!newWindow || newWindow.closed || typeof newWindow.closed == 'undefined') {
                        alert('Gagal membuka file. Mungkin pop-up blocker browser Anda aktif. Silakan nonaktifkan pop-up blocker untuk situs ini.');
                    }

                } else {
                    console.error('ID Jadwal tidak ditemukan di payload.');
                }
            });
            
            // ... listener lain
        });
    </script>
    @livewireScripts
    @stack('scripts') {{-- Pastikan baris ini ada --}}
</body>
</html>