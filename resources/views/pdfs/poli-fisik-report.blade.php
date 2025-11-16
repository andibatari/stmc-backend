<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pemeriksaan Fisik - {{ $patient->nama_lengkap ?? $patient->nama_karyawan ?? 'Pasien' }}</title>
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
            font-size: 11px;
        }

        /* --- UTILITY CSS --- */
        .page-strip { position: absolute; top: 0; height: 100%; width: 5px; background-color: var(--color-light); }
        .page-strip.left { left: 0; border-right: 1px solid var(--color-primary); }
        .page-strip.right { right: 0; border-left: 1px solid var(--color-primary); }

        /* --- HEADER (TATA LETAK BERBASIS TABLE) --- */
        .header {
            padding: 10px 30px;
            border-bottom: 4px solid var(--color-primary);
            margin-bottom: 20px;
        }

        /* Tabel untuk penyejajaran vertikal logo dan teks */
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { padding: 0; vertical-align: middle; line-height: 1.3; }

        .logo-cell-left { width: 10%; text-align: left; }
        .text-cell { width: 80%; text-align: center; }
        .logo-cell-right { width: 10%; text-align: right; }

        .logo-box { width: 55px; height: 55px; display: inline-block; }
        .logo-box img { width: 100%; height: 100%; object-fit: contain; }

        .header-text { text-align: center; padding: 0 15px; }
        .header-text h1 { color: var(--color-primary); margin: 0; font-size: 20px; font-weight: 700; line-height: 1.3; }
        .header-text .subtitle-primary { font-size: 14px; font-weight: bold; color: var(--color-primary); margin-top: 2px; display: block; }
        .header-text .subtitle-secondary { font-size: 11px; color: var(--color-text-light); margin-top: 1px; display: block; }

        /* --- INFORMASI PASIEN --- */
        .patient-info {
            padding: 0 30px 20px 30px;
            font-size: 13px;
            border-bottom: 1px solid var(--color-light);
            margin-bottom: 10px;
        }
        .patient-table { width: 100%; border-collapse: collapse; }
        .patient-table td { padding: 0; vertical-align: top; width: 50%; }
        .patient-row { display: block; margin-bottom: 5px; }
        .patient-label { width: 120px; font-weight: bold; color: var(--color-text); display: inline-block; }

        /* --- ISI LAPORAN --- */
        .content { padding: 0 30px; }
        .section-title { text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 10px; color: var(--color-primary); text-transform: uppercase; }

        /* Gaya untuk Detail Pemeriksaan */
        .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .detail-table td { padding: 2px 0; vertical-align: top; font-size: 11px;}
        .detail-table .label-col { width: 30%; font-weight: bold; color: var(--color-text);}
        .detail-table .value-col { width: 20%; color: var(--color-text); }
        .detail-table .value-col-long { width: 50%; color: var(--color-text); }

        /* Judul Bagian Bernomor (1, 2, 3, 4) */
        .detail-table .subtitle {
            font-weight: 700;
            color: var(--color-primary);
            padding-top: 5px;
            padding-bottom: 5px;
            font-size: 13px;
            border-bottom: 1px solid var(--color-primary);
            text-transform: uppercase;
        }

        /* Gaya untuk Pemeriksaan Sistem */
        .system-check-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .system-check-table .sys-row td {
            padding: 2px 0;
            vertical-align: top;
        }
        .sys-label {
            width: 25%;
            font-weight: bold;
            padding-right: 5px;
        }
        .sys-value {
            width: 25%;
            font-weight: normal;
        }
        .sys-sublabel {
            width: 25%;
            font-weight: bold;
            padding-right: 5px;
        }
        .sys-subvalue {
            width: 25%;
            font-weight: normal;
        }

        /* Sub-Judul Sistem (Kepala & Leher, Dada & Paru) */
        .sys-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--color-accent);
            padding-top: 10px;
            padding-bottom: 5px;
            border-bottom: 1px dashed var(--color-light);
            margin-bottom: 5px;
        }
        .sys-value-container {
            line-height: 1.2;
            padding: 2px 0;
        }

        /* Gaya untuk Tabel Pajanan Pekerjaan */
        .pajanan-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .pajanan-table .item-label { width: 70%; padding-left: 10px; }
        .pajanan-table .item-value { width: 30%; font-weight: normal; }
        .pajanan-table tr {
            border-bottom: 1px dotted var(--color-light);
        }
        .pajanan-table tr:last-child {
            border-bottom: none;
        }
        .pajanan-table td {
            padding: 5px 0;
        }

        /* --- KESIMPULAN --- */
        .category-box { margin-top: 30px; padding: 15px; background-color: var(--color-light); border: 2px solid var(--color-primary); border-radius: 8px; }
        .category-box h3 { margin: 0 0 5px 0; color: var(--color-primary); font-size: 14px; font-weight: bold; }
        .category-box p { margin: 0; font-size: 11px; }

        /* --- PENANDATANGAN --- */
        .signer { margin-top: 60px; padding-right: 30px; text-align: right; font-size: 12px; }
        .signer p { margin: 0; }

        /* --- FOOTER UNIK --- */
        .footer-left-strip { position: fixed; bottom: 0; left: 0; width: 100%; height: 15px; }
        .footer-shape-main { position: absolute; bottom: 0; left: 0; width: 40%; height: 15px; background-color: var(--color-primary); clip-path: polygon(0 0, 100% 100%, 0 100%); }
        .footer-shape-accent { position: absolute; bottom: 0; left: 0; width: 30%; height: 10px; background-color: var(--color-accent); clip-path: polygon(0 0, 100% 100%, 0 100%); }
        .footer-info { position: absolute; bottom: 5px; right: 30px; font-size: 9px; color: var(--color-text-light); }
    </style>
</head>
<body>

    <div class="header">
        <table class="header-table">
            <tr>
                {{-- LOGO KIRI --}}
                <td class="logo-cell-left">
                    <div class="logo-box">
                        <img src="{{ public_path('images/logo-semen-tonasa.png') }}" alt="Logo Kiri" style="width: 100%; height: 100%;">
                    </div>
                </td>

                {{-- TEKS TENGAH --}}
                <td class="text-cell">
                    <div class="header-text">
                        <h1>SEMEN TONASA MEDICAL CENTRE (STMC)</h1>
                        <p class="subtitle-primary">MEDICAL CHECK UP </p>
                        <p class="subtitle-secondary">PT SEMEN TONASA - HASIL PEMERIKSAAN FISIK</p>
                    </div>
                </td>

                {{-- LOGO KANAN --}}
                <td class="logo-cell-right">
                    <div class="logo-box">
                        <img src="{{ public_path('images/logo-stmc.png') }}" alt="Logo Kanan" style="width: 100%; height: 100%;">
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
                        <span class="patient-label">Perusahaan</span>
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
            HASIL PEMERIKSAAN FISIK
        </div>

        {{-- TABEL TANDA VITAL --}}
        <table class="detail-table">
            <tr>
                <td colspan="4" class="subtitle">1. TANDA VITAL & ANTROPOMETRI</td>
            </tr>
            @php $tv = $fisikResult->data_fisik['tanda_vital']; @endphp
            <tr>
                <td class="label-col">Tinggi Badan / Berat Badan</td>
                <td class="value-col">: {{ $tv['tinggi_badan'] ?? 'N/A' }} cm / {{ $tv['berat_badan'] ?? 'N/A' }} kg</td>
                <td class="label-col">Tekanan Darah</td>
                <td class="value-col">: {{ $tv['tekanan_darah_sistol'] ?? 'N/A' }}/{{ $tv['tekanan_darah_diastol'] ?? 'N/A' }} mmHg</td>
            </tr>
            <tr>
                <td class="label-col">BMI / Kategori</td>
                <td class="value-col">: {{ $tv['bmi'] ?? 'N/A' }} / {{ $tv['kategori_bmi'] ?? 'N/A' }}</td>
                <td class="label-col">Nadi / Pernafasan</td>
                <td class="value-col">: {{ $tv['nadi'] ?? 'N/A' }} x/mnt / {{ $tv['pernafasan'] ?? 'N/A' }} x/mnt</td>
            </tr>
            <tr>
                <td class="label-col">Suhu / SpO2</td>
                <td class="value-col">: {{ $tv['suhu'] ?? 'N/A' }} °C / {{ $tv['spo2'] ?? 'N/A' }} %</td>
                <td class="label-col"></td>
                <td class="value-col"></td>
            </tr>
        </table>

        {{-- TABEL PEMERIKSAAN SISTEM (MENGGUNAKAN TATA LETAK 4 KOLOM) --}}
        <table class="detail-table">
            {{-- Judul Bagian --}}
            <tr>
                <td colspan="4" class="subtitle">2. PEMERIKSAAN FISIK PER SISTEM</td>
            </tr>

            {{-- KEPALA & LEHER --}}
            @php $kepala = $fisikResult->data_fisik['kepala']; $leher = $fisikResult->data_fisik['leher']; @endphp
            <tr><td colspan="4" class="sys-title">KEPALA & LEHER</td></tr>

            <tr class="sys-row">
                <td class="sys-label">Anemi / Ikterus</td>
                <td class="sys-value">: {{ $kepala['anemi'] ?? 'N/A' }} / {{ $kepala['ikterus'] ?? 'N/A' }}</td>
                <td class="sys-sublabel">Tonsil Kanan / Kiri</td>
                <td class="sys-subvalue">: {{ $kepala['tonsil_kanan'] ?? 'N/A' }} / {{ $kepala['tonsil_kiri'] ?? 'N/A' }}</td>
            </tr>
            <tr class="sys-row">
                <td class="sys-label">Dyspnoe / Cyanosis</td>
                <td class="sys-value">: {{ $kepala['dyspnoe'] ?? 'N/A' }} / {{ $kepala['cyanosis'] ?? 'N/A' }}</td>
                <td class="sys-sublabel">Serumen / Membran Timpani</td>
                <td class="sys-subvalue">: {{ $kepala['serumen'] ?? 'N/A' }} / {{ $kepala['membran_timpani'] ?? 'N/A' }}</td>
            </tr>
            <tr class="sys-row">
                <td class="sys-label">Refleks Pupil</td>
                <td class="sys-value">: {{ $kepala['refleks_pupil'] ?? 'N/A' }}</td>
                <td class="sys-sublabel">JVP / Tiroid</td>
                <td class="sys-subvalue">: {{ $leher['jvp'] ?? 'N/A' }} / {{ $leher['tiroid'] ?? 'N/A' }}</td>
            </tr>
            <tr class="sys-row">
                <td class="sys-label">Kelenjar Getah Bening</td>
                <td class="sys-value" colspan="3">: {{ $leher['kelenjar_getah_bening'] ?? 'N/A' }}</td>
            </tr>

            {{-- DADA & PARU --}}
            @php $dada = $fisikResult->data_fisik['dada']; $paru = $fisikResult->data_fisik['paru']; @endphp
            <tr><td colspan="4" class="sys-title">DADA & PARU</td></tr>
            <tr class="sys-row">
                <td class="sys-label">Bunyi Jantung I / II</td>
                <td class="sys-value">: {{ $dada['bunyi_jantung_1'] ?? 'N/A' }} / {{ $dada['bunyi_jantung_2'] ?? 'N/A' }}</td>
                <td class="sys-sublabel">Bunyi Nafas Dasar</td>
                <td class="sys-subvalue">: {{ $paru['bunyi_nafas'] ?? 'N/A' }}</td>
            </tr>
            <tr class="sys-row">
                <td class="sys-label">Bunyi Nafas Tambahan</td>
                <td class="sys-value" colspan="3">: {{ $paru['bunyi_nafas_tambahan'] ?? 'N/A' }}</td>
            </tr>

            {{-- ABDOMEN --}}
            @php $abdomen = $fisikResult->data_fisik['abdomen']; @endphp
            <tr><td colspan="4" class="sys-title">ABDOMEN</td></tr>
            <tr class="sys-row">
                <td class="sys-label">Peristaltik / Nyeri Tekan</td>
                <td class="sys-value">: {{ $abdomen['peristaltik'] ?? 'N/A' }} / {{ $abdomen['nyeri_tekan'] ?? 'N/A' }}</td>
                <td class="sys-sublabel">Hati / Limpa</td>
                <td class="sys-subvalue">: {{ $abdomen['hati'] ?? 'N/A' }} / {{ $abdomen['limpa'] ?? 'N/A' }}</td>
            </tr>
            <tr class="sys-row">
                <td class="sys-label">Massa</td>
                <td class="sys-value" colspan="3">: {{ $abdomen['massa'] ?? 'N/A' }}</td>
            </tr>

            {{-- EKSTREMITAS --}}
            @php $eks = $fisikResult->data_fisik['ekstremitas']; @endphp
            <tr><td colspan="4" class="sys-title">EKSTREMITAS & REFLEKS</td></tr>
            <tr class="sys-row">
                <td class="sys-label">Ekstremitas</td>
                <td class="sys-value" colspan="3">: {{ $eks['ekstremitas'] ?? 'N/A' }}</td>
            </tr>
            <tr class="sys-row">
                <td class="sys-label">Refleks Fisiologis Kanan / Kiri</td>
                <td class="sys-value">: {{ $eks['refleks_fisiologis_kanan'] ?? 'N/A' }} / {{ $eks['refleks_fisiologis_kiri'] ?? 'N/A' }}</td>
                <td class="sys-sublabel">Refleks Patologis Kanan / Kiri</td>
                <td class="sys-subvalue">: {{ $eks['refleks_patologis_kanan'] ?? 'N/A' }} / {{ $eks['refleks_patologis_kiri'] ?? 'N/A' }}</td>
            </tr>
        </table>

        {{-- KRITIS: TABEL PEMERIKSAAN MATA --}}
        <table class="detail-table">
            <tr>
                <td colspan="4" class="subtitle">3. PEMERIKSAAN MATA</td>
            </tr>
            @php $mata = $fisikResult->data_fisik['mata']; @endphp
            <tr>
                <td class="label-col">Visus Kanan / Kiri</td>
                <td class="value-col">: {{ $mata['visus_kanan'] ?? 'N/A' }} / {{ $mata['visus_kiri'] ?? 'N/A' }}</td>
                <td class="label-col">Konjungtiva / Sklera</td>
                <td class="value-col">: {{ $mata['konjungtiva'] ?? 'N/A' }} / {{ $mata['sklera'] ?? 'N/A' }}</td>
            </tr>
            {{-- KRITIS: Tambahkan baris untuk kesimpulan mata --}}
            <tr>
                <td class="label-col">Kesimpulan</td>
                <td class="value-col-long" colspan="3">: {{ $mata['kesimpulan_mata'] ?? 'N/A' }}</td>
            </tr>
        </table>

        {{-- RIWAYAT PAJANAN PEKERJAAN --}}
        <div style="margin-top: 30px;">
            <table class="detail-table">
                <tr><td colspan="4" class="subtitle">4. RIWAYAT PAJANAN PEKERJAAN</td></tr>
            </table>

            <table class="pajanan-table">
                <tr><td colspan="2" class="sys-title" style="padding-left: 0; border-bottom: none;">A. FISIK</td></tr>
                @foreach ($pajanan['fisik'] as $key => $value)
                @php $label = match ($key) {
                    'radiasi_non_pengion' => 'Radiasi bukan pengion (Gel mikro, infrared, dll)',
                    'lain_fisik' => 'Lain - lain',
                    default => ucfirst(str_replace('_', ' ', $key)),
                }; @endphp
                <tr>
                    <td class="item-label">{{ $label }}</td>
                    <td class="item-value">: {{ $value }}</td>
                </tr>
                @endforeach
            </table>

            <table class="pajanan-table">
                <tr><td colspan="2" class="sys-title" style="padding-left: 0; border-bottom: none;">B. KIMIA</td></tr>
                @foreach ($pajanan['kimia'] as $key => $value)
                @php $label = match ($key) {
                    'debu_anorganik' => 'Debu anorganik (Silika, semen, dll)',
                    'debu_organic' => 'Debu organic (Kapas, tekstil, gandum)',
                    'logam_berat' => 'Logam berat (Timah hitam, Air raksa, dll)',
                    'iritan_asam' => 'Iritan asam (Air keras, Asam sulfat)',
                    'iritan_basa' => 'Iritan basa (Amoniak, Soda api)',
                    'cairan_pembersih' => 'Cairan pembersih (Amonia, Klor, Kporit)',
                    'uap_logam' => 'Uap logam (Mangan, Seng)',
                    'lain_kimia' => 'Lain - lain',
                    default => ucfirst(str_replace('_', ' ', $key)),
                }; @endphp
                <tr>
                    <td class="item-label">{{ $label }}</td>
                    <td class="item-value">: {{ $value }}</td>
                </tr>
                @endforeach
            </table>

            <table class="pajanan-table">
                <tr><td colspan="2" class="sys-title" style="padding-left: 0; border-bottom: none;">C. BIOLOGI</td></tr>
                @foreach ($pajanan['biologi'] as $key => $value)
                @php $label = match ($key) {
                    'bakteri' => 'Bakteri / Virus / Jamur / Parasit',
                    'darah' => 'Darah / Cairan tubuh lain',
                    'nyamuk' => 'Nyamuk / Serangga / Lain - lain',
                    'limbah' => 'Limbah (Kotoran manusia / Hewan)',
                    'lain_biologi' => 'Lain - lain',
                    default => ucfirst(str_replace('_', ' ', $key)),
                }; @endphp
                <tr>
                    <td class="item-label">{{ $label }}</td>
                    <td class="item-value">: {{ $value }}</td>
                </tr>
                @endforeach
            </table>

            <table class="pajanan-table">
                <tr><td colspan="2" class="sys-title" style="padding-left: 0; border-bottom: none;">D. PSIKOLOGI</td></tr>
                @foreach ($pajanan['psikologi'] as $key => $value)
                @php $label = match ($key) {
                    'beban_kerja' => 'Beban kerja tidak sesuai dengan waktu dan jumlah pekerjaan',
                    'pekerjaan_tidak_sesuai' => 'Pekerjaan tidak sesuai dengan pengetahuan dan keterampilan',
                    'ketidakjelasan_tugas' => 'Ketidakjelasan tugas',
                    'hambatan_jenjang_karir' => 'Hambatan jenjang karir',
                    'bekerja_giliran' => 'Bekerja giliran (Shift)',
                    'konflik_teman_sekerja' => 'Konflik dengan teman sekerja',
                    'konflik_keluarga' => 'Konflik dalam keluarga',
                    'lain_psikologi' => 'Lain - lain',
                    default => ucfirst(str_replace('_', ' ', $key)),
                }; @endphp
                <tr>
                    <td class="item-label">{{ $label }}</td>
                    <td class="item-value">: {{ $value }}</td>
                </tr>
                @endforeach
            </table>

            <table class="pajanan-table">
                <tr><td colspan="2" class="sys-title" style="padding-left: 0; border-bottom: none;">E. ERGONOMIS</td></tr>
                @foreach ($pajanan['ergonomis'] as $key => $value)
                @php $label = match ($key) {
                    'gerakan_berulang' => 'Gerakan berulang dengan tangan',
                    'angkat_berat' => 'Angkat / Angkut berat',
                    'duduk_lama' => 'Duduk lama > 4 jam terus - menerus',
                    'berdiri_lama' => 'Berdiri lama > 4 jam terus - menerus',
                    'posisi_tidak_ergonomis' => 'Posisi tubuh tidak ergonomis',
                    'pencahayaan_tidak_sesuai' => 'Pencahayaan tidak sesuai',
                    'bekerja_layar_monitor' => 'Bekerja dengan layar / monitor > 4 jam dalam sehari',
                    'lain_ergonomis' => 'Lain - lain',
                    default => ucfirst(str_replace('_', ' ', $key)),
                }; @endphp
                <tr>
                    <td class="item-label">{{ $label }}</td>
                    <td class="item-value">: {{ $value }}</td>
                </tr>
                @endforeach
            </table>
        </div>

        {{-- KESIMPULAN --}}
        <div class="category-box">
            <h3>KESIMPULAN DIAGNOSA:</h3>
            <p>{{ $fisikResult->kesimpulan ?? 'Belum ada kesimpulan medis yang dicatat.' }}</p>
        </div>
        <div class="category-box" style="margin-top: 15px;">
            <h3>KETERANGAN / SARAN TINDAK LANJUT:</h3>
            <p>{{ $fisikResult->keterangan ?? 'Tidak ada saran atau keterangan tambahan.' }}</p>
        </div>


        <div class="signer">
            <p>Pangkep, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
            <p>Hormat kami,</p>
            <br><br><br><br>
            <p style="border-bottom: 1px solid var(--color-text); display: inline-block; padding-bottom: 2px;">{{ $dokter->nama_lengkap ?? 'Nama Petugas / Dokter' }}</p>
        </div>
    </div>

    {{-- FOOTER UNIK STMC (Elemen Diagonal/Strip Kiri) --}}
    <div class="footer-left-strip">
        <div class="footer-shape-main"></div>
        <div class="footer-shape-accent"></div>
        <div class="footer-info">STMC - HASIL PEMERIKSAAN FISIK</div>
    </div>
</body>
</html>