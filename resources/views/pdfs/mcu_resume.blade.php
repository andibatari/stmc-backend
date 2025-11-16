<!DOCTYPE html>
<html>
<head>
    <title>Resume MCU</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: sans-serif; font-size: 10pt; margin: 0.5in; }
        
        /* CONTAINER UTAMA HEADER */
        .header-container {
            /* Menggunakan table layout untuk stabilitas di Dompdf */
            width: 100%;
            display: table; 
            border-bottom: 2px solid #000; /* Garis hitam */
        }
        .header-cell {
            display: table-cell;
            vertical-align: middle;
            padding-bottom: 10px;
        }
        
        /* SEL KIRI: LOGO */
        .header-left {
            width: 30%; /* Memberi ruang lebih untuk logo dan teks di tengah */
            text-align: left;
        }
        /* SEL TENGAH: TEKS */
        .header-center {
            width: 80%;
            text-align: left;
            padding-left: 0;
            line-height: 1.5;
        }
        
        /* FUNGSI UNTUK GAMBAR (Memastikan path benar dan ukuran sesuai) */
        .header-logo {
            /* Menaikkan ukuran logo */
            height: 70px; 
            width: auto;
            margin-right: 5px; /* Jarak antar logo */
        }
        
        /* TEKS */
        .text-title { font-size: 14pt; margin: 0; }
        .text-subtitle { font-size: 14pt; margin: 0; text-decoration: underline; }
        .text-priority { font-size: 11pt; margin: 0; color: #cc3333; font-weight: bold; }
        
        /* Data Pasien & Content */
        .data-pasien { margin-bottom: 5px; }
        .data-pasien table { width: 100%; border-collapse: collapse; }
        .data-pasien td { padding: 2px 0; vertical-align: top; }
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
    </style>
</head>
<body>

    {{-- HEADER BARU DENGAN 2 LOGO --}}
    <div class="header-container">
        
        <div class="header-cell header-left">
            {{-- LOGO SIG --}}
            <img class="header-logo" src="{{ public_path('images/logo-stmc.png') }}" alt="Logo SIG">
            {{-- LOGO TONASA --}}
            <img class="header-logo" src="{{ public_path('images/logo-semen-tonasa.png') }}" alt="Logo Tonasa">
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
                <td style="width: 15%;">Kepada Yth.</td>
            </tr>
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td>{{ $patient_data['nama'] }}</td>
            </tr>
            {{-- ... (Data pasien lainnya) ... --}}
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $patient_data['alamat'] }}</td>
            </tr>
            <tr>
                <td>NIK / SAP</td>
                <td>:</td>
                <td>{{ $patient_data['nik_sap'] }}</td>
            </tr>
            <tr>
                <td>Unit Kerja</td>
                <td>:</td>
                <td>{{ $patient_data['unit_kerja'] }}</td>
            </tr>
        </table>
    </div>

    {{-- ... (Konten Hormat dan Hasil Pemeriksaan Tetap Sama) ... --}}
    <div class="content-section">
        Dengan Hormat,
        <p>Pada Pemeriksaan Kesehatan Berkala di Klinik Semen Tonasa Medical Centre yang dilakukan pada tanggal <b>{{ $tanggal_mcu }}</b>, ternyata Bapak/Ibu/Sdr (i) harus memperhatikan hal-hal sebagai berikut:</p>
    </div>

    {{-- ... (Blok PHP Parsing Resume Data) ... --}}
    @php
    // Pastikan $resume_body_raw adalah string dan tidak null/kosong sebelum di-decode
    $resumeData = ($resume_body_raw && is_string($resume_body_raw)) ? json_decode($resume_body_raw, true) : null; 
    $resumeText = '';
    
    // Jika data resume ditemukan (berhasil di-parse)
    if ($resumeData) {
        $resumeMap = [
            'bmi' => 'BMI', 'laboratorium' => 'Hasil Laboratorium', 'ecg' => 'Hasil Pemeriksaan ECG / Rekam Jantung',
            'gigi' => 'Hasil Pemeriksaan Gigi', 'mata' => 'Hasil Pemeriksaan Mata', 'spirometri' => 'Hasil Pemeriksaan Spirometri',
            'audiometri' => 'Hasil Pemeriksaan Audiometri', 'kesegaran' => 'Hasil Pemeriksaan Kesegaran Jasmani',
            'temuan_lain' => 'Temuan lain',
        ];

        $i = 1;
        foreach ($resumeMap as $key => $label) {
            $value = $resumeData[$key] ?? '—'; 
            // Menggunakan tag <p> dan styling number agar rapi di Dompdf
            $resumeText .= "<p style='margin:0; padding-left: 15px; text-indent: -15px;'>{$i}. {$label} : <b>{$value}</b></p>";
            $i++;
        }
    }
    
    // LOGIKA PERBAIKAN SARAN KE POIN-POIN
    $saranArray = [];
    if ($resume_saran) {
        // Coba pisahkan berdasarkan baris baru (\n) atau koma (,)
        // Kemudian trim setiap item
        $saranTemp = preg_split("/[\r\n,]+/", $resume_saran, -1, PREG_SPLIT_NO_EMPTY);
        $saranArray = array_map('trim', $saranTemp);
        $saranArray = array_filter($saranArray); // Hapus elemen kosong
    }
    @endphp

    <div class="content-section">
        {{-- HASIL PEMERIKSAAN (RESUME BODY) --}}
        @if ($resumeText)
            {{-- Menggunakan div agar Dompdf dapat merender list dari p tag yang kita buat --}}
            <div style="font-size: 11pt; line-height: 1.5;">{!! $resumeText !!}</div> 
        @else
            <pre class="pre-wrap" style="font-size: 11pt; line-height: 1.5;">Data hasil pemeriksaan (Butir 1-9) belum diisi.</pre>
        @endif
    </div>
    
    <div class="content-section">
        <h4 style="border-bottom: 1px solid #000; padding-bottom: 5px;">Saran:</h4>
        {{-- SARAN DOKTER (MENJADI LIST BERBUTIR) --}}
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
    
    <div class="content-section">
        <h4 style="margin-bottom: 5px;">Kategori</h4>
        {{-- KATEGORI AKHIR (RESUME KATEGORI) --}}
        <p style="font-weight: bold; font-size: 12pt;">{{ $resume_kategori ?? 'N/A' }}</p>
    </div>
    
    <div style="margin-top: 15px; text-align: right;">
        <p>Gowa, {{ $tanggal_cetak }}</p>
        <p style="margin-bottom: 70px;">Dokter Pemeriksa,</p>
        <p style="font-weight: bold;">(Nama Dokter)</p>
        <p>NIP. XXXXXXXXXXXXX</p>
    </div>

</body>
</html>