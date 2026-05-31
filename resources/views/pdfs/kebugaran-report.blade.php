<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kebugaran Jasmani - {{ $patient->nama_lengkap ?? $patient->nama_karyawan ?? 'Pasien' }}</title>
    <style>
        :root {
            --color-primary: #1e293b; /* Slate 800 */
            --color-accent: #dc2626; /* Red 600 */
            --color-light: #f1f5f9; /* Slate 100 */
            --color-text: #334155; 
            --color-text-light: #64748b;
        }

        @page { size: A4; margin: 40px; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; padding: 0; line-height: 1.5; color: var(--color-text); font-size: 10pt;}
        
        /* HEADER */
        .header-table { width: 100%; border-collapse: collapse; border-bottom: 3px solid var(--color-primary); padding-bottom: 15px; margin-bottom: 25px; }
        .header-table td { vertical-align: middle; }
        .logo-cell { width: 15%; text-align: center; }
        .text-cell { width: 70%; text-align: center; }
        .logo-box { width: 65px; height: 65px; display: inline-block; }
        .logo-box img { width: 100%; height: 100%; object-fit: contain; }
        
        .header-text h1 { color: var(--color-primary); margin: 0 0 5px 0; font-size: 16pt; font-weight: 900; }
        .header-text .sub-title { font-size: 11pt; font-weight: bold; color: var(--color-accent); margin: 0; letter-spacing: 1px;}
        .header-text .company { font-size: 9pt; color: var(--color-text-light); margin: 2px 0 0 0; }

        /* PATIENT INFO */
        .patient-box { background-color: var(--color-light); border-radius: 8px; padding: 15px; margin-bottom: 30px; }
        .patient-table { width: 100%; border-collapse: collapse; font-size: 10pt; }
        .patient-table td { padding: 4px 0; vertical-align: top; }
        .label-col { width: 25%; font-weight: bold; color: var(--color-primary); }
        .colon-col { width: 2%; font-weight: bold;}
        .data-col { width: 23%; font-weight: bold;}

        /* CONTENT */
        .section-title { text-align: center; font-size: 14pt; font-weight: 900; margin-bottom: 25px; color: var(--color-primary); text-transform: uppercase; text-decoration: underline;}
        .result-group-title { font-size: 12pt; margin-bottom: 15px; font-weight: bold; color: var(--color-accent); border-bottom: 1px solid #cbd5e1; padding-bottom: 5px; }
        
        .result-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; margin-left: 10px;}
        .result-table td { padding: 8px 0; border-bottom: 1px dashed #e2e8f0; font-size: 11pt;}
        .result-table .lbl { width: 60%; color: var(--color-text);}
        .result-table .val { width: 40%; font-weight: bold; color: var(--color-primary); text-align: right; padding-right: 20px;}

        /* KESIMPULAN BOX */
        .category-box { margin-top: 40px; padding: 25px; background-color: #f8fafc; border: 2px dashed var(--color-accent); border-radius: 12px; text-align: center; }
        .category-box h3 { margin: 0 0 15px 0; color: var(--color-primary); font-size: 14pt; }
        .category-result { font-size: 20pt; font-weight: 900; color: var(--color-accent); display: block; background: white; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0;}
        
        /* SIGNATURE */
        .signer-box { width: 100%; margin-top: 60px; }
        .signer-table { width: 100%; border-collapse: collapse; }
        .signer-table td { width: 50%; vertical-align: bottom; text-align: right; padding-right: 20px;}
        .signer-text { font-size: 11pt; margin: 0; }
        .doctor-name { font-weight: bold; text-decoration: underline; margin-top: 80px; margin-bottom: 0;}
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <div class="logo-box"><img src="{{ public_path('images/logo-semen-tonasa.png') }}" alt="Logo Kiri"></div>
            </td>
            <td class="text-cell">
                <div class="header-text">
                    <h1>SEMEN TONASA MEDICAL CENTRE</h1>
                    <p class="sub-title">MEDICAL CHECK UP REPORT</p>
                    <p class="company">PT SEMEN TONASA</p>
                </div>
            </td>
            <td class="logo-cell">
                <div class="logo-box"><img src="{{ public_path('images/logo-stmc.png') }}" alt="Logo Kanan"></div>
            </td>
        </tr>
    </table>

    <div class="patient-box">
        <table class="patient-table">
            <tr>
                <td class="label-col">Nama Pasien</td><td class="colon-col">:</td><td class="data-col">{{ $patient->nama_lengkap ?? $patient->nama_karyawan ?? 'N/A' }}</td>
                <td class="label-col">NIK / SAP</td><td class="colon-col">:</td><td class="data-col">{{ $patient->nik_pasien ?? $patient->no_sap ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label-col">Tgl Lahir / Umur</td><td class="colon-col">:</td><td class="data-col">{{ \Carbon\Carbon::parse($patient->tanggal_lahir)->format('d-m-Y') }} ({{ \Carbon\Carbon::parse($patient->tanggal_lahir)->age }} Thn)</td>
                <td class="label-col">Jenis Kelamin</td><td class="colon-col">:</td><td class="data-col">{{ $patient->jenis_kelamin ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label-col">Perusahaan / Unit</td><td class="colon-col">:</td><td colspan="4" class="data-col">{{ $instansiPasien ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="section-title">Hasil Pemeriksaan Kesegaran Jasmani</div>
    <h3 class="result-group-title">Rincian Uji Kebugaran</h3>
    
    <table class="result-table">
        <tr>
            <td class="lbl">Lama Pemeriksaan</td>
            <td class="val">{{ $kebugaranResult->durasi_menit }} Menit</td>
        </tr>
        <tr>
            <td class="lbl">Beban Latihan</td>
            <td class="val">{{ $kebugaranResult->beban_latihan }} Level</td>
        </tr>
        <tr>
            <td class="lbl">Jumlah Denyut Nadi (per menit terakhir)</td>
            <td class="val">{{ $kebugaranResult->denyut_nadi }} x/mnt</td>
        </tr>
        <tr>
            <td class="lbl" style="border-bottom: none;">Kebutuhan VO2 Maksimal (Volume Oksigen)</td>
            <td class="val" style="border-bottom: none;">{{ number_format($kebugaranResult->vo2_max, 2) }} Liter/mnt</td>
        </tr>
    </table>

    <div class="category-box">
        <h3>Indeks Kesimpulan Kesegaran Jasmani</h3>
        <span class="category-result">
            {{ number_format($kebugaranResult->indeks_kebugaran, 2) }} kg/m/min <br>
            <span style="font-size: 16pt; color: #475569; display: block; margin-top: 5px;">Kategori: {{ strtoupper($kebugaranResult->kategori) }}</span>
        </span>
    </div>

    <div class="signer-box">
        <table class="signer-table">
            <tr>
                <td></td>
                <td>
                    <p class="signer-text">Pangkep, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
                    <p class="signer-text">Dokter Pemeriksa,</p>
                    <p class="signer-text doctor-name">(________________________)</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>