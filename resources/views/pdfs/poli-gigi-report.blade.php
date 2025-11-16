<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pemeriksaan Gigi {{ $patient->nama_lengkap ?? $patient->nama_karyawan ??'N/A' }}</title>
    <style>
        body { 
            font-family: 'Arial', sans-serif; 
            font-size: 10pt; 
            margin: 0; 
            padding: 20px;
            line-height: 1.3; 
        }
        .page { width: 100%; max-width: 800px; margin: 0 auto; }
        
        /* --- HEADER DAN LOGO BARU --- */
        .header { 
            width: 100%;
            border-bottom:  1px solid #333; /* Garis ganda yang lebih menarik */
            padding-bottom: 8px; 
            margin-bottom: 10px; 
            color: #333;
            border-collapse: collapse;
        }
        .header td {
            vertical-align: middle; /* Memastikan semua konten sel sejajar di tengah */
            padding: 0;
            line-height: 1.2;
        }
        .logo-box {
            width: 10%;
            text-align: left;
        }
        .logo {
            width: 60px; /* Ukuran standar logo untuk laporan */
            height: auto;
        }
        .header-text {
            width: 90%;
            text-align: center;
            font-size: 8.5pt;
        }
        .header-text p { margin: 1px 0; }

        /* PENGATURAN TEKS HEADER KHUSUS */
        .header-main { 
            font-size: 20pt; 
            font-weight: bold; 
            margin: 0; 
            color: #000;
        }
        .header-sub { 
            font-size: 18pt; 
            font-weight: 500; 
            margin: 0;
        }
        .header-address { 
            font-size: 15pt; 
            margin: 0;
        }
/*         --- AKHIR PERBAIKAN HEADER --- */

        h1 { 
            font-size: 13pt; 
            text-align: center; 
            margin: 8px 0 10px 0; 
            color: #2C3E50;
            font-weight: 900;
        } /* Judul lebih besar */
        
        .patient-info, .exam-section { 
            margin-bottom: 10px; 
            padding: 3px 0; 
        }
        .patient-info table { 
            width: 100%; 
            font-size: 9.5pt; 
            border-collapse: collapse; 
        }
        .patient-info td { padding: 1px 5px; }
        .label { 
            font-weight: 600; 
            width: 120px; 
            color: #555; 
    }
        
        .section-title { 
            font-weight: bold; 
            margin-top: 3px; 
            margin-bottom: 4px; 
            font-size: 10pt; 
            color: #34495E; 
            border-bottom: 1px dashed #bbb;
            padding-bottom: 2px;
        }
        .result-box { 
            min-height: 20px; 
            padding: 5px; 
            border-bottom: 1px dashed #888; /* Gunakan garis putus-putus */
            margin-bottom: 10px;
        }

        .exam-section table td { padding: 2px 5px; } /* Padding internal tabel dikecilkan */
        
         /* Peta Gigi (Odontogram) */
        .dental-chart-container {
            margin: 8px 0; 
            padding: 0;
            border: none; /* Hilangkan border */
        }

         /* Gaya untuk peta gigi di PDF */
        .dental-chart-container { 
            margin: 8px 0; 
            padding: 0;
            border: none; 
        }
        .tooth-box {
        position: relative;
        width: 100%;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #666;
        border-radius: 4px;
        font-size: 8pt;
        font-weight: normal;
        background-color: #E5E7EB; /* Default grey */
        color: #1F2937;
        }
        .dental-chart-container .section-title {
            margin-top: 0;
            border: none;
            text-align: left;
        }

        /* Styling gigi klinis: Disuntikkan sebagai string CSS yang sudah diproses dari Livewire */
        {{ $dynamicCss }}
        
        /* Tanda Tangan */
        .signature-box {
            text-align: right; 
            margin-top: 30px; 
            font-size: 9.5pt;
        }
        .signature-area {
            height: 45px; 
            margin-top: 3px;
        }
        .doctor-name {
             text-decoration: underline; 
            margin-bottom: 0; 
            font-weight: bold;
        }
        /* PENTING: Pengaturan print untuk meminimalisir pemotongan */
        @media print {
            html, body {
                height: 100%;
                overflow: hidden;
            }
        }

    </style>
</head>
<body>
    <div class="page">
    {{-- Mengganti struktur div flex dengan tabel untuk header yang lebih stabil di cetak --}}
        <table class="header">
            <tr>
                {{-- Logo Kiri --}}
                <td class="logo-box">
                    <img src="{{ public_path('images/logo-semen-tonasa.png') }}" alt="Logo Semen Tonasa" class="logo">
                    </td>               
                    {{-- Teks Header Tengah --}}
                    <td class="header-text">
                        <p class="header-main">KLINIK SEMEN TONASA MEDICAL CENTRE</p>
                        <p class="header-sub">PT SEMEN TONASA</p>
                        <p class="header-address">Jl. Jend. Sudirman No. 1, Kab. Pangkep, Sulawesi Selatan</p>
                    </td>               
                    <td class="logo-box" style="text-align: right;">
                        <img src="{{ public_path('images/logo-stmc.png') }}" alt="Logo STMC" class="logo">
                        <div style="width: 60px; height: 1px; display: inline-block;"></div>
                    </td>
            </tr>
        </table>

        <h1>LAPORAN PEMERIKSAAN GIGI</h1>

        {{-- Data Pasien --}}
        <div class="patient-info">
            <table style="width: 100%;">
                <tr>
                    <td class="label">SAP</td><td>:</td><td>{{ $patient->no_sap ??  'N/A' }}</td>
                    <td class="label">Nama Pasien</td><td>:</td><td>{{ $patient->nama_lengkap ?? $patient->nama_karyawan?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">NIK</td><td>:</td><td>{{ $patient->nik_pasien ?? $patient->nik_karyawan ?? 'N/A' }}</td>
                    <td class="label">Umur / JK</td><td>:</td><td>
                        {{ $patient->tanggal_lahir ? \Carbon\Carbon::parse($patient->tanggal_lahir)->age . ' Tahun' : '-' }} / {{ $patient->jenis_kelamin ?? 'N/A' }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Perusahaan</td><td>:</td><td>{{ $instansiPasien ?? 'N/A' }}</td>
                    <td class="label">No. Hp</td><td>:</td><td>{{ $patient->no_hp ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Tgl. Pemeriksaan</td><td>:</td><td>{{ now()->format('d-m-Y') }}</td>
                </tr>
            </table>
        </div>
        
        {{-- Checkbox Karyawan/Non-Karyawan Dinamis --}}
        <div style="margin-bottom: 8px; font-size: 9pt; color: #444; padding: 5px 0;">
             Tipe Pasien: 
            <span style="font-weight: bold; margin-right: 15px;">
                @if ($isKaryawan) [Karyawan] @else [Non-Karyawan/Umum] @endif
            </span>
        </div>

        {{-- 1. Pemeriksaan Ekstra Oral --}}
        <div class="exam-section">
            <p class="section-title">1. PEMERIKSAAN EKSTRA ORAL</p>
            <table style="width: 100%; font-size: 10pt;">
                 <tr>
                     <td style="width: 40%; padding-left: 10px;">Kelenjar Submandibular:</td>
                    <td style="width: 60%;">{{ $ekstraOral['kelenjar_submandibular'] }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 10px;">Kelenjar Leher:</td>
                    <td>{{ $ekstraOral['kelenjar_leher'] }}</td>
                </tr>
            </table>
        </div>
        
        {{-- 2. Pemeriksaan Intra Oral --}}
        <div class="exam-section">
            <p class="section-title">2. PEMERIKSAAN INTRA ORAL</p>
            <table style="width: 100%; font-size: 10pt;">
                <!-- ... (tabel intra oral tetap sama) ... -->
                @foreach (['oklusi', 'torus_palatinus', 'torus_mandibularis', 'palatum', 'diastema', 'gigi_anomali', 'ginggiva', 'karang_gigi', 'lain_lain'] as $key)
                    <tr>
                        <td style="width: 40%; font-weight: 500; padding-top: 3px; padding-left: 10px;">{{ ucwords(str_replace('_', ' ', $key)) }}:</td>
                        <td>{{ $intraOral[$key] ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </table>
        </div>

        {{-- Peta Gigi di PDF (Menggunakan tampilan kotak sederhana) --}}
        <div class="dental-chart-container" style="border: 1px solid #ccc; padding: 10px; border-radius: 5px;">
             <p class="section-title" style="border: none; margin-bottom: 5px;">2.1. Odontogram</p>
            {{-- Menggunakan dental-chart-svg yang sudah mendukung status klinis --}}
            @include('livewire.components.dental-chart-svg')
        </div>

        {{-- 3. Keterangan Hasil Pemeriksaan --}}
        <div class="exam-section">
            <p class="section-title">3. KETERANGAN HASIL PEMERIKSAAN</p>
            <div class="result-box">
                {{ $keterangan ?? 'Tidak ada keterangan tambahan.' }}
            </div>
        </div>

        {{-- 4. Kesimpulan --}}
        <div class="exam-section">
             <p class="section-title">4. KESIMPULAN</p>
            <div class="result-box">
                {{ $kesimpulan ?? '...' }}
            </div>
        </div>
        
        <div class="signature-box">
            <p>Pangkep, {{ now()->format('d - m - Y') }}</p>
            <p>Dokter Pemeriksa</p>

            <div class="signature-area"></div>
                {{-- Nama Dokter --}}
                <p class="doctor-name">{{ $dokter->nama_lengkap ?? 'Nama Dokter' }}</p>
                {{-- <p style="margin-top: 0;">SIP: {{ $dokter->sip ?? 'N/A' }}</p> --}}
            
        </div>
    </div>
</body>
</html>
