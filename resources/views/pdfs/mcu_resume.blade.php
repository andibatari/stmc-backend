<!DOCTYPE html>
<html>
<head>
    <title>Resume MCU</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: sans-serif; font-size: 10pt; margin: 0.5in; }
        
        /* CONTAINER UTAMA HEADER */
        .header-container {
            width: 100%;
            display: table; 
            border-bottom: 2px solid #000; 
        }
        .header-cell {
            display: table-cell;
            vertical-align: middle;
            padding-bottom: 10px;
        }
        
        /* SEL KIRI: LOGO */
        .header-left {
            width: 30%; 
            text-align: left;
        }
        /* SEL TENGAH: TEKS */
        .header-center {
            width: 80%;
            text-align: left;
            padding-left: 0;
            line-height: 1.5;
        }
        
        .header-logo {
            height: 70px; 
            width: auto;
            margin-right: 5px; 
        }
        
        /* TEKS */
        .text-title { font-size: 14pt; margin: 0; }
        .text-subtitle { font-size: 14pt; margin: 0; text-decoration: underline; }
        .text-priority { font-size: 11pt; margin: 0; color: #cc3333; font-weight: bold; }
        
        /* Data Pasien & Content */
        .data-pasien { margin-bottom: 5px; }
        .data-pasien table { width: 100%; border-collapse: collapse; }
        .data-pasien td { padding: 2px 0; vertical-align: top; font-size: 10pt; }

        .data-pasien .label-col { width: 15%; padding-right: 5px; }
        .data-pasien .colon-col { width: 1%; }
        .data-pasien .value-col { width: 37%; }

        .content-section { margin-top: 2px; }
        .pre-wrap { white-space: pre-wrap; }
        .saran-list { 
            margin: 5px 0 10px 0; 
            padding-left: 15px; 
            list-style-type: disc;
        }
        .saran-list li { 
            margin-bottom: 5px; 
            font-size: 11pt;
        }
        
        /* STYLE UNTUK PARAGRAF PEMBUKA */
        .greeting {
            margin-top: 15px;
            margin-bottom: 20px;
            line-height: 1.6;
            font-size: 10pt;
            text-align: justify;
            color: #334155;
        }
        .greeting strong {
            font-size: 11pt;
            color: #0f172a;
            display: inline-block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

    {{-- HEADER BARU DENGAN 2 LOGO --}}
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
    <hr style="border: none; border-bottom: 0.5px solid #000; margin-top: 0; margin-bottom: 15px;">    

    <div class="data-pasien">
        <table>
            <tr>
                <td colspan="6">Kepada Yth.</td>
            </tr>
            <tr>
                <td class="label-col">Nama</td>
                <td class="colon-col">:</td>
                <td class="value-col">{{ $patient_data['nama'] }}</td>
                <td class="label-col">Tgl. Lahir</td>
                <td class="colon-col">:</td>
                <td class="value-col">{{ $patient_data['tgl_lahir'] }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $patient_data['alamat'] }}</td>
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td>{{ $patient_data['jenis_kelamin'] }}</td>
            </tr>
            <tr>
                <td>NIK / SAP</td>
                <td>:</td>
                <td>{{ $patient_data['nik_sap'] }}</td>
                <td>Paket MCU</td>
                <td>:</td>
                <td>{{ $patient_data['paket_mcu'] }}</td>
            </tr>
            <tr>
                <td>Unit Kerja</td>
                <td>:</td>
                <td>{{ $patient_data['unit_kerja'] }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table> {{-- PENUTUP TABEL YANG SEBELUMNYA HILANG --}}
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
            $resumeText .= "<p style='margin:0; padding-left: 15px; text-indent: -15px;'>{$i}. {$label} : <b>{$value}</b></p>";
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

    {{-- HASIL PEMERIKSAAN (RESUME BODY) --}}
    <div class="content-section">
        @if ($resumeText)
            <div style="font-size: 11pt; line-height: 1.5;">{!! $resumeText !!}</div> 
        @else
            <pre class="pre-wrap" style="font-size: 11pt; line-height: 1.5;">Data hasil pemeriksaan (Butir 1-9) belum diisi.</pre>
        @endif
    </div>
    
    {{-- SARAN --}}
    <div class="content-section">
        <h4 style="border-bottom: 1px solid #000; padding-bottom: 5px;">Saran:</h4>
        @if (!empty($saranArray))
            <ul class="saran-list">
                @foreach ($saranArray as $saran)
                    <li>{{ $saran }}</li>
                @endforeach
            </ul>
        @else
            <p style="font-size: 11pt; margin-left: 15px;">Saran dokter belum diisi.</p>
        @endif
    </div>
    
    {{-- KATEGORI AKHIR --}}
    <div class="content-section">
        <h4 style="margin-bottom: 5px;">Kategori Akhir</h4>
        <p style="font-weight: bold; font-size: 12pt;">{{ $resume_kategori ?? 'N/A' }}</p>
    </div>
    
    {{-- TANDA TANGAN GANDA --}}
    <table style="width: 100%; margin-top: 40px; text-align: center; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <p style="margin: 0; color: white;">.</p>
                <p style="margin-bottom: 70px;">Mengetahui,<br>Kepala Klinik STMC,</p>
                <p style="font-weight: bold; text-decoration: underline; margin: 0;">{{ $setting_kepala_klinik }}</p>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <p style="margin: 0;">Pangkep, {{ $tanggal_cetak }}</p>
                <p style="margin-bottom: 70px;">Dokter Pemeriksa,</p>
                <p style="font-weight: bold; text-decoration: underline; margin: 0;">{{ $doctor_data['nama'] ?? '(Nama Dokter)' }}</p>
                <p style="margin: 0; font-size: 9pt;">{{ $doctor_data['nip'] ?? 'NIP. -' }}</p>
            </td>
        </tr>
    </table>

</body>
</html>