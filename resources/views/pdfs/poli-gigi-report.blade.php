<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pemeriksaan Gigi - {{ $patient->nama_lengkap ?? $patient->nama_karyawan ?? 'N/A' }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 9.5pt; margin: 0; padding: 20px 30px; color: #334155; }
        .page { width: 100%; max-width: 800px; margin: 0 auto; }
        
        .header { width: 100%; border-bottom: 3px solid #0f4a7b; padding-bottom: 10px; margin-bottom: 20px; border-collapse: collapse; }
        .header td { vertical-align: middle; }
        .logo { width: 65px; height: auto; }
        .header-text { text-align: center; }
        .header-main { font-size: 16pt; font-weight: 900; color: #0f4a7b; margin: 0; letter-spacing: 1px; }
        .header-sub { font-size: 11pt; font-weight: bold; color: #475569; margin: 4px 0; }
        
        .document-title { text-align: center; font-size: 12pt; font-weight: bold; color: #1e293b; text-transform: uppercase; margin-bottom: 15px; padding: 8px; background-color: #f1f5f9; border-radius: 4px; border: 1px solid #e2e8f0; }

        .patient-card { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .patient-card td { padding: 5px 8px; border: 1px solid #cbd5e1; font-size: 9pt; }
        .patient-card .label { background-color: #f8fafc; font-weight: bold; color: #475569; width: 15%; }
        .patient-card .value { font-weight: 500; color: #0f172a; width: 35%; }
        
        .badge { display: inline-block; padding: 3px 8px; border-radius: 12px; font-size: 8pt; font-weight: bold; background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }

        .section-title { font-size: 10.5pt; font-weight: bold; color: #ffffff; background-color: #0f4a7b; padding: 5px 10px; margin: 0 0 10px 0; border-radius: 3px; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 9pt; }
        .data-table td { padding: 5px 10px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        .data-table .col-label { width: 30%; font-weight: bold; color: #64748b; background-color: #f8fafc; border-right: 1px solid #e2e8f0; }
        .data-table .col-value { width: 70%; color: #1e293b; font-weight: bold; }

        .dental-chart-container { border: 2px dashed #cbd5e1; padding: 15px; border-radius: 6px; text-align: center; margin-bottom: 20px; background-color: #f8fafc; }
        
        .result-box { background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 10px 15px; margin-bottom: 20px; font-size: 9.5pt; color: #78350f; }

        .signature-section { width: 100%; margin-top: 30px; border-collapse: collapse; page-break-inside: avoid; }
        .signature-section td { width: 50%; text-align: center; vertical-align: bottom; }
        .sign-area { height: 60px; }
        .doctor-name { font-weight: bold; text-decoration: underline; color: #0f4a7b; margin: 0; }

        {{ $dynamicCss }}
    </style>
</head>
<body>
    <div class="page">
        <!-- HEADER -->
        <table class="header">
            <tr>
                <td style="width: 15%; text-align: left;"><img src="{{ public_path('images/logo-semen-tonasa.png') }}" class="logo"></td>
                <td style="width: 70%;" class="header-text">
                    <p class="header-main">SEMEN TONASA MEDICAL CENTRE</p>
                    <p class="header-sub">KLINIK UTAMA PT SEMEN TONASA</p>
                </td>
                <td style="width: 15%; text-align: right;"><img src="{{ public_path('images/logo-stmc.png') }}" class="logo"></td>
            </tr>
        </table>

        <div class="document-title">Laporan Hasil Pemeriksaan Poli Gigi Lengkap</div>

        <!-- INFO PASIEN -->
        <div style="margin-bottom: 8px;">
            <span class="badge">@if ($isKaryawan) PASIEN KARYAWAN @else PASIEN UMUM / NON-KARYAWAN @endif</span>
        </div>
        <table class="patient-card">
            <tr>
                <td class="label">Nama Pasien</td><td class="value">{{ $patient->nama_lengkap ?? $patient->nama_karyawan ?? 'N/A' }}</td>
                <td class="label">NIK / SAP</td><td class="value">{{ $patient->nik_pasien ?? $patient->nik_karyawan ?? 'N/A' }} / {{ $patient->no_sap ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Umur / Kelamin</td><td class="value">{{ $patient->tanggal_lahir ? \Carbon\Carbon::parse($patient->tanggal_lahir)->age . ' Tahun' : '-' }} / {{ $patient->jenis_kelamin ?? 'N/A' }}</td>
                <td class="label">Tgl. Periksa</td><td class="value">{{ now()->format('d M Y') }}</td>
            </tr>
            <tr>
                <td class="label">Perusahaan</td><td colspan="3" class="value">{{ $instansiPasien ?? 'N/A' }}</td>
            </tr>
        </table>

        <!-- 1. EKSTRA ORAL -->
        <div class="section-title">1. PEMERIKSAAN EKSTRA ORAL</div>
        <table class="data-table">
            <tr>
                <td class="col-label">Kelenjar Submandibular</td><td class="col-value">: {{ $ekstraOral['kelenjar_submandibular'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Kelenjar Leher</td><td class="col-value">: {{ $ekstraOral['kelenjar_leher'] ?? '-' }}</td>
            </tr>
        </table>

        <!-- 2. INTRA ORAL -->
        <div style="page-break-inside: avoid;">
            <div class="section-title">2. PEMERIKSAAN INTRA ORAL</div>
            <table class="data-table">
                @foreach (['oklusi', 'torus_palatinus', 'torus_mandibularis', 'palatum', 'diastema', 'gigi_anomali', 'ginggiva', 'karang_gigi', 'lain_lain'] as $key)
                <tr>
                    <td class="col-label">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                    <td class="col-value">: {{ $intraOral[$key] ?? '-' }}</td>
                </tr>
                @endforeach
            </table>
        </div>

        <!-- 3. ODONTOGRAM -->
        <div style="page-break-inside: avoid;">
            <div class="section-title">3. ODONTOGRAM (PETA GIGI)</div>
            <div class="dental-chart-container">
                @include('livewire.components.dental-chart-svg')
            </div>
        </div>

        <!-- 4. KESIMPULAN -->
        <div style="page-break-inside: avoid;">
            <div class="section-title">4. KESIMPULAN & SARAN TINDAK LANJUT</div>
            <div class="result-box">
                <strong>Kesimpulan Klinis:</strong><br>
                {{ $kesimpulan ?? 'Belum ada kesimpulan medis yang dicatat.' }}
                <br><br>
                <strong>Keterangan Tambahan / Saran:</strong><br>
                {{ $keterangan ?? 'Tidak ada keterangan tambahan.' }}
            </div>
            
            <!-- TANDA TANGAN -->
            <table class="signature-section">
                <tr>
                    <td></td>
                    <td>
                        <p style="margin: 0;">Pangkep, {{ now()->format('d F Y') }}</p>
                        <p style="margin: 0; color: #64748b;">Dokter Pemeriksa,</p>
                        <div class="sign-area"></div>
                        <p class="doctor-name">{{ $dokter->nama_lengkap ?? 'Nama Dokter' }}</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>