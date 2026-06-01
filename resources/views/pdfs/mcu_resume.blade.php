<!DOCTYPE html>
<html>
<head>
    <title>Resume MCU</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        /* 1. KEMBALIKAN FONT KE 10PT (STANDAR BACA) TAPI MARGIN KERTAS TETAP KECIL */
        body { font-family: sans-serif; font-size: 10pt; margin: 0.3in 0.4in; color: #1e293b; }
        
        .header-container { width: 100%; display: table; border-bottom: 2px solid #000; }
        .header-cell { display: table-cell; vertical-align: middle; padding-bottom: 5px; }
        .header-left { width: 30%; text-align: left; }
        .header-center { width: 80%; text-align: left; padding-left: 0; line-height: 1.2; }
        
        /* LOGO & JUDUL DIPADATKAN */
        .header-logo { height: 50px; width: auto; margin-right: 5px; }
        .text-title { font-size: 18pt; margin: 0; font-weight: 900;}
        .text-subtitle { font-size: 16pt; margin: 0; text-decoration: underline; font-weight: bold;}
        .text-priority { font-size: 14pt; margin: 0; color: #cc3333; font-weight: bold; }
        
        /* DATA PASIEN */
        .data-pasien { margin-top: 7px; margin-bottom: 7px; }
        .data-pasien table { width: 100%; border-collapse: collapse; }
        .data-pasien td { padding: 1px 0; vertical-align: top; font-size: 10pt; }
        .data-pasien .label-col { width: 18%; padding-right: 5px; font-weight: bold;}
        .data-pasien .colon-col { width: 1%; }
        .data-pasien .value-col { width: 37%; }

        /* KATA PENGANTAR (DISCLAIMER) */
        .greeting {
            margin-top: 8px;
            margin-bottom: 8px;
            line-height: 1.3;
            text-align: justify;
        }
        .greeting strong { font-size: 10.5pt; color: #000; display: inline-block; margin-bottom: 2px; }

        /* KONTEN HASIL & SARAN */
        .content-section { margin-top: 10px; }
        .content-section h4 { margin: 0 0 2px 0; font-size: 11pt; border-bottom: 1px solid #000; padding-bottom: 2px; }
        
        .saran-list { margin: 2px 0 2px 0; padding-left: 15px; list-style-type: disc; }
        .saran-list li { margin-bottom: 2px; line-height: 1.3; font-size: 10pt;}
        
        .pre-wrap { white-space: pre-wrap; margin: 0; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header-container">
        <div class="header-cell header-left">
            <img class="header-logo" src="{{ public_path($setting_logo_stmc) }}" alt="Logo SIG">
            <img class="header-logo" src="{{ public_path($setting_logo_tonasa) }}" alt="Logo Tonasa">
        </div>
        <div class="header-cell header-center">
            <p class="text-title">MEDICAL CHECK UP</p>
            <p class="text-subtitle">KLINIK SEMEN TONASA MEDICAL CENTRE</p>
            <p class="text-priority">YOUR HEALTH, OUR PRIORITY</p>
        </div>
    </div>
    <hr style="border: none; border-bottom: 0.5px solid #000; margin-top: 0; margin-bottom: 6px;">    

    {{-- DATA PASIEN --}}
    <div class="data-pasien">
        <table>
            <tr><td colspan="6" style="padding-bottom: 3px;">Kepada Yth.</td></tr>
            <tr>
                <td class="label-col">Nama</td><td class="colon-col">:</td><td class="value-col">{{ $patient_data['nama'] }}</td>
                <td class="label-col">Tgl. Lahir</td><td class="colon-col">:</td><td class="value-col">{{ $patient_data['tgl_lahir'] }}</td>
            </tr>
            <tr>
                <td class="label-col">Alamat</td><td class="colon-col">:</td><td class="value-col">{{ $patient_data['alamat'] }}</td>
                <td class="label-col">Jenis Kelamin</td><td class="colon-col">:</td><td class="value-col">{{ $patient_data['jenis_kelamin'] }}</td>
            </tr>
            <tr>
                <td class="label-col">NIK / SAP</td><td class="colon-col">:</td><td class="value-col">{{ $patient_data['nik_sap'] }}</td>
                <td class="label-col">Paket MCU</td><td class="colon-col">:</td><td class="value-col">{{ $patient_data['paket_mcu'] }}</td>
            </tr>
            <tr>
                <td class="label-col">Unit Kerja</td><td class="colon-col">:</td><td class="value-col">{{ $patient_data['unit_kerja'] }}</td>
                <td></td><td></td><td></td>
            </tr>
        </table>
    </div>

    {{-- KATA PENGANTAR --}}
    <div class="greeting">
        <strong>Dengan Hormat,</strong><br>
        {!! $setting_disclaimer !!}
    </div>

    {{-- BLOK PHP PARSING DATA RESUME --}}
    @php
    $resumeData = ($resume_body_raw && is_string($resume_body_raw)) ? json_decode($resume_body_raw, true) : null; 
    $resumeText = '';
    
    if ($resumeData) {
        $resumeMap = [
            'bmi' => 'BMI', 'laboratorium' => 'Hasil Laboratorium', 'ecg' => 'Hasil Pemeriksaan EKG / Rekam Jantung',
            'gigi' => 'Hasil Pemeriksaan Gigi', 'mata' => 'Hasil Pemeriksaan Mata', 'spirometri' => 'Hasil Pemeriksaan Spirometri',
            'audiometri' => 'Hasil Pemeriksaan Audiometri', 'kesegaran' => 'Hasil Pemeriksaan Kesegaran Jasmani', 'thorax_photo' => 'Hasil Pemeriksaan Thorax Photo',
            'treadmill' => 'Hasil Treadmill', 'usg' => 'Hasil USG','temuan_lain' => 'Temuan lain',
        ];

        $i = 1;
        foreach ($resumeMap as $key => $label) {
            $value = $resumeData[$key] ?? '—'; 
            // Margin diset ke 0 agar rapat, line-height 1.3 agar teks tidak bertumpuk
            $resumeText .= "<p style='margin:0; padding-left: 15px; text-indent: -15px; line-height: 1.3;'>{$i}. {$label} : <b>{$value}</b></p>";
            $i++;
        }
    }
    
    $saranArray = [];
    if ($resume_saran) {
        $saranTemp = preg_split("/[\r\n,]+/", $resume_saran, -1, PREG_SPLIT_NO_EMPTY);
        $saranArray = array_map('trim', $saranTemp);
        $saranArray = array_filter($saranArray); 
    }
    @endphp

    {{-- HASIL PEMERIKSAAN --}}
    <div class="content-section">
        @if ($resumeText)
            <div style="font-size: 10pt;">{!! $resumeText !!}</div> 
        @else
            <pre class="pre-wrap" style="font-size: 10pt;">Data hasil pemeriksaan (Butir 1-9) belum diisi.</pre>
        @endif
    </div>
    
    {{-- SARAN --}}
    <div class="content-section">
        <h4>Saran:</h4>
        @if (!empty($saranArray))
            <ul class="saran-list">
                @foreach ($saranArray as $saran)
                    <li>{{ $saran }}</li>
                @endforeach
            </ul>
        @else
            <p style="margin: 2px 0 0 15px; font-size: 10pt;">Saran dokter belum diisi.</p>
        @endif
    </div>
    
    {{-- KATEGORI AKHIR --}}
    <div class="content-section" style="margin-top: 10px;">
        <h4 style="border: none;">Kategori Akhir:</h4>
        <p style="font-weight: 900; font-size: 12pt; margin: 0;">{{ $resume_kategori ?? 'N/A' }}</p>
    </div>
    
    {{-- TANDA TANGAN GANDA COMPACT --}}
    <div style="page-break-inside: avoid; margin-top: 15px;">
        <table style="width: 100%; text-align: center; border-collapse: collapse; font-size: 10pt;">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%; padding-bottom: 5px;">Pangkep, {{ $tanggal_cetak }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Mengetahui,<br>Kepala Klinik STMC,</td>
                <td style="vertical-align: top;">Dokter Pemeriksa,</td>
            </tr>
            {{-- Spacer Tanda Tangan dikurangi jadi 45px --}}
            <tr>
                <td style="height: 45px;"></td>
                <td style="height: 45px;"></td>
            </tr>
            <tr>
                <td style="vertical-align: top;">
                    <span style="font-weight: bold; text-decoration: underline;">{{ $setting_kepala_klinik }}</span>
                </td>
                <td style="vertical-align: top;">
                    <span style="font-weight: bold; text-decoration: underline;">{{ $doctor_data['nama'] ?? '(Nama Dokter)' }}</span><br>
                    <span style="font-size: 8.5pt;">{{ $doctor_data['nip'] ?? 'NIP. -' }}</span>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>