@extends('layouts.app') 

@section('content')

    <div class="bg-gray-100 min-h-screen p-4 lg:p-6">
        <div class="container mx-auto">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 mb-6 lg:mb-8">Dashboard Admin</h1>

            <div class="mb-6 lg:mb-8 grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                
                {{-- KARTU 1: Karyawan Belum MCU --}}
                <div class="bg-white rounded-xl shadow-lg p-4 lg:p-6 flex flex-col justify-center transition-transform duration-200 hover:scale-[1.03] border-t-4 border-red-500">
                    <p class="text-xs lg:text-sm font-medium text-red-500 mb-1">Karyawan Belum MCU (1 Thn Terakhir)</p>
                    <p class="text-3xl lg:text-4xl font-bold text-red-700">{{ $karyawanBelumMcu }}</p>
                    <p class="text-xs lg:text-sm text-gray-500 mt-2">Dari total {{ $totalKaryawan }} Karyawan</p>
                </div>

                {{-- KARTU 2: Karyawan Sudah MCU --}}
                <div class="bg-white rounded-xl shadow-lg p-4 lg:p-6 flex flex-col justify-center transition-transform duration-200 hover:scale-[1.03] border-t-4 border-green-500">
                    <p class="text-xs lg:text-sm font-medium text-green-500 mb-1">Karyawan Sudah MCU</p>
                    <p class="text-3xl lg:text-4xl font-bold text-green-700">{{ $karyawanSudahMcu }}</p>
                    <p class="text-xs lg:text-sm text-gray-500 mt-2">Progress: {{ $persenSelesai }}%</p>
                </div>

                {{-- KARTU 3: Total Area Bermasalah (Pemantauan Lingkungan) --}}
                <a href="{{ route('pemantauan.index') }}" 
                    id="lingkungan-card" 
                    class="bg-white rounded-xl shadow-lg p-4 lg:p-6 flex flex-col justify-center transition-transform duration-200 hover:scale-[1.03] border-t-4 
                    {{ $areaBermasalah > 0 ? 'border-orange-500 hover:shadow-orange-200' : 'border-gray-300' }}"
                >
                    <p class="text-xs lg:text-sm font-medium {{ $areaBermasalah > 0 ? 'text-orange-500' : 'text-gray-500' }} mb-1">
                        Total Area Bermasalah
                    </p>
                    <p class="text-3xl lg:text-4xl font-bold {{ $areaBermasalah > 0 ? 'text-orange-700' : 'text-gray-600' }}" id="area-count-display">
                        {{ $areaBermasalah }}
                    </p>
                    <p class="text-xs lg:text-sm text-gray-500 mt-2" id="area-name-display">
                        @if ($areaBermasalah > 0)
                            Tekan untuk detail Area
                        @else
                            Semua area aman dari NAB
                        @endif
                    </p>
                </a>
                
                {{-- KARTU 4: Pasien Hari Ini (Gaya yang berbeda di Mobile/Desktop) --}}
                <div class="bg-white rounded-xl shadow-md p-4 lg:p-6 flex items-center justify-between transition-transform duration-200 hover:scale-105">
                    <div>
                        <p class="text-xs lg:text-sm font-medium text-gray-500 mb-1">Pasien Hari Ini</p>
                        <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{$totalPasienHariIni}}</p>
                    </div>
                    <div class="bg-indigo-100 p-3 rounded-full">
                        <svg class="h-6 w-6 lg:h-8 lg:w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                </div>

                {{-- KARTU 5: Total Pengajuan Jadwal --}}
                <div class="bg-white rounded-xl shadow-md p-4 lg:p-6 flex items-center justify-between transition-transform duration-200 hover:scale-105">
                    <div>
                        <p class="text-xs lg:text-sm font-medium text-gray-500 mb-1">Total Pengajuan Jadwal</p>
                        <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{$totalJadwalMcu}}</p>
                    </div>
                    <div class="bg-pink-100 p-3 rounded-full">
                        <svg class="h-6 w-6 lg:h-8 lg:w-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>

                {{-- KARTU 6: Total Karyawan --}}
                <div class="bg-white rounded-xl shadow-md p-4 lg:p-6 flex items-center justify-between transition-transform duration-200 hover:scale-105">
                    <div>
                        <p class="text-xs lg:text-sm font-medium text-gray-500 mb-1">Total Karyawan</p>
                        <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $totalKaryawan }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="h-6 w-6 lg:h-8 lg:w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>

                {{-- KARTU 7: Total Non PTST --}}
                <div class="bg-white rounded-xl shadow-md p-4 lg:p-6 flex items-center justify-between transition-transform duration-200 hover:scale-105">
                    <div>
                        <p class="text-xs lg:text-sm font-medium text-gray-500 mb-1">Total Non PTST</p>
                        <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $totalPesertaMcu }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="h-6 w-6 lg:h-8 lg:w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>

                {{-- KARTU 8: Unit Kerja Aktif --}}
                <div class="bg-white rounded-xl shadow-md p-4 lg:p-6 flex items-center justify-between transition-transform duration-200 hover:scale-105">
                    <div>
                        <p class="text-xs lg:text-sm font-medium text-gray-500 mb-1">Unit Kerja Aktif</p>
                        <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{$totalUnitKerja}}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <svg class="h-6 w-6 lg:h-8 lg:w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m-4 0v-7a1 1 0 011-1h2a1 1 0 011 1v7m-4 0v-7a1 1 0 011-1h2a1 1 0 011 1v7m-4 0h6"></path>
                        </svg>
                    </div>
                </div>
                
                {{-- KARTU 9: Departemen Aktif --}}
                <div class="bg-white rounded-xl shadow-md p-4 lg:p-6 flex items-center justify-between transition-transform duration-200 hover:scale-105">
                    <div>
                        <p class="text-xs lg:text-sm font-medium text-gray-500 mb-1">Departemen Aktif</p>
                        <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{$totalDepartemen}}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <svg class="h-6 w-6 lg:h-8 lg:w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                        </svg>
                    </div>
                </div>

                {{-- KARTU 10: Dokter (Ini adalah duplikat dari Pasien Hari Ini di kode asli, saya asumsikan ini Dokter) --}}
                <div class="bg-white rounded-xl shadow-md p-4 lg:p-6 flex items-center justify-between transition-transform duration-200 hover:scale-105">
                    <div>
                        <p class="text-xs lg:text-sm font-medium text-gray-500 mb-1">Dokter</p>
                        <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{$totalDokter}}</p>
                    </div>
                    <div class="bg-cyan-100 p-3 rounded-full">
                        <svg class="h-6 w-6 lg:h-8 lg:w-8 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            {{-- BAGIAN BAWAH: GRAFIK & NOTIFIKASI --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
                <div class="bg-white rounded-xl shadow-lg p-4 lg:p-6 lg:col-span-2">
                    <h2 class="text-base lg:text-lg font-semibold text-gray-800 mb-4">Tren Pasien MCU Tahunan</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
                        
                        <div>
                            <p class="text-xs lg:text-sm font-medium text-gray-600 mb-2">Total Pasien MCU (Karyawan & Non-Karyawan)</p>
                            <div style="height: 250px;"><canvas id="totalMcuChart" class="w-full h-full"></canvas></div>
                        </div>

                        <div>
                            <p class="text-xs lg:text-sm font-medium text-gray-600 mb-2">Pasien Berdasarkan Kategori</p>
                            <div style="height: 250px;"><canvas id="categoryMcuChart" class="w-full h-full"></canvas></div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-4 lg:p-6">
                    <h2 class="text-base lg:text-lg font-semibold text-gray-800 mb-4">Manajemen Notifikasi</h2>
                    
                    <div class="space-y-4">
                        <p class="text-xs text-gray-600">Gunakan fitur ini untuk mengirim pengingat jadwal MCU kepada karyawan melalui email dan Aplikasi Mobile STMC (Flutter).</p>
                        
                        <div class="flex flex-col">
                            <a href="#" class="inline-flex items-center justify-center px-3 py-1.5 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 transition ease-in-out duration-150">
                                <svg class="h-3 w-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                Kirim Notif Pengingat Besok
                            </a>
                            <p class="text-xs text-gray-500 mt-1 text-center">Akan memproses jadwal MCU yang jatuh tempo besok (Aplikasi & Email).</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Memuat Library Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

<script>
    // Data dari Laravel Controller (JSON encoding)
    const labels = @json($years);
    const totalData = @json($mcuCountsByYear);
    const karyawanData = @json($karyawanCounts);
    const nonKaryawanData = @json($nonKaryawanCounts);

    // Data yang akan menampung daftar nama area
    let currentAreaNames = @json($lingkunganStatus['areaNames'] ?? []); // Ambil data awal dari Controller
    let areaIndex = 0;

    // --- Grafik 1: Total Pasien MCU Tahunan (Line Chart) ---
    const totalMcuCtx = document.getElementById('totalMcuChart');
    if (totalMcuCtx) {
        new Chart(totalMcuCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Pasien MCU',
                    data: totalData,
                    borderColor: 'rgb(59, 130, 246)', // Tailwind blue-500
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    tension: 0.3,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    // --- Grafik 2: Perbandingan Karyawan vs Non-Karyawan (Stacked Bar Chart) ---
    const categoryMcuCtx = document.getElementById('categoryMcuChart');
    if (categoryMcuCtx) {
        new Chart(categoryMcuCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Karyawan',
                        data: karyawanData,
                        backgroundColor: 'rgb(220, 38, 38)', // Tailwind red-600
                    },
                    {
                        label: 'Non-Karyawan',
                        data: nonKaryawanData,
                        backgroundColor: 'rgb(16, 185, 129)', // Tailwind green-500
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Fungsi untuk merotasi nama area setiap 3 detik
    function rotateAreaNames() {
        const nameDisplay = document.getElementById('area-name-display');
        
        if (nameDisplay && currentAreaNames.length > 0) {
            
            if (areaIndex >= currentAreaNames.length) {
                areaIndex = 0;
            }

            const areaName = currentAreaNames[areaIndex];
            nameDisplay.textContent = `⚠️ Area: ${areaName} (NAB Terlampaui)`; 
            
            areaIndex++;

        } else if (nameDisplay) {
            nameDisplay.textContent = "Semua area aman dari NAB";
        }
    }

    // Panggil rotasi setiap 3 detik
    setInterval(rotateAreaNames, 3000); 

    // Fungsi untuk me-refresh data card melalui AJAX
    function autoRefreshCard() {
        const monitoringCard = document.getElementById('lingkungan-card');
        const nameDisplay = document.getElementById('area-name-display');
        const countDisplay = document.getElementById('area-count-display');

        if (monitoringCard) {
            fetch('{{ route('dashboard.data_lingkungan') }}') 
                .then(response => response.json())
                .then(data => {
                    
                    // 1. Update data array lokal
                    currentAreaNames = data.areaNames; 

                    // 2. Update count display
                    countDisplay.textContent = data.areaBermasalah; 
                    
                    // 3. Update warna dinamis dan kelas (Logika update class Tailwind perlu ditambahkan di sini jika ada)
                    const isProblem = data.areaBermasalah > 0;
                    
                    // 4. Reset index rotasi dan jalankan rotasi awal
                    areaIndex = 0;
                    rotateAreaNames();
                })
                .catch(error => console.error('Error fetching environment data:', error));
        }
    }

    // Refresh data melalui AJAX setiap 5 detik (5000 ms)
    setInterval(autoRefreshCard, 5000); 

    // Jalankan rotasi pertama kali saat halaman dimuat
    rotateAreaNames();
</script>
@endsection