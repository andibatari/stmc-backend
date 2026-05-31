<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pemeriksaan Fisik - {{ $patient->nama_lengkap ?? $patient->nama_karyawan ?? 'Pasien' }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 9.5pt; margin: 0; padding: 20px 30px; color: #334155; }
        .page { width: 100%; max-width: 800px; margin: 0 auto; }
        
        /* HEADER */
        .header { width: 100%; border-bottom: 3px solid #0f4a7b; padding-bottom: 10px; margin-bottom: 15px; border-collapse: collapse; }
        .header td { vertical-align: middle; }
        .logo { width: 65px; height: auto; }
        .header-text { text-align: center; }
        .header-main { font-size: 16pt; font-weight: 900; color: #0f4a7b; margin: 0; letter-spacing: 1px; }
        .header-sub { font-size: 11pt; font-weight: bold; color: #475569; margin: 3px 0; }
        
        .document-title { text-align: center; font-size: 12pt; font-weight: bold; color: #1e293b; text-transform: uppercase; margin-bottom: 15px; padding: 8px; background-color: #f1f5f9; border-radius: 4px; border: 1px solid #e2e8f0; }

        /* INFO PASIEN */
        .patient-card { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .patient-card td { padding: 5px 8px; border: 1px solid #cbd5e1; font-size: 9pt; }
        .patient-card .label { background-color: #f8fafc; font-weight: bold; color: #475569; width: 15%; }
        .patient-card .value { font-weight: 500; color: #0f172a; width: 35%; }

        /* TABEL DATA LENGKAP */
        .section-title { font-size: 10.5pt; font-weight: bold; color: #ffffff; background-color: #0f4a7b; padding: 5px 10px; margin: 15px 0 5px 0; border-radius: 3px; }
        .sub-title { font-size: 9.5pt; font-weight: bold; color: #0f4a7b; background-color: #e0f2fe; padding: 4px 8px; margin-bottom: 5px; margin-top: 10px; border-left: 3px solid #0284c7; }
        
        .grid-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; font-size: 8.5pt; }
        .grid-table td { padding: 4px 6px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        .grid-table .label-col { width: 22%; font-weight: bold; color: #64748b; }
        .grid-table .value-col { width: 28%; color: #1e293b; font-weight: bold; border-right: 1px solid #e2e8f0; }
        .grid-table .value-col-last { width: 28%; color: #1e293b; font-weight: bold; }
        .grid-table tr:last-child td { border-bottom: none; }

        /* TABEL PAJANAN */
        .pajanan-container { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .pajanan-container td { vertical-align: top; width: 50%; }
        .pajanan-table { width: 100%; border-collapse: collapse; font-size: 8pt; }
        .pajanan-table td { padding: 3px 5px; border-bottom: 1px dotted #cbd5e1; }
        .pajanan-table .item-label { width: 75%; color: #475569; }
        .pajanan-table .item-value { width: 25%; font-weight: bold; color: #0f4a7b; text-align: right; }

        .result-box { background-color: #f0fdf4; border-left: 4px solid #16a34a; padding: 8px 12px; margin-bottom: 15px; font-size: 9pt; color: #14532d; }
        
        .signature-section { width: 100%; margin-top: 20px; border-collapse: collapse; page-break-inside: avoid; }
        .signature-section td { width: 50%; text-align: center; vertical-align: bottom; }
        .sign-area { height: 60px; }
        
        .page-break-avoid { page-break-inside: avoid; }
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

        <div class="document-title">Laporan Hasil Pemeriksaan Fisik Lengkap</div>

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
        <div class="page-break-avoid">
            <div class="section-title">1. TANDA VITAL & ANTROPOMETRI</div>
            @php $tv = $fisikResult->data_fisik['tanda_vital'] ?? []; @endphp
            <table class="grid-table" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                <tr>
                    <td class="label-col">Tinggi Badan</td><td class="value-col">: {{ $tv['tinggi_badan'] ?? '-' }} cm</td>
                    <td class="label-col" style="padding-left:10px;">Tekanan Sistol</td><td class="value-col-last">: {{ $tv['tekanan_darah_sistol'] ?? '-' }} mmHg</td>
                </tr>
                <tr>
                    <td class="label-col">Berat Badan</td><td class="value-col">: {{ $tv['berat_badan'] ?? '-' }} kg</td>
                    <td class="label-col" style="padding-left:10px;">Tekanan Diastol</td><td class="value-col-last">: {{ $tv['tekanan_darah_diastol'] ?? '-' }} mmHg</td>
                </tr>
                <tr>
                    <td class="label-col">BMI (Kategori)</td><td class="value-col">: {{ $tv['bmi'] ?? '-' }} ({{ $tv['kategori_bmi'] ?? '-' }})</td>
                    <td class="label-col" style="padding-left:10px;">Nadi / Nafas</td><td class="value-col-last">: {{ $tv['nadi'] ?? '-' }} x/mnt / {{ $tv['pernafasan'] ?? '-' }} x/mnt</td>
                </tr>
                <tr>
                    <td class="label-col">Suhu Tubuh</td><td class="value-col">: {{ $tv['suhu'] ?? '-' }} °C</td>
                    <td class="label-col" style="padding-left:10px;">SpO2 (Oksigen)</td><td class="value-col-last">: {{ $tv['spo2'] ?? '-' }} %</td>
                </tr>
            </table>
        </div>

        <!-- 2. PEMERIKSAAN FISIK -->
        <div class="section-title">2. PEMERIKSAAN FISIK PER SISTEM</div>
        
        @php 
            $kepala = $fisikResult->data_fisik['kepala'] ?? []; 
            $leher = $fisikResult->data_fisik['leher'] ?? []; 
            $mata = $fisikResult->data_fisik['mata'] ?? []; 
            $dada = $fisikResult->data_fisik['dada'] ?? []; 
            $paru = $fisikResult->data_fisik['paru'] ?? []; 
            $abdomen = $fisikResult->data_fisik['abdomen'] ?? []; 
            $eks = $fisikResult->data_fisik['ekstremitas'] ?? []; 
        @endphp

        <div class="page-break-avoid">
            <div class="sub-title">A. Area Kepala, Mata & Leher</div>
            <table class="grid-table">
                <tr>
                    <td class="label-col">Anemi</td><td class="value-col">: {{ $kepala['anemi'] ?? '-' }}</td>
                    <td class="label-col" style="padding-left:10px;">Ikterus</td><td class="value-col-last">: {{ $kepala['ikterus'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Dyspnoe</td><td class="value-col">: {{ $kepala['dyspnoe'] ?? '-' }}</td>
                    <td class="label-col" style="padding-left:10px;">Cyanosis</td><td class="value-col-last">: {{ $kepala['cyanosis'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Refleks Pupil</td><td class="value-col">: {{ $kepala['refleks_pupil'] ?? '-' }}</td>
                    <td class="label-col" style="padding-left:10px;">Membran Timpani</td><td class="value-col-last">: {{ $kepala['membran_timpani'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Tonsil Kanan / Kiri</td><td class="value-col">: {{ $kepala['tonsil_kanan'] ?? '-' }} / {{ $kepala['tonsil_kiri'] ?? '-' }}</td>
                    <td class="label-col" style="padding-left:10px;">Serumen</td><td class="value-col-last">: {{ $kepala['serumen'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">JVP (Leher)</td><td class="value-col">: {{ $leher['jvp'] ?? '-' }}</td>
                    <td class="label-col" style="padding-left:10px;">Kelenjar Tiroid</td><td class="value-col-last">: {{ $leher['tiroid'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Kelenjar Getah Bening</td><td class="value-col" colspan="3">: {{ $leher['kelenjar_getah_bening'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Visus Kanan / Kiri</td><td class="value-col">: {{ $mata['visus_kanan'] ?? '-' }} / {{ $mata['visus_kiri'] ?? '-' }}</td>
                    <td class="label-col" style="padding-left:10px;">Konjungtiva</td><td class="value-col-last">: {{ $mata['konjungtiva'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Kesimpulan Mata</td><td class="value-col">: {{ $mata['kesimpulan_mata'] ?? '-' }}</td>
                    <td class="label-col" style="padding-left:10px;">Sklera</td><td class="value-col-last">: {{ $mata['sklera'] ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <div class="page-break-avoid">
            <div class="sub-title">B. Area Dada, Paru & Abdomen</div>
            <table class="grid-table">
                <tr>
                    <td class="label-col">Bunyi Jantung I</td><td class="value-col">: {{ $dada['bunyi_jantung_1'] ?? '-' }}</td>
                    <td class="label-col" style="padding-left:10px;">Bunyi Jantung II</td><td class="value-col-last">: {{ $dada['bunyi_jantung_2'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Bunyi Nafas Dasar</td><td class="value-col">: {{ $paru['bunyi_nafas'] ?? '-' }}</td>
                    <td class="label-col" style="padding-left:10px;">Bunyi Tambahan</td><td class="value-col-last">: {{ $paru['bunyi_nafas_tambahan'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Peristaltik</td><td class="value-col">: {{ $abdomen['peristaltik'] ?? '-' }}</td>
                    <td class="label-col" style="padding-left:10px;">Nyeri Tekan</td><td class="value-col-last">: {{ $abdomen['nyeri_tekan'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Hati</td><td class="value-col">: {{ $abdomen['hati'] ?? '-' }}</td>
                    <td class="label-col" style="padding-left:10px;">Limpa / Massa</td><td class="value-col-last">: {{ $abdomen['limpa'] ?? '-' }} / {{ $abdomen['massa'] ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <div class="page-break-avoid">
            <div class="sub-title">C. Ekstremitas & Refleks</div>
            <table class="grid-table">
                <tr>
                    <td class="label-col">Kondisi Ekstremitas</td><td class="value-col" colspan="3">: {{ $eks['ekstremitas'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Refleks Fisiologis (Ka/Ki)</td><td class="value-col">: {{ $eks['refleks_fisiologis_kanan'] ?? '-' }} / {{ $eks['refleks_fisiologis_kiri'] ?? '-' }}</td>
                    <td class="label-col" style="padding-left:10px;">Refleks Patologis (Ka/Ki)</td><td class="value-col-last">: {{ $eks['refleks_patologis_kanan'] ?? '-' }} / {{ $eks['refleks_patologis_kiri'] ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <!-- 3. PAJANAN -->
        <div class="page-break-avoid">
            <div class="section-title">3. RIWAYAT PAJANAN PEKERJAAN (HAZARD)</div>
            <table class="pajanan-container">
                <tr>
                    <td style="padding-right: 10px;">
                        <div class="sub-title" style="margin-top: 0;">Bahaya Fisik & Kimia</div>
                        <table class="pajanan-table">
                            @foreach ($pajanan['fisik'] ?? [] as $key => $val)
                                <tr><td class="item-label">{{ ucwords(str_replace('_', ' ', $key)) }}</td><td class="item-value">{{ $val }}</td></tr>
                            @endforeach
                            @foreach ($pajanan['kimia'] ?? [] as $key => $val)
                                <tr><td class="item-label">{{ ucwords(str_replace('_', ' ', $key)) }} (Kimia)</td><td class="item-value">{{ $val }}</td></tr>
                            @endforeach
                        </table>
                    </td>
                    <td style="padding-left: 10px;">
                        <div class="sub-title" style="margin-top: 0;">Biologi, Psikologi & Ergonomis</div>
                        <table class="pajanan-table">
                            @foreach ($pajanan['biologi'] ?? [] as $key => $val)
                                <tr><td class="item-label">{{ ucwords(str_replace('_', ' ', $key)) }}</td><td class="item-value">{{ $val }}</td></tr>
                            @endforeach
                            @foreach ($pajanan['psikologi'] ?? [] as $key => $val)
                                <tr><td class="item-label">{{ ucwords(str_replace('_', ' ', $key)) }}</td><td class="item-value">{{ $val }}</td></tr>
                            @endforeach
                            @foreach ($pajanan['ergonomis'] ?? [] as $key => $val)
                                <tr><td class="item-label">{{ ucwords(str_replace('_', ' ', $key)) }}</td><td class="item-value">{{ $val }}</td></tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <!-- 4. KESIMPULAN -->
        <div class="page-break-avoid">
            <div class="section-title">4. KESIMPULAN & SARAN</div>
            <div class="result-box">
                <strong>Kesimpulan Diagnosa:</strong><br>
                {{ $fisikResult->kesimpulan ?? 'Belum ada kesimpulan medis yang dicatat.' }}
                <br><br>
                <strong>Tindak Lanjut / Keterangan Tambahan:</strong><br>
                {{ $fisikResult->keterangan ?? 'Tidak ada keterangan tambahan.' }}
            </div>
            
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
    </div>
</body>
</html>