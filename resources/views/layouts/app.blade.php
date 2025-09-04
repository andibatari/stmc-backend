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
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .sidebar-link.active {
            background-color: #7f1d1d; /* bg-red-800 lighter */
        }
        .sub-menu {
            display: none;
        }
        /* Menyesuaikan tinggi navigasi agar tidak menyebabkan scroll pada keseluruhan sidebar */
        .sidebar-nav-container {
            flex-grow: 1;
            /* overflow-y: auto; */
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
                                    <span>Manajemen Karyawan</span>
                                </span>
                                <svg class="h-4 w-4 transform {{ request()->routeIs('karyawan.*') ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <ul id="submenuKaryawan" class="sub-menu pl-8 mt-1 {{ request()->routeIs('karyawan.*') ? 'block' : '' }}">
                                <li class="my-1">
                                    <a href="{{ route('karyawan.index') }}" class="sidebar-link block p-3 text-sm text-gray-300 hover:bg-red-700 rounded-lg mx-1 transition-colors duration-200 {{ request()->routeIs('karyawan.index') ? 'active bg-red-700' : '' }}">
                                        Daftar Karyawan
                                    </a>
                                </li>
                                <!-- Tambahkan submenu lain di sini jika perlu -->
                            </ul>
                        </li>
                        <li class="my-1">
                            <button id="toggleJadwal" class = "w-full text-left sidebar-link flex items-center justify-between p-4 text-gray-200 hover:bg-red-700 rounded-lg mx-3 transition-colors duration-200 {{ request()->routeIs('jadwal.*') ? 'active bg-red-700' : '' }}"> 
                                <span class="flex items-center">
                                    <svg class="h-5 w-5 mr-3" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/></svg>
                                    <span>Manajemen Jadwal</span>
                                </span>
                                <svg class="h-4 w-4 transform {{ request()->routeIs('jadwal.*') ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>

                            <ul id="submenuJadwal" class="sub-menu pl-8 mt-1 {{ request()->routeIs('jadwal.*') ? 'block' : '' }}">
                                <li class="my-1">
                                    <a href="{{ route('jadwal.index') }}" class="sidebar-link block p-3 text-sm text-gray-300 hover:bg-red-700 rounded-lg mx-1 transition-colors duration-200 {{ request()->routeIs('jadwal.index') ? 'active bg-red-700' : '' }}">
                                        Daftar Jadwal
                                    </a>
                                </li>
                                <!-- Tambahkan submenu lain di sini jika perlu -->
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
                    <div class="relative flex items-center mr-6">
                        <svg class="h-6 w-6 text-gray-500 hover:text-gray-700 cursor-pointer transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span class="absolute top-0 right-0 h-2 w-2 bg-red-500 rounded-full"></span>
                    </div>
                    <div class="flex items-center">
                        <img src="https://placehold.co/40x40/E5E7EB/9CA3AF?text=AU" alt="Admin" class="h-10 w-10 rounded-full mr-2">
                        <div class="flex flex-col text-sm">
                            <!-- Menampilkan nama lengkap admin yang login -->
                            <span class="font-semibold text-gray-900">{{ Auth::guard('admin_users')->user()->nama_lengkap ?? 'Admin' }}</span>
                            <!-- Menampilkan email admin yang login -->
                            <span class="text-gray-500">{{ Auth::guard('admin_users')->user()->email ?? 'admin@stmc.com' }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6 flex-1 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toggleKaryawan = document.getElementById('toggleKaryawan');
            var submenuKaryawan = document.getElementById('submenuKaryawan');
            var toggleJadwal = document.getElementById('toggleJadwal');
            var submenuJadwal = document.getElementById('submenuJadwal');
            var sidebarToggle = document.getElementById('sidebarToggle');
            var sidebar = document.getElementById('sidebar');
            var sidebarOverlay = document.getElementById('sidebarOverlay');
            var mainContentArea = document.getElementById('mainContentArea');

            // fungsi sinkronisasi sidebar dengan margin
            function syncSidebarState() {
                if (sidebar.classList.contains('sidebar-open')) {
                    mainContentArea.style.marginLeft = '16rem';
                } else {
                    mainContentArea.style.marginLeft = '0';
                }
            }

            // toggle submenu karyawan
            toggleKaryawan.addEventListener('click', function() {
                submenuKaryawan.style.display = 
                    submenuKaryawan.style.display === "block" ? "none" : "block";
            });

            // toggle submenu jadwal
            toggleJadwal.addEventListener('click', function() {
                submenuJadwal.style.display = 
                    submenuJadwal.style.display === "block" ? "none" : "block";
            });

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

    </script>
    @livewireScripts
    @stack('scripts') {{-- Pastikan baris ini ada --}}
</body>
</html>
