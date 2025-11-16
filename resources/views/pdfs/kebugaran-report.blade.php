<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kebugaran Jasmani - {{ $patient->nama_lengkap ?? $patient->nama_karyawan ?? 'Pasien' }}</title>
    <style>
        /* Definisi Warna Baru */
        :root {
            --color-primary: #2F4F4F; /* Biru Tua Gelap (Dark Slate Gray) */
            --color-accent: #4682B4; /* Biru Baja (Steel Blue) */
            --color-light: #E6F0F5; 
            --color-text: #2F4F4F; 
            --color-text-light: #6A7F8F;
        }

        @page { size: A4; margin: 0; }
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            line-height: 1.6; 
            color: var(--color-text); 
            position: relative;
        }
        
        /* Hapus elemen page-strip dari CSS */
        
        /* --- HEADER (TATA LETAK BERBASIS TABLE) --- */
        .header {
            /* Padding vertikal diperbesar untuk spacing yang lebih baik */
            padding: 20px 30px; 
            border-bottom: 4px solid var(--color-primary);
            margin-bottom: 20px;
        }
        
        /* Tabel untuk penyejajaran vertikal logo dan teks */
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            padding: 0;
            vertical-align: middle; /* KRITIS: Menyejajarkan semua konten ke tengah vertikal */
            /* Hapus line-height: 1.2 dari sini */
        }

        .logo-cell-left { width: 12%; text-align: left; }
        .text-cell { width: 76%; text-align: center; }
        .logo-cell-right { width: 12%; text-align: right; }

        .logo-box {
            width: 55px; 
            height: 55px;
            display: inline-block;
        }
        
        /* CSS KRITIS UNTUK MENJAGA UKURAN LOGO */
        .logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain; 
        }

        .header-text h1 { 
            color: var(--color-primary); 
            margin: 0; 
            /* Font size diperbesar */
            font-size: 20px; 
            font-weight: 700; 
            line-height: 1.5;
        }
        .header-text p { 
            font-size: 10px; 
            color: var(--color-text-light); 
            margin: 0; 
            line-height: 1.5;
        }
        
        /* --- INFORMASI PASIEN (TATA LETAK BERBASIS TABLE) --- */
        .patient-info {
            padding: 0 30px 20px 30px;
            font-size: 13px;
            border-bottom: 1px solid var(--color-light); 
            margin-bottom: 30px;
        }
        .patient-table {
            width: 100%;
            border-collapse: collapse;
        }
        .patient-table td {
            padding: 0;
            vertical-align: top;
            width: 50%;
        }
        .patient-row {
            display: block;
            margin-bottom: 5px;
        }
        .patient-label {
            width: 120px;
            font-weight: bold;
            color: var(--color-text);
            display: inline-block;
        }
        
        /* --- KONTEN HASIL --- */
        .content { padding: 0 30px; }
        .section-title { text-align: center; font-size: 20px; font-weight: bold; margin-bottom: 30px; color: var(--color-primary); text-transform: uppercase; }
        .result-group-title { font-size: 16px; margin-bottom: 15px; font-weight: bold; color: var(--color-accent); border-bottom: 2px solid var(--color-light); padding-bottom: 5px; }
        
        .result-item { 
            clear: both;
            margin-bottom: 12px;
            font-size: 14px;
        }
        .result-label { float: left; width: 380px; color: var(--color-text-light); padding-right: 10px; }
        .result-value-container { overflow: hidden; font-weight: bold; color: var(--color-text); }
        .result-colon { float: left; width: 10px; }
        .result-value-span { float: left; width: 50px; }
        .result-unit-span { overflow: hidden; }

        /* --- KATEGORI HASIL AKHIR --- */
        .category-box { margin-top: 30px; padding: 20px; background-color: var(--color-light); border: 2px solid var(--color-primary); border-radius: 8px; text-align: center; }
        .category-box h3 { margin: 0 0 10px 0; color: var(--color-primary); font-size: 18px; }
        .category-result { font-size: 24px; font-weight: 900; color: var(--color-primary); display: block; }
        
        /* --- PENANDATANGAN --- */
        .signer { 
            margin-top: 80px; 
            padding-right: 30px; 
            text-align: right; 
            font-size: 13px; 
            /* Tambahkan jarak di bawah sebelum footer */
            margin-bottom: 40px; 
        }
        .signer p { margin: 0; }
        
        /* --- FOOTER UNIK STMC (Elemen Diagonal/Strip Kiri) --- */
        .footer-left-strip { 
            position: fixed; 
            bottom: 0; 
            left: 0; 
            width: 100%; 
            height: 20px; /* Diperbesar sedikit */
        }
        .footer-shape-main { 
            position: absolute; 
            bottom: 0; 
            left: 0; 
            width: 45%; /* Diperlebar */
            height: 20px; 
            background-color: var(--color-primary); 
            clip-path: polygon(0 0, 100% 100%, 0 100%); 
        }
        .footer-shape-accent { 
            position: absolute; 
            bottom: 0; 
            left: 0; 
            width: 35%; /* Diperlebar */
            height: 15px; 
            background-color: var(--color-accent); 
            clip-path: polygon(0 0, 100% 100%, 0 100%); 
        }
        .footer-info { 
            position: absolute; 
            bottom: 5px; 
            right: 30px; 
            font-size: 9px; 
            color: var(--color-text); /* Diubah ke warna gelap agar terbaca */
        }
    </style>
</head>
<body>

    {{-- HAPUS ELEMEN VERTIKAL SISI KERTAS --}}

    <div class="header">
        {{-- MENGGUNAKAN TABEL UNTUK MENJAMIN KESEJAJARAN VERTIKAL --}}
        <table class="header-table">
            <tr>
                {{-- LOGO KIRI --}}
                <td class="logo-cell-left">
                    <div class="logo-box">
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo-semen-tonasa.png'))) }}" alt="Logo Kiri" style="width: 100%; height: 100%;">
                    </div>
                </td>
                
                {{-- TEKS TENGAH --}}
                <td class="text-cell">
                    <div class="header-text">
                        <h1>SEMEN TONASA MEDICAL CENTRE (STMC)</h1>
                        <p style="font-size: 13px; font-weight: bold; color: var(--color-primary);">MEDICAL CHECK UP REPORT</p>
                        <p style="font-size: 11px;">PT SEMEN TONASA</p>
                    </div>
                </td>

                {{-- LOGO KANAN --}}
                <td class="logo-cell-right">
                    <div class="logo-box">
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo-stmc.png'))) }}" alt="Logo Kanan" style="width: 100%; height: 100%;">
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="patient-info">
        <table class="patient-table">
            <tr>
                <td>
                    <div class="patient-row">
                        <span class="patient-label">Nama Pasien</span>
                        <span style="margin-right: 5px;">:</span>
                        <span class="patient-data">{{ $patient->nama_lengkap ?? $patient->nama_karyawan ?? 'N/A' }}</span> 
                    </div>
                    <div class="patient-row">
                        <span class="patient-label">Tgl. Lahir / Umur</span>
                        <span style="margin-right: 5px;">:</span>
                        <span class="patient-data">{{ \Carbon\Carbon::parse($patient->tanggal_lahir)->format('d M Y') }} / {{ \Carbon\Carbon::parse($patient->tanggal_lahir)->age }} Thn</span>
                    </div>
                    <div class="patient-row">
                        <span class="patient-label">Perusahaan/Unit</span>
                        <span style="margin-right: 5px;">:</span>
                        <span class="patient-data">{{ $instansiPasien ?? 'N/A' }}</span>
                    </div>
                </td>
                <td>
                    <div class="patient-row">
                        <span class="patient-label">NIK / No. SAP</span>
                        <span style="margin-right: 5px;">:</span>
                        <span class="patient-data">{{ $patient->nik_pasien ?? $patient->no_sap ?? 'N/A' }}</span>
                    </div>
                    <div class="patient-row">
                        <span class="patient-label">Jenis Kelamin</span>
                        <span style="margin-right: 5px;">:</span>
                        <span class="patient-data">{{ $patient->jenis_kelamin ?? 'N/A' }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="content">
        <div class="section-title">
            HASIL PEMERIKSAAN KESEGARAN JASMANI
        </div>

        <h3 class="result-group-title">DATA UJI KEBUGARAN</h3>
        
        <div style="padding-left: 15px;">
            {{-- Lama Pemeriksaan --}}
            <div class="result-item">
                <span class="result-label">Lama Pemeriksaan</span>
                <span class="result-colon">:</span>
                <div class="result-value-container">
                    <span class="result-value-span">{{ $kebugaranResult->durasi_menit }}</span>
                    <span class="result-unit-span">Menit</span>
                </div>
            </div>

            {{-- Beban Latihan --}}
            <div class="result-item">
                <span class="result-label">Beban Latihan</span>
                <span class="result-colon">:</span>
                <div class="result-value-container">
                    <span class="result-value-span">{{ $kebugaranResult->beban_latihan }}</span>
                    <span class="result-unit-span">Level</span>
                </div>
            </div>

            {{-- Jumlah denyut nadi --}}
            <div class="result-item">
                <span class="result-label">Jumlah denyut nadi per menit terakhir</span>
                <span class="result-colon">:</span>
                <div class="result-value-container">
                    <span class="result-value-span">{{ $kebugaranResult->denyut_nadi }}</span>
                    <span class="result-unit-span">x/menit</span>
                </div>
            </div>

            {{-- Kebutuhan VO2 Maksimal --}}
            <div class="result-item">
                <span class="result-label">Kebutuhan VO2 Maksimal (Volume Oksigen Maksimal)</span>
                <span class="result-colon">:</span>
                <div class="result-value-container">
                    <span class="result-value-span">{{ number_format($kebugaranResult->vo2_max, 2) }}</span>
                    <span class="result-unit-span">Liter / menit</span>
                </div>
            </div>
        </div>

        <div class="category-box">
            <h3>KESIMPULAN INDEKS KESEGARAN JASMANI:</h3>
            {{-- Indeks Kebugaran Jasmani (hasil perhitungan) --}}
            <span class="category-result">
                {{ number_format($kebugaranResult->indeks_kebugaran, 2) }} kg/m/min (Kategori: {{ strtoupper($kebugaranResult->kategori) }})
            </span>
        </div>

        <div class="signer">
            <p>Pangkep, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
            <p>Hormat kami,</p>
            <br><br><br><br>
            <p style="border-bottom: 1px solid var(--color-text); display: inline-block; padding-bottom: 2px;">(Nama Petugas / Dokter)</p>
        </div>
    </div>

    {{-- FOOTER UNIK STMC (Elemen Diagonal/Strip Kiri) --}}
    <div class="footer-left-strip">
        <div class="footer-shape-main"></div>
        <div class="footer-shape-accent"></div>
        <div class="footer-info">STMC - KESEGARAN JASMANI</div>
    </div>
</body>
</html>
