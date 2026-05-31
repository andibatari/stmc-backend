<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pemeriksaan Fisik - {{ $patient->nama_lengkap ?? $patient->nama_karyawan ?? 'Pasien' }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10pt; margin: 0; padding: 20px; color: #334155; }
        .page { width: 100%; max-width: 800px; margin: 0 auto; }
        
        /* HEADER PREMIUM */
        .header { width: 100%; border-bottom: 3px solid #0f4a7b; padding-bottom: 10px; margin-bottom: 20px; border-collapse: collapse; }
        .header td { vertical-align: middle; }
        .logo { width: 70px; height: auto; }
        .header-text { text-align: center; }
        .header-main { font-size: 18pt; font-weight: 900; color: #0f4a7b; margin: 0; letter-spacing: 1px; }
        .header-sub { font-size: 12pt; font-weight: bold; color: #475569; margin: 4px 0; }
        
        .document-title { text-align: center; font-size: 14pt; font-weight: bold; color: #1e293b; text-transform: uppercase; margin-bottom: 20px; padding: 10px; background-color: #f1f5f9; border-radius: 4px; border: 1px solid #e2e8f0; }

        /* INFO PASIEN (CARD STYLE) */
        .patient-card { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .patient-card td { padding: 6px 10px; border: 1px solid #cbd5e1; font-size: 9.5pt; }
        .patient-card .label { background-color: #f8fafc; font-weight: bold; color: #475569; width: 15%; }
        .patient-card .value { font-weight: 500; color: #0f172a; width: 35%; }

        /* TABEL PEMERIKSAAN KISI-KISI */
        .section-title { font-size: 11pt; font-weight: bold; color: #ffffff; background-color: #0f4a7b; padding: 6px 10px; margin: 20px 0 10px 0; border-radius: 3px; }
        .sub-title { font-size: 10pt; font-weight: bold; color: #0284c7; border-bottom: 1px solid #bae6fd; padding-bottom: 4px; margin-bottom: 8px; margin-top: 15px; }
        
        .grid-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 9pt; }
        .grid-table td { padding: 4px 5px; vertical-align: top; }
        .grid-table .label-col { width: 25%; font-weight: bold; color: #64748b; }
        .grid-table .value-col { width: 25%; color: #1e293b; font-weight: 500; border-right: 1px solid #f1f5f9; }
        
        /* PAJANAN TABLE */
        .pajanan-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 8.5pt; }
        .pajanan-table td { padding: 4px; border-bottom: 1px dotted #cbd5e1; }
        .pajanan-table .item-label { width: 75%; color: #475569; }
        .pajanan-table .item-value { width: 25%; font-weight: bold; color: #0f4a7b; text-align: right; }

        .result-box { background-color: #f0fdf4; border-left: 4px solid #16a34a; padding: 10px 15px; margin-bottom: 15px; font-size: 9.5pt; color: #14532d; }
        .signature-section { width: 100%; margin-top: 40px; border-collapse: collapse; }
        .signature-section td { width: 50%; text-align: center; vertical-align: bottom; }
        .sign-area { height: 70px; }
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

        <div class="document-title">Laporan Hasil Pemeriksaan Fisik</div>

        <!-- INFO PASIEN -->
        <table class="patient-card">
            <tr>
                <td class="label">Nama Pasien</td><td class="value">{{ $patient->nama_lengkap ?? $patient->nama_karyawan ?? 'N/A' }}</td>
                <td class="label">NIK / SAP</td><td class="value">{{ $patient->nik_pasien ?? $patient->no_sap ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Umur / Kelamin</td><td class="value">{{ \Carbon\Carbon::parse($patient->tanggal_lahir)->age }} Thn / {{ $patient->jenis_kelamin ?? 'N/A' }}</td>
                <td class="label">Tgl. Periksa</td><td class="value">{{ now()->format('d M Y') }}</td>
            </tr>
            <tr>
                <td class="label">Perusahaan</td><td colspan="3" class="value">{{ $instansiPasien ?? 'N/A' }}</td>
            </tr>
        </table>

        <!-- 1. TANDA VITAL -->
        <div class="section-title">1. TANDA VITAL & ANTROPOMETRI</div>
        @php $tv = $fisikResult->data_fisik['tanda_vital']; @endphp
        <table class="grid-table" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px;">
            <tr>
                <td class="label-col">Tinggi / Berat Badan</td><td class="value-col">: {{ $tv['tinggi_badan'] ?? '-' }} cm / {{ $tv['berat_badan'] ?? '-' }} kg</td>
                <td class="label-col" style="padding-left: 10px;">Tekanan Darah</td><td class="value-col" style="border: none;">: {{ $tv['tekanan_darah_sistol'] ?? '-' }}/{{ $tv['tekanan_darah_diastol'] ?? '-' }} mmHg</td>
            </tr>
            <tr>
                <td class="label-col">BMI / Kategori</td><td class="value-col">: {{ $tv['bmi'] ?? '-' }} ({{ $tv['kategori_bmi'] ?? '-' }})</td>
                <td class="label-col" style="padding-left: 10px;">Nadi / Pernafasan</td><td class="value-col" style="border: none;">: {{ $tv['nadi'] ?? '-' }} x/mnt / {{ $tv['pernafasan'] ?? '-' }} x/mnt</td>
            </tr>
            <tr>
                <td class="label-col">Suhu Tubuh</td><td class="value-col">: {{ $tv['suhu'] ?? '-' }} °C</td>
                <td class="label-col" style="padding-left: 10px;">SpO2 (Oksigen)</td><td class="value-col" style="border: none;">: {{ $tv['spo2'] ?? '-' }} %</td>
            </tr>
        </table>

        <!-- 2. PEMERIKSAAN SISTEM -->
        <div class="section-title">2. PEMERIKSAAN FISIK PER SISTEM</div>
        
        @php $kepala = $fisikResult->data_fisik['kepala']; $leher = $fisikResult->data_fisik['leher']; @endphp
        <div class="sub-title">A. Kepala & Leher</div>
        <table class="grid-table">
            <tr>
                <td class="label-col">Anemi / Ikterus</td><td class="value-col">: {{ $kepala['anemi'] ?? '-' }} / {{ $kepala['ikterus'] ?? '-' }}</td>
                <td class="label-col" style="padding-left: 10px;">Tonsil Kanan / Kiri</td><td class="value-col" style="border: none;">: {{ $kepala['tonsil_kanan'] ?? '-' }} / {{ $kepala['tonsil_kiri'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-col">Refleks Pupil</td><td class="value-col">: {{ $kepala['refleks_pupil'] ?? '-' }}</td>
                <td class="label-col" style="padding-left: 10px;">Tiroid / KGB</td><td class="value-col" style="border: none;">: {{ $leher['tiroid'] ?? '-' }} / {{ $leher['kelenjar_getah_bening'] ?? '-' }}</td>
            </tr>
        </table>

        @php $dada = $fisikResult->data_fisik['dada']; $paru = $fisikResult->data_fisik['paru']; @endphp
        <div class="sub-title">B. Dada & Paru</div>
        <table class="grid-table">
            <tr>
                <td class="label-col">Bunyi Jantung I/II</td><td class="value-col">: {{ $dada['bunyi_jantung_1'] ?? '-' }} / {{ $dada['bunyi_jantung_2'] ?? '-' }}</td>
                <td class="label-col" style="padding-left: 10px;">Bunyi Nafas Dasar</td><td class="value-col" style="border: none;">: {{ $paru['bunyi_nafas'] ?? '-' }}</td>
            </tr>
        </table>

        @php $abdomen = $fisikResult->data_fisik['abdomen']; $eks = $fisikResult->data_fisik['ekstremitas']; $mata = $fisikResult->data_fisik['mata']; @endphp
        <div class="sub-title">C. Abdomen, Ekstremitas & Mata</div>
        <table class="grid-table">
            <tr>
                <td class="label-col">Hati / Limpa</td><td class="value-col">: {{ $abdomen['hati'] ?? '-' }} / {{ $abdomen['limpa'] ?? '-' }}</td>
                <td class="label-col" style="padding-left: 10px;">Ekstremitas</td><td class="value-col" style="border: none;">: {{ $eks['ekstremitas'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-col">Visus Kanan / Kiri</td><td class="value-col">: {{ $mata['visus_kanan'] ?? '-' }} / {{ $mata['visus_kiri'] ?? '-' }}</td>
                <td class="label-col" style="padding-left: 10px;">Kesimpulan Mata</td><td class="value-col" style="border: none;">: {{ $mata['kesimpulan_mata'] ?? '-' }}</td>
            </tr>
        </table>

        <!-- 3. PAJANAN -->
        <div class="section-title">3. RIWAYAT PAJANAN PEKERJAAN</div>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; vertical-align: top; padding-right: 10px;">
                    <div class="sub-title" style="margin-top: 0;">Fisik & Kimia</div>
                    <table class="pajanan-table">
                        <tr><td class="item-label">Kebisingan</td><td class="item-value">{{ $pajanan['fisik']['kebisingan'] ?? '-' }}</td></tr>
                        <tr><td class="item-label">Suhu Panas/Dingin</td><td class="item-value">{{ $pajanan['fisik']['suhu_panas'] ?? '-' }}</td></tr>
                        <tr><td class="item-label">Debu / Asap</td><td class="item-value">{{ $pajanan['kimia']['debu_anorganik'] ?? '-' }}</td></tr>
                    </table>
                </td>
                <td style="width: 50%; vertical-align: top; padding-left: 10px;">
                    <div class="sub-title" style="margin-top: 0;">Ergonomis & Psikologi</div>
                    <table class="pajanan-table">
                        <tr><td class="item-label">Duduk/Berdiri Lama</td><td class="item-value">{{ $pajanan['ergonomis']['duduk_lama'] ?? '-' }}</td></tr>
                        <tr><td class="item-label">Beban Kerja</td><td class="item-value">{{ $pajanan['psikologi']['beban_kerja'] ?? '-' }}</td></tr>
                        <tr><td class="item-label">Layar Monitor > 4 Jam</td><td class="item-value">{{ $pajanan['ergonomis']['bekerja_layar_monitor'] ?? '-' }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- 4. KESIMPULAN -->
        <div class="section-title">4. KESIMPULAN & SARAN</div>
        <div class="result-box">
            <strong>Kesimpulan Diagnosa:</strong><br>
            {{ $fisikResult->kesimpulan ?? 'Belum ada kesimpulan medis yang dicatat.' }}
            <br><br>
            <strong>Tindak Lanjut:</strong><br>
            {{ $fisikResult->keterangan ?? 'Tidak ada saran tambahan.' }}
        </div>

        <!-- TANDA TANGAN -->
        <table class="signature-section">
            <tr>
                <td></td>
                <td>
                    <p style="margin: 0;">Pangkep, {{ now()->format('d F Y') }}</p>
                    <p style="margin: 0; color: #64748b;">Dokter Pemeriksa,</p>
                    <div class="sign-area"></div>
                    <p style="font-weight: bold; text-decoration: underline; color: #0f4a7b; margin:0;">{{ $dokter->nama_lengkap ?? 'Nama Dokter' }}</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>