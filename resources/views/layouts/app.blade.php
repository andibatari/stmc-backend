<!DOCTYPE html>
<html lang="id">
<head>
    {{-- <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script> --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem MCU & Pemantauan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" xintegrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
            /* Tambahkan transisi untuk pergeseran smooth */
            transition: margin-left 0.3s ease-in-out;
        }
        .sidebar-link.active {
            background-color: #7f1d1d; /* bg-red-800 lighter */
        }
        .sub-menu {
            /* KRITIS: Biarkan JS mengontrol display */
        }
        .sidebar-nav-container {
            flex-grow: 1;
            overflow-y: auto; 
            overflow-x: hidden;
            height: 0; 
        }

        /* Styling untuk sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 40; 
            transition: transform 0.3s ease-in-out;
            transform: translateX(-100%); /* Sembunyi di mobile secara default */
            width: 16rem; /* w-64 */
            flex-shrink: 0; 
        }
        
        /* Sidebar terlihat ketika kelas 'sidebar-open' ditambahkan */
        .sidebar.sidebar-open {
            transform: translateX(0%);
        }
        
        /* Styling untuk header */
        .main-header {
            position: sticky;
            top: 0;
            z-index: 30; 
            width: 100%;
        }

        /* 🎯 KRITIS: Mengatur Margin Kiri di Main Content */
        /* Konten utama: di desktop harus memiliki margin kiri agar tidak tertutup sidebar */
        .main-content-area {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease-in-out;
        }
        
        /* Margin kiri untuk main content di layar besar */
        @media (min-width: 1024px) { /* lg breakpoint */
            /* Di desktop, sidebar selalu terlihat, jadi beri margin permanen */
            .main-content-area {
                margin-left: 16rem; /* Lebar sidebar (w-64 = 16rem) */
            }
            /* Pastikan sidebar juga selalu terlihat di desktop */
            .sidebar {
                transform: translateX(0%);
            }
        }
        /* 🎯 Perbaikan Mobile: Gunakan body class untuk mematikan scroll dan memberi padding */
        /* Ketika sidebar terbuka di mobile, main content tidak perlu digeser, cukup sidebar yang fixed */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 35; 
            display: none; 
            transition: opacity 0.3s ease-in-out;
            opacity: 0;
        }
        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }
        @media (min-width: 1024px) {
            .sidebar-overlay {
                display: none !important;
            }
        }
        
        /* 🎯 PENTING: Kelas yang akan ditambahkan ke BODY saat sidebar mobile terbuka */
        /* Mencegah scrolling di belakang sidebar yang fixed */
        body.sidebar-mobile-open {
            overflow: hidden; 
        }
        /* Di mobile, konten utama tidak perlu digeser/margin */
        @media (max-width: 1023px) {
            .main-content-area {
                margin-left: 0 !important; 
            }
        }
    </style>
    @livewireStyles
</head>
<body>
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <div class="flex min-h-screen bg-gray-100">
        <aside id="sidebar" class="bg-red-800 text-white shadow-xl border-r border-gray-200 flex flex-col sidebar">
            <div class="p-6 flex items-center border-b border-red-700">
                <img src="{{asset('images/LogoStmc.png')}}" alt="STMC Logo" class="h-12 w-12 mr-2 rounded">
                <div class="flex flex-col">
                    <span class="font-bold text-lg">STMC</span>
                    <span class="text-xs text-gray-300">Semen Tonasa Medical Centre</span>
                </div>
            </div>
            
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

        <div id="mainContentArea" class="main-content-area">
            <header class="bg-white shadow-sm p-4 flex items-center justify-between border-b border-gray-200 main-header">
                <div class="flex items-center">
                    <button id="sidebarToggle" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 lg:hidden">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <div class="text-lg font-semibold text-gray-700 ml-4 lg:ml-0">
                        @yield('title', 'Dashboard')
                    </div>
                </div>
                
                <div class="flex items-center">
                    {{-- DROPDOWN NOTIFIKASI TUNGGAL --}}
                    <div class="relative">
                        <button id="notificationDropdownButton" class="p-2 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors duration-150 relative focus:outline-none">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            
                            {{-- Badge Notifikasi HANYA JIKA ADA UNREAD --}}
                            @if (isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                                <span class="absolute top-0 right-0 h-4 w-4 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center -mt-1 -mr-1">
                                    {{ $unreadNotificationsCount }}
                                </span>
                            @else
                                {{-- Jika tidak ada notifikasi, tampilkan badge merah kecil jika Anda ingin menunjukkan status ON --}}
                                <span class="absolute top-0 right-0 h-2 w-2 bg-red-500 rounded-full"></span>
                            @endif
                        </button>

                        <div id="notificationDropdownMenu" class="absolute right-0 mt-2 w-72 bg-white rounded-lg shadow-xl overflow-hidden z-20 border border-gray-200 hidden">
                            <div class="p-4 border-b">
                                <h4 class="text-sm font-semibold text-gray-700">Notifikasi Baru</h4>
                            </div>
                            
                            {{-- Konten Notifikasi --}}
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
                        @php
                            $user = Auth::guard('admin_users')->user();
                            $photoUrl = null;
                            
                            if ($user->foto_profil) {
                                $basePath = str_replace('public/', 'storage/', $user->foto_profil);
                                $photoUrl = asset($basePath) . '?t=' . now()->timestamp; 
                            } else {
                                $photoUrl = 'https://ui-avatars.com/api/?name=' . urlencode($user->nama_lengkap ?? 'Admin') . '&color=FFFFFF&background=DC2626&size=40';
                            }
                        @endphp
                        <img src="{{ $photoUrl }}" alt="Admin" class="h-10 w-10 rounded-full mr-2 lg:mr-4"> 
                            <div class="flex flex-col text-sm hidden lg:flex"> 
                                <span class="font-semibold text-gray-900">{{ Auth::guard('admin_users')->user()->nama_lengkap ?? 'Admin' }}</span>
                                <span class="text-gray-500">{{ Auth::guard('admin_users')->user()->no_sap ?? Auth::guard('admin_users')->user()->email ?? 'N/A'}}</span>
                            </div>
                            <svg class="h-4 w-4 ml-2 text-gray-500 hidden lg:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
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

            <main class="p-6 flex-1 overflow-y-auto">
                {{-- Flash Message/Alerts --}}
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
            
            var toggleAdmin = document.getElementById('toggleAdmin');
            var submenuAdmin = document.getElementById('submenuAdmin');

            var sidebarToggle = document.getElementById('sidebarToggle');
            var sidebar = document.getElementById('sidebar');
            var sidebarOverlay = document.getElementById('sidebarOverlay');
            var body = document.body; // Ambil body
            
            var toggleLingkungan = document.getElementById('toggleLingkungan');
            var submenuLingkungan = document.getElementById('submenuLingkungan');

            var toggleNotifikasi = document.getElementById('toggleNotifikasi');
            var submenuNotifikasi = document.getElementById('submenuNotifikasi');

            // elemen dropdown notifikasi & profil
            var notificationDropdownButton = document.getElementById('notificationDropdownButton');
            var notificationDropdownMenu = document.getElementById('notificationDropdownMenu');
            var profileDropdownButton = document.getElementById('profileDropdownButton');
            var profileDropdownMenu = document.getElementById('profileDropdownMenu');
            
            // Panah
            var arrowKaryawan = document.getElementById('arrowKaryawan');
            var arrowJadwal = document.getElementById('arrowJadwal');
            var arrowAdmin = document.getElementById('arrowAdmin');
            var arrowLingkungan = document.getElementById('arrowLingkungan');
            var arrowNotifikasi = document.getElementById('arrowNotifikasi');


            /**
             * Fungsi untuk mengaktifkan/menonaktifkan dropdown.
             */
            function toggleDropdown(button, menu) {
                var allDropdowns = [notificationDropdownMenu, profileDropdownMenu];
                allDropdowns.forEach(function(item) {
                    if (item && item !== menu && !item.classList.contains('hidden')) {
                        item.classList.add('hidden');
                    }
                });
                if (menu) {
                    menu.classList.toggle('hidden');
                }
            }
            
            /**
             * Fungsi untuk mengelola toggle submenu dan rotasi panah.
             */
            function toggleSubmenu(submenu, arrow, forceOpen = false) {
                if (!submenu || !arrow) return;
                
                const isCurrentlyOpen = submenu.style.display === 'block';

                if (forceOpen || !isCurrentlyOpen) {
                    submenu.style.display = 'block'; 
                    arrow.classList.add('rotate-180');
                } else {
                    submenu.style.display = 'none'; 
                    arrow.classList.remove('rotate-180');
                }
            }
            
            /**
             * Fungsi untuk mengelola klik menu utama (toggle)
             */
            function handleMenuClick(submenu, arrow) {
                if (submenu.classList.contains('hidden')) {
                    submenu.classList.remove('hidden');
                    submenu.style.display = 'none'; 
                }
                toggleSubmenu(submenu, arrow);
            }
            
            // --- INICIALISASI STATUS MENU SAAT LOAD ---
            const menuConfigs = [
                { toggle: toggleKaryawan, submenu: submenuKaryawan, arrow: arrowKaryawan, routeActive: {{ request()->routeIs('karyawan.*') ? 'true' : 'false' }} },
                { toggle: toggleJadwal, submenu: submenuJadwal, arrow: arrowJadwal, routeActive: {{ request()->routeIs('jadwal.*') || request()->routeIs('scan.qr') ? 'true' : 'false' }} },
                { toggle: toggleAdmin, submenu: submenuAdmin, arrow: arrowAdmin, routeActive: {{ request()->routeIs('admin.create') || request()->routeIs('admin.tambah-dokter') || request()->routeIs('paket-poli') ? 'true' : 'false' }} },
                { toggle: toggleLingkungan, submenu: submenuLingkungan, arrow: arrowLingkungan, routeActive: {{ request()->routeIs('pemantauan.*') ? 'true' : 'false' }} },
                { toggle: toggleNotifikasi, submenu: submenuNotifikasi, arrow: arrowNotifikasi, routeActive: {{ request()->routeIs('notifications.*') ? 'true' : 'false' }} }
            ];

            menuConfigs.forEach(config => {
                if (config.toggle) {
                    // 1. Inisialisasi: Hapus kelas 'hidden' dari Blade jika rute aktif, dan atur display ke 'block'
                    if (config.routeActive === 'true') {
                        config.submenu.classList.remove('hidden');
                        config.submenu.style.display = 'block';
                        config.arrow.classList.add('rotate-180');
                    } else {
                        config.submenu.style.display = 'none';
                    }
                    
                    // 2. Tambahkan event listener untuk toggle manual
                    config.toggle.addEventListener('click', () => handleMenuClick(config.submenu, config.arrow));
                }
            });


            // Tambahkan event listener untuk tombol dropdown
            if (notificationDropdownButton) {
                notificationDropdownButton.addEventListener('click', function(event) {
                    event.stopPropagation();
                    toggleDropdown(notificationDropdownButton, notificationDropdownMenu);
                });
            }

            if (profileDropdownButton) {
                profileDropdownButton.addEventListener('click', function(event) {
                    event.stopPropagation();
                    toggleDropdown(profileDropdownButton, profileDropdownMenu);
                });
            }

            // Tutup semua dropdown ketika mengklik di luar area dropdown
            window.addEventListener('click', function(event) {
                if (notificationDropdownMenu && !notificationDropdownMenu.contains(event.target) && !notificationDropdownButton.contains(event.target)) {
                    notificationDropdownMenu.classList.add('hidden');
                }
                if (profileDropdownMenu && !profileDropdownMenu.contains(event.target) && !profileDropdownButton.contains(event.target)) {
                    profileDropdownMenu.classList.add('hidden');
                }
            });


            /**
             * 🎯 FUNGSI UTAMA UNTUK MEMPERBAIKI SIDEBAR MOBILE
             */
            function toggleMobileSidebar() {
                const isSidebarOpen = sidebar.classList.toggle('sidebar-open');
                sidebarOverlay.classList.toggle('show');
                
                // Mencegah scroll pada body saat sidebar fixed terbuka
                if (isSidebarOpen) {
                    body.classList.add('sidebar-mobile-open');
                } else {
                    body.classList.remove('sidebar-mobile-open');
                }
            }

            // toggle sidebar
            if (sidebarToggle && sidebar && sidebarOverlay) {
                sidebarToggle.addEventListener('click', toggleMobileSidebar);
            }

            // overlay klik → tutup sidebar
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', toggleMobileSidebar);
            }

            // 🎯 Nonaktifkan sidebar mobile dan hapus kelas 'body' jika beralih ke desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) { // lg breakpoint
                    sidebar.classList.remove('sidebar-open');
                    sidebarOverlay.classList.remove('show');
                    body.classList.remove('sidebar-mobile-open');
                }
            });

        });

        // Di dalam <script> di layout Anda
        document.addEventListener('livewire:initialized', () => {
    
            Livewire.on('view-merged-pdf', (event) => { 
                const data = event[0]; 
                const jadwalId = data.jadwalId;

                if (jadwalId) {
                    const baseUrl = window.location.origin;
                    const url = `${baseUrl}/admin/download-mcu-summary/${jadwalId}`;
                    
                    const newWindow = window.open(url, '_blank'); 
                    
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
    @stack('scripts') 
</body>
</html>