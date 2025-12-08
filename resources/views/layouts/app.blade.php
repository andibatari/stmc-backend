<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem MCU & Pemantauan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* 1. Sidebar Base Style: Fixed, Hidden by default on Mobile, Open on Desktop */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 40; 
            width: 16rem; /* w-64 */
            /* Default: Sembunyikan di Mobile */
            transform: translateX(-100%); 
            transition: transform 0.3s ease-in-out;
            flex-shrink: 0; 
            /* Tambahan untuk styling */
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        /* Sidebar terlihat ketika kelas 'sidebar-open' ditambahkan */
        .sidebar.sidebar-open {
            transform: translateX(0%);
        }
        
        /* 2. Desktop Behavior (lg: breakpoint) */
        @media (min-width: 1024px) { 
            /* Di desktop, sidebar selalu terlihat */
            .sidebar {
                transform: translateX(0%);
            }
        }

        /* 3. Main Content Area (Kritis untuk margin) */
        .main-content-area {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease-in-out;
            /* Default: No margin on Mobile */
            margin-left: 0;
        }
        
        /* Margin kiri untuk main content di layar besar (lg) */
        @media (min-width: 1024px) { 
            .main-content-area {
                margin-left: 16rem; /* Lebar sidebar (w-64 = 16rem) */
            }
        }

        /* 4. Overlay Mobile */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 35; 
            display: none; 
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
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
        
        /* 5. Body Scroll Lock (Kritis untuk Mobile UX) */
        body.sidebar-mobile-open {
            overflow: hidden; 
        }

        /* 6. Styling Link Aktif & Hover */
        .sidebar-link.active {
            background-color: #991b1b; /* Lebih terang sedikit dari red-800 */
            border-left: 4px solid #f87171; /* red-400 */
            margin-left: 0;
            margin-right: 0;
            padding-left: 20px; /* Sesuaikan dengan border-left */
        }
        .sidebar-link:not(.active) {
            margin-left: 0.75rem; /* mx-3 awal */
            margin-right: 0.75rem;
        }
        .sidebar-link:hover {
            background-color: #b91c1c; /* red-700 */
        }
        .sub-menu a:hover {
             background-color: #b91c1c !important; /* red-700 untuk submenu */
        }
        .sub-menu a.active {
            background-color: #991b1b !important;
        }

        /* 7. Scrollbar Custom (Opsional tapi bagus untuk estetika) */
        .sidebar-nav-container::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar-nav-container::-webkit-scrollbar-thumb {
            background-color: #991b1b; /* red-700 */
            border-radius: 3px;
        }
        .sidebar-nav-container::-webkit-scrollbar-track {
            background: #7f1d1d; /* red-800 lighter */
        }

    </style>
    @livewireStyles
</head>
<body class="font-sans">
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <div class="flex min-h-screen bg-gray-100">
        <aside id="sidebar" class="bg-red-800 text-white shadow-2xl flex flex-col sidebar">
            
            <div class="p-6 flex items-center border-b border-red-700 bg-red-900">
                <div class="p-6 flex items-center border-b border-red-700">
                <img src="{{asset('images/LogoStmc.png')}}" alt="STMC Logo" class="h-12 w-12 mr-2 rounded">
                <div class="flex flex-col">
                    <span class="font-bold text-lg">STMC</span>
                    <span class="text-xs text-gray-300">Semen Tonasa Medical Centre</span>
                </div>
            </div>
            </div>
            
            <div class="mt-4 sidebar-nav-container flex-grow h-full overflow-y-auto">
                <nav>
                    <ul>
                        <li class="my-1">
                            <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center p-4 text-gray-200 hover:bg-red-700 rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <svg class="h-5 w-5 mr-3" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        
                        <li class="my-1">
                            <button id="toggleKaryawan" class="w-full text-left sidebar-link flex items-center justify-between p-4 text-gray-200 hover:bg-red-700 rounded-lg transition-colors duration-200 {{ request()->routeIs('karyawan.*') ? 'active bg-red-700' : '' }}">
                                <span class="flex items-center">
                                    <svg class="h-5 w-5 mr-3" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                    <span>Manajemen Pasien</span>
                                </span>
                                <svg id="arrowKaryawan" class="h-4 w-4 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <ul id="submenuKaryawan" class="sub-menu pl-8 mt-1 {{ request()->routeIs('karyawan.*') ? 'block' : 'hidden' }}">
                                <li class="my-1">
                                    <a href="{{ route('karyawan.index') }}" class="sidebar-link block p-3 text-sm text-gray-300 hover:bg-red-700 rounded-lg mx-1 transition-colors duration-200 {{ request()->routeIs('karyawan.index') ? 'active' : '' }}">
                                        Daftar Pasien
                                    </a>
                                </li>
                            </ul>
                        </li>
                        
                        <li class="my-1">
                            <button id="toggleJadwal" class="w-full text-left sidebar-link flex items-center justify-between p-4 text-gray-200 hover:bg-red-700 rounded-lg transition-colors duration-200 {{ request()->routeIs('jadwal.*') || request()->routeIs('scan.qr') ? 'active bg-red-700' : '' }}">
                                <span class="flex items-center">
                                    <svg class="h-5 w-5 mr-3" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/></svg>
                                    <span>Manajemen Jadwal</span>
                                </span>
                                <svg id="arrowJadwal" class="h-4 w-4 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>

                            <ul id="submenuJadwal" class="sub-menu pl-8 mt-1 {{ request()->routeIs('jadwal.*') || request()->routeIs('scan.qr') ? 'block' : 'hidden' }}">
                                <li class="my-1">
                                    <a href="{{ route('jadwal.index') }}" class="sidebar-link block p-3 text-sm text-gray-300 hover:bg-red-700 rounded-lg mx-1 transition-colors duration-200 {{ request()->routeIs('jadwal.index') ? 'active' : '' }}">
                                        Daftar Jadwal
                                    </a>
                                </li>
                                <li class="my-1">
                                    <a href="{{ route('scan.qr') }}" class="sidebar-link block p-3 text-sm text-gray-300 hover:bg-red-700 rounded-lg mx-1 transition-colors duration-200 {{ request()->routeIs('scan.qr') ? 'active' : '' }}">
                                        Registrasi Pasien via QR
                                    </a>
                                </li>
                            </ul>
                        </li>
                        
                        <li class="my-1">
                            <button id="toggleLingkungan" class="w-full text-left sidebar-link flex items-center justify-between p-4 text-gray-200 hover:bg-red-700 rounded-lg transition-colors duration-200 {{ request()->routeIs('pemantauan.*') ? 'active bg-red-700' : '' }}">
                                <span class="flex items-center">
                                    <svg class="h-5 w-5 mr-3" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2L4 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-8-3z"/></svg>
                                    <span>Manajemen Lingkungan</span>
                                </span>
                                <svg id="arrowLingkungan" class="h-4 w-4 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <ul id="submenuLingkungan" class="sub-menu pl-8 mt-1 {{ request()->routeIs('pemantauan.*') ? 'block' : 'hidden' }}">
                                <li class="my-1">
                                    <a href="{{ route('pemantauan.index') }}" class="sidebar-link block p-3 text-sm text-gray-300 hover:bg-red-700 rounded-lg mx-1 transition-colors duration-200 {{ request()->routeIs('pemantauan.index') ? 'active' : '' }}">
                                        Pemantauan Lingkungan
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="my-1">
                            <button id="toggleNotifikasi" class="w-full text-left sidebar-link flex items-center justify-between p-4 text-gray-200 hover:bg-red-700 rounded-lg transition-colors duration-200 {{ request()->routeIs('notifications.*') ? 'active bg-red-700' : '' }}">
                                <span class="flex items-center">
                                    <svg class="h-5 w-5 mr-3" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-1.38-1.12-2.5-2.5-2.5S8.5 2.62 8.5 4v.68C5.63 5.36 4 7.93 4 11v5l-2 2v1h20v-1l-2-2z"/></svg>
                                    <span>Manajemen Notifikasi</span>
                                </span>
                                <svg id="arrowNotifikasi" class="h-4 w-4 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <ul id="submenuNotifikasi" class="sub-menu pl-8 mt-1 {{ request()->routeIs('notifications.*') ? 'block' : 'hidden' }}">
                                <li class="my-1">
                                    <a href="{{ route('notifications.dashboard') }}" class="sidebar-link block p-3 text-sm text-gray-300 hover:bg-red-700 rounded-lg mx-1 transition-colors duration-200 {{ request()->routeIs('notifications.dashboard') ? 'active' : '' }}">
                                        Dashboard Notif
                                    </a>
                                </li>
                                <li class="my-1">
                                    <a href="{{ route('notifications.history') }}" class="sidebar-link block p-3 text-sm text-gray-300 hover:bg-red-700 rounded-lg mx-1 transition-colors duration-200 {{ request()->routeIs('notifications.history') ? 'active' : '' }}">
                                        Riwayat Pengiriman
                                    </a>
                                </li>
                            </ul>
                        </li>
                        
                        <li class="my-1">
                            <button id="toggleAdmin" class="w-full text-left sidebar-link flex items-center justify-between p-4 text-gray-200 hover:bg-red-700 rounded-lg transition-colors duration-200 {{ request()->routeIs(['admin.create', 'admin.tambah-dokter', 'paket-poli']) ? 'active bg-red-700' : '' }}">
                                <span class="flex items-center">
                                    <svg class="h-5 w-5 mr-3" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5.01C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2.01 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm-1 18H5V8h14v11z"/></svg>
                                    <span>Manajemen Layanan</span>
                                </span>
                                <svg id="arrowAdmin" class="h-4 w-4 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <ul id="submenuAdmin" class="sub-menu pl-8 mt-1 {{ request()->routeIs(['admin.create', 'admin.tambah-dokter', 'paket-poli']) ? 'block' : 'hidden' }}">
                                <li class="my-1">
                                    <a href="{{ route('admin.create') }}" class="sidebar-link block p-3 text-sm text-gray-300 hover:bg-red-700 rounded-lg mx-1 transition-colors duration-200 {{ request()->routeIs('admin.create') ? 'active' : '' }}">
                                        Tambah Admin
                                    </a>
                                </li>
                                <li class="my-1">
                                    <a href="{{ route('admin.tambah-dokter') }}" class="sidebar-link block p-3 text-sm text-gray-300 hover:bg-red-700 rounded-lg mx-1 transition-colors duration-200 {{ request()->routeIs('admin.tambah-dokter') ? 'active' : '' }}">
                                        Tambah Dokter
                                    </a>
                                </li>
                                <li class="my-1">
                                    <a href="{{ route('paket-poli') }}" class="sidebar-link block p-3 text-sm text-gray-300 hover:bg-red-700 rounded-lg mx-1 transition-colors duration-200 {{ request()->routeIs('paket-poli') ? 'active' : '' }}">
                                        Kelola Paket & Poli
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="mt-auto p-4 border-t border-red-700 bg-red-700">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center bg-red-600 hover:bg-red-500 text-white font-bold py-2 px-4 rounded-lg shadow-xl transition-colors duration-200">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <div id="mainContentArea" class="main-content-area">
            
            <header class="bg-white shadow-md p-4 flex items-center justify-between border-b border-gray-200 main-header">
                <div class="flex items-center">
                    <button id="sidebarToggle" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 lg:hidden p-2 rounded-md hover:bg-gray-100">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <div class="text-xl font-bold text-gray-800 ml-3 lg:ml-0">
                        @yield('title', 'Dashboard')
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    {{-- DROPDOWN NOTIFIKASI --}}
                    <div class="relative">
                        <button id="notificationDropdownButton" class="p-2 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors duration-150 relative focus:outline-none">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            
                            {{-- Badge Notifikasi --}}
                            @if (isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                                <span class="absolute top-0 right-0 h-5 w-5 bg-red-600 text-white text-xs font-bold rounded-full flex items-center justify-center -mt-1 -mr-1 shadow-md">
                                    {{ $unreadNotificationsCount }}
                                </span>
                            @else
                                <span class="absolute top-0 right-0 h-2 w-2 bg-red-600 rounded-full"></span>
                            @endif
                        </button>

                        <div id="notificationDropdownMenu" class="absolute right-0 mt-2 w-72 bg-white rounded-lg shadow-xl overflow-hidden z-20 border border-gray-200 hidden">
                            <div class="p-4 border-b">
                                <h4 class="text-base font-bold text-gray-800">Notifikasi</h4>
                            </div>
                            
                            {{-- Konten Notifikasi (Contoh Statis) --}}
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
                                <a href="{{ route('jadwal.index') }}" class="text-xs font-semibold text-red-600 hover:text-red-800 transition-colors duration-150">Lihat Semua Notifikasi</a>
                            </div>
                        </div>
                    </div>

                    {{-- Dropdown Profil Admin --}}
                    <div class="relative">
                        <button id="profileDropdownButton" class="flex items-center focus:outline-none p-1 rounded-full hover:bg-gray-100 transition-colors duration-150">
                        @php
                            $user = Auth::guard('admin_users')->user();
                            $photoUrl = null;
                            
                            if (isset($user) && $user->foto_profil) {
                                $basePath = str_replace('public/', 'storage/', $user->foto_profil);
                                $photoUrl = asset($basePath) . '?t=' . now()->timestamp; 
                            } else {
                                $photoUrl = 'https://ui-avatars.com/api/?name=' . urlencode($user->nama_lengkap ?? 'Admin') . '&color=FFFFFF&background=DC2626&size=40';
                            }
                        @endphp
                        <img src="{{ $photoUrl }}" alt="Admin Profile" class="h-10 w-10 rounded-full mr-2"> 
                            <div class="flex flex-col text-sm hidden lg:flex items-start"> 
                                <span class="font-semibold text-gray-900 truncate max-w-[120px]">{{ Auth::guard('admin_users')->user()->nama_lengkap ?? 'Admin' }}</span>
                                <span class="text-gray-500 text-xs">{{ Auth::guard('admin_users')->user()->no_sap ?? Auth::guard('admin_users')->user()->email ?? 'N/A'}}</span>
                            </div>
                            <svg class="h-4 w-4 ml-2 text-gray-500 hidden lg:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div id="profileDropdownMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl overflow-hidden z-20 border border-gray-200 hidden">
                            <a href="{{ route('admin.profile.edit') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 border-b">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Kelola Profil
                            </a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="flex items-center w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-4 sm:p-6 flex-1 overflow-y-auto">
                {{-- Flash Message/Alerts --}}
                @if (session('error'))
                    <div id="alert-error" class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50" role="alert">
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
                    <div id="alert-success" class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50" role="alert">
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
            // Pengaturan Toggle Submenu
            const menuConfigs = [
                { toggle: 'toggleKaryawan', submenu: 'submenuKaryawan', arrow: 'arrowKaryawan', routeActive: {{ request()->routeIs('karyawan.*') ? 'true' : 'false' }} },
                { toggle: 'toggleJadwal', submenu: 'submenuJadwal', arrow: 'arrowJadwal', routeActive: {{ request()->routeIs('jadwal.*') || request()->routeIs('scan.qr') ? 'true' : 'false' }} },
                { toggle: 'toggleAdmin', submenu: 'submenuAdmin', arrow: 'arrowAdmin', routeActive: {{ request()->routeIs(['admin.create', 'admin.tambah-dokter', 'paket-poli']) ? 'true' : 'false' }} },
                { toggle: 'toggleLingkungan', submenu: 'submenuLingkungan', arrow: 'arrowLingkungan', routeActive: {{ request()->routeIs('pemantauan.*') ? 'true' : 'false' }} },
                { toggle: 'toggleNotifikasi', submenu: 'submenuNotifikasi', arrow: 'arrowNotifikasi', routeActive: {{ request()->routeIs('notifications.*') ? 'true' : 'false' }} }
            ];

            function toggleSubmenu(submenu, arrow, forceOpen = false) {
                if (!submenu || !arrow) return;
                
                const isCurrentlyOpen = submenu.style.display === 'block';

                if (forceOpen || !isCurrentlyOpen) {
                    // Tutup semua submenu lain kecuali yang aktif saat ini
                    menuConfigs.forEach(config => {
                        const otherSubmenu = document.getElementById(config.submenu);
                        const otherArrow = document.getElementById(config.arrow);
                        if (otherSubmenu && otherSubmenu !== submenu) {
                            otherSubmenu.style.display = 'none';
                            otherArrow && otherArrow.classList.remove('rotate-180');
                        }
                    });

                    // Buka submenu yang dipilih
                    submenu.style.display = 'block'; 
                    arrow.classList.add('rotate-180');
                } else {
                    submenu.style.display = 'none'; 
                    arrow.classList.remove('rotate-180');
                }
            }
            
            menuConfigs.forEach(config => {
                const toggleBtn = document.getElementById(config.toggle);
                const submenu = document.getElementById(config.submenu);
                const arrow = document.getElementById(config.arrow);

                if (toggleBtn) {
                    // Inisialisasi status menu saat load
                    if (config.routeActive === 'true' && submenu) {
                        submenu.style.display = 'block';
                        arrow && arrow.classList.add('rotate-180');
                    } else if(submenu) {
                        submenu.style.display = 'none';
                    }
                    
                    // Event listener untuk toggle manual
                    toggleBtn.addEventListener('click', () => {
                        toggleSubmenu(submenu, arrow);
                    });

                    // Tambahkan event listener untuk link submenu
                    const submenuLinks = submenu ? submenu.querySelectorAll('a') : [];
                    submenuLinks.forEach(link => {
                        link.addEventListener('click', () => {
                            // Tutup sidebar setelah mengklik link submenu di mobile
                            if (window.innerWidth < 1024) { 
                                toggleMobileSidebar(); 
                            }
                        });
                    });
                }
            });

            // Pengaturan Dropdown Header
            var notificationDropdownButton = document.getElementById('notificationDropdownButton');
            var notificationDropdownMenu = document.getElementById('notificationDropdownMenu');
            var profileDropdownButton = document.getElementById('profileDropdownButton');
            var profileDropdownMenu = document.getElementById('profileDropdownMenu');
            
            function toggleDropdown(menu) {
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
            
            if (notificationDropdownButton) {
                notificationDropdownButton.addEventListener('click', function(event) {
                    event.stopPropagation();
                    toggleDropdown(notificationDropdownMenu);
                });
            }

            if (profileDropdownButton) {
                profileDropdownButton.addEventListener('click', function(event) {
                    event.stopPropagation();
                    toggleDropdown(profileDropdownMenu);
                });
            }

            window.addEventListener('click', function(event) {
                if (notificationDropdownMenu && !notificationDropdownMenu.contains(event.target) && !notificationDropdownButton.contains(event.target)) {
                    notificationDropdownMenu.classList.add('hidden');
                }
                if (profileDropdownMenu && !profileDropdownMenu.contains(event.target) && !profileDropdownButton.contains(event.target)) {
                    profileDropdownMenu.classList.add('hidden');
                }
            });


            // Pengaturan Sidebar Mobile (Drawer)
            var sidebarToggle = document.getElementById('sidebarToggle');
            var sidebar = document.getElementById('sidebar');
            var sidebarOverlay = document.getElementById('sidebarOverlay');
            var body = document.body;
            
            function toggleMobileSidebar() {
                const isSidebarOpen = sidebar.classList.toggle('sidebar-open');
                sidebarOverlay.classList.toggle('show');
                
                if (isSidebarOpen) {
                    body.classList.add('sidebar-mobile-open');
                } else {
                    body.classList.remove('sidebar-mobile-open');
                }
            }

            if (sidebarToggle && sidebar && sidebarOverlay) {
                sidebarToggle.addEventListener('click', toggleMobileSidebar);
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', toggleMobileSidebar);
            }

            // Nonaktifkan sidebar mobile dan hapus kelas 'body' jika beralih ke desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) { // lg breakpoint
                    sidebar.classList.remove('sidebar-open');
                    sidebarOverlay.classList.remove('show');
                    body.classList.remove('sidebar-mobile-open');
                }
            });

        });

        // Livewire Listeners
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