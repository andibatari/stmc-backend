<?php

namespace App\Http\Controllers;

// Mengimpor model-model Eloquent ORM yang mengelola data tabel terkait di database
use App\Models\JadwalMcu;
use App\Models\JadwalPoli; 
use App\Models\PoliGigiResult;
use App\Models\KebugaranResult;
use App\Models\FisikResult;
use Illuminate\Http\Request;

// Mengimpor Facade DomPDF untuk mengonversi dokumen HTML/Blade menjadi berkas PDF
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

// Mengimpor library FPDI dari Setasign untuk membaca dan menggabungkan beberapa file PDF menjadi satu file utuh
use setasign\Fpdi\Tcpdf\Fpdi; 

// Mengimpor kelas inti dari package Endroid QR Code v5 untuk membangkitkan matriks barcode dua dimensi
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;

class McuPdfController extends Controller
{
    // Mengambil data path berkas poli dari penyimpanan publik lokal lalu melakukan pengalihan unduhan (redirect)
    public function viewPdf($id) {
        $poliData = JadwalPoli::findOrFail($id);
        
        if ($poliData->file_path && Storage::disk('public')->exists($poliData->file_path)) {
            return redirect(Storage::disk('public')->url($poliData->file_path));
        }
        
        abort(404, "File tidak ditemukan di Local Storage.");
    }

    // Mengambil file rekam medis poli gigi spesifik berdasarkan ID jadwal poli
    public function viewPdfGigi($id) {
        $result = PoliGigiResult::where('jadwal_poli_id', $id)->firstOrFail();
        return $this->redirectLocal($result->file_path);
    }

    // Mengambil file rekam medis uji kebugaran jasmani spesifik berdasarkan ID jadwal poli
    public function viewPdfKebugaran($id) {
        $result = KebugaranResult::where('jadwal_poli_id', $id)->firstOrFail();
        return $this->redirectLocal($result->file_path);
    }

    // Mengambil file rekam medis pemeriksaan fisik umum spesifik berdasarkan ID jadwal poli
    public function viewPdfFisik($id) {
        $result = FisikResult::where('jadwal_poli_id', $id)->firstOrFail();
        return $this->redirectLocal($result->file_path);
    }

    // Fungsi pembantu (helper) internal untuk memvalidasi keberadaan file di local storage sebelum dialihkan
    private function redirectLocal($filePath) {
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            return redirect(Storage::disk('public')->url($filePath));
        }
        abort(404, "File tidak ditemukan di Local Storage.");
    }
    
    /**
     * FUNGSI KRITIS: Menghasilkan objek PDF Resume yang telah disisipkan QR Code Tanda Tangan Digital.
     */
    protected function generateResumePdfObject(JadwalMcu $jadwal)
    {
        // Memuat relasi tabel dokter dan paket mcu untuk menghindari masalah N+1 query database
        $jadwal->load(['dokter', 'paketMcu']);
        
        // Menentukan entitas pasien terpilih, baik dari tabel karyawan maupun tabel peserta umum/non-karyawan
        $patient = $jadwal->karyawan ?? $jadwal->pesertaMcu;
        if (!$patient) return null;
        
        // Mengambil instans data dokter penanggung jawab pemeriksaan
        $doctor = $jadwal->dokter;
        $doctorName = 'Dokter Tidak Ditunjuk';
        $doctorNip = 'NIP. N/A';

        // Validasi pengamanan data, memastikan objek model dokter tersedia sebelum mengakses kolomnya
        if ($doctor) {
            $doctorName = $doctor->nama_lengkap ?? $doctor->name ?? $doctor->nama ?? 'Dokter Tidak Ditunjuk';
            $doctorNip = $doctor->nip ?? 'NIP. XXXXXXXXXXXXX';
        }

        // Memformat tanggal rekam medis ke standar penulisan Indonesia (Tanggal/Bulan/Tahun)
        $tanggalMcuFormatted = Carbon::parse($jadwal->tanggal_mcu)->format('d/m/Y');

        // Mengambil konfigurasi dinamis teks pengantar dokumen hasil MCU dari tabel settings
        $namaKepalaKlinik = \App\Models\Setting::where('key', 'nama_kepala_klinik')->value('value') ?? 'Dr. Penanggung Jawab';
        $teksDisclaimerRaw = \App\Models\Setting::where('key', 'teks_disclaimer')->value('value') ?? 'Pada Pemeriksaan Kesehatan Berkala di Klinik Semen Tonasa Medical Centre yang dilakukan pada tanggal <b>[TANGGAL]</b>...';
        
        // Melakukan substitusi otomatis string template penanda [TANGGAL] dengan nilai tanggal MCU yang asli
        $teksDisclaimerFinal = str_replace('[TANGGAL]', $tanggalMcuFormatted, $teksDisclaimerRaw);

        // Mengambil path asset logo instansi secara dinamis dari database sistem
        $logoStmc = \App\Models\Setting::where('key', 'logo_stmc')->value('value') ?? 'images/logo-stmc.png';
        $logoTonasa = \App\Models\Setting::where('key', 'logo_tonasa')->value('value') ?? 'images/logo-semen-tonasa.png';
        
        // =========================================================================
        // ⬇️ LOGIKA PENGEMBANGAN BARU: GENERATE QR CODE VALIDASI (ENDROID QR v5) ⬇️
        // =========================================================================
        
        // Menghasilkan tautan/URL verifikasi publik unik menggunakan kolom UUID 'qr_code_id' milik jadwal mcu
        $linkValidasiPublik = route('validasi.pdf', $jadwal->qr_code_id);

        // Inisialisasi pembuatan objek QR Code murni melalui instansiasi kelas Endroid v5
        $qrCode = new QrCode($linkValidasiPublik);
        $qrCode->setSize(150); // Menentukan dimensi ukuran QR Code sebesar 85x85 piksel agar muat di area tanda tangan
        $qrCode->setMargin(0); // Menghilangkan white space margin bawaan di sisi luar barcode agar presisi layouting
        $qrCode->setEncoding(new Encoding('UTF-8')); // Mengunci enkripsi karakter string URL ke standar UTF-8
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::High); // Mengeset level redundansi tinggi agar QR tetap terbaca walau dokumen sedikit terlipat/rusak

        // Menyiapkan kelas penulis (writer) untuk mengekspor matriks kode dua dimensi menjadi format gambar PNG
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        
        // Mengonversi data biner gambar PNG menjadi string teks Base64 agar dapat dilebur langsung ke dokumen HTML DomPDF
        $qrCodeBase64 = base64_encode($result->getString());

        // =========================================================================
        // ⬆️ AKHIR LOGIKA PEMBANGUNAN QR CODE ⬆️
        // =========================================================================

        // Memetakan seluruh kumpulan variabel ke dalam array satu dimensi untuk dikirimkan (parsing) ke file view
        $data = [
            'jadwal' => $jadwal,
            'patient' => $patient,
            'tanggal_mcu' => Carbon::parse($jadwal->tanggal_mcu)->format('d/m/Y'),
            'tanggal_cetak' => Carbon::now()->format('d/m/Y'),
            'resume_body_raw' => $jadwal->resume_body,
            'resume_saran' => $jadwal->resume_saran,
            'resume_kategori' => $jadwal->resume_kategori,
            'qrCodeBase64' => $qrCodeBase64, // Menyisipkan string gambar QR Code ke array data
            'doctor_data' => [
                'nama' => $doctorName,
                'nip' => $doctorNip,
            ],
            'patient_data' => [
                'nama' => $patient->nama_lengkap ?? $patient->nama_karyawan,
                'alamat' => $patient->alamat ?? 'N/A',
                'tgl_lahir' => $patient->tanggal_lahir ?? 'N/A',
                'jenis_kelamin' => $patient->jenis_kelamin ?? 'N/A',
                'paket_mcu' => $jadwal->paketMcu->nama_paket ?? 'N/A',
                'nik_sap' => $patient->no_sap ?? $patient->nik_karyawan ?? 'N/A',
                'unit_kerja' => $patient->unitKerja->nama_unit_kerja ?? 'N/A',
                'nab_suhu_kerja' => 28.0 
            ],
            'setting_kepala_klinik' => $namaKepalaKlinik,
            'setting_disclaimer'  => $teksDisclaimerFinal,
            'setting_logo_stmc'   => $logoStmc,
            'setting_logo_tonasa' => $logoTonasa,
        ];

        // Memuat file cetakan HTML/Blade rekam medis dan menyuntikkan data array ke dalamnya
        return Pdf::loadView('pdfs.mcu_resume', $data);
    }
    
    // Fungsi khusus untuk langsung mengunduh/melihat berkas lembar resume MCU tunggal tanpa berkas poli lampiran
    public function downloadResume($jadwalId)
    {
         $jadwal = JadwalMcu::with(['karyawan', 'pesertaMcu', 'paketMcu','dokter'])->findOrFail($jadwalId);
         $resumePdf = $this->generateResumePdfObject($jadwal);
         
         $patientName = ($jadwal->karyawan ?? $jadwal->pesertaMcu)->nama_lengkap ?? ($jadwal->karyawan ?? $jadwal->pesertaMcu)->nama_karyawan ?? 'Pasien';
         $fileName = 'Resume_MCU_' . str_replace(' ', '_', $patientName) . '_' . $jadwal->tanggal_mcu . '.pdf';
         
         return $resumePdf->stream($fileName);
    }

    // Fungsi penggabungan berkas (merging): Menggabungkan Lembar Utama Resume dengan Berkas PDF lampiran dari ruangan poli
    public function downloadMcuSummary($jadwalId)
    {
        $jadwal = JadwalMcu::with(['jadwalPoli.poli', 'karyawan', 'pesertaMcu'])->findOrFail($jadwalId);
        $patient = $jadwal->karyawan ?? $jadwal->pesertaMcu;
        $patientName = $patient->nama_lengkap ?? $patient->nama_karyawan ?? 'Pasien';

        $tempFiles = []; 
        $pdfFilesToMerge = [];

        // Membuat folder temporer (temp_pdf) di dalam server lokal jika direktori tersebut belum tersedia
        $tempDirFullPath = storage_path('app/temp_pdf');
        if (!file_exists($tempDirFullPath)) {
            mkdir($tempDirFullPath, 0777, true);
        }

        // Memanggil fungsi render lembar resume utama dan menyimpannya sebagai file fisik sementara di server lokal
        $resumePdf = $this->generateResumePdfObject($jadwal);
        if (!$resumePdf) abort(404, 'Data pasien tidak ditemukan.');

        $tempResumePath = $tempDirFullPath . '/resume_' . $jadwalId . '_' . time() . '.pdf';
        $resumePdf->save($tempResumePath);
        $pdfFilesToMerge[] = $tempResumePath;
        $tempFiles[] = $tempResumePath;

        // Melakukan iterasi ke seluruh tabel jadwal_polis untuk mengambil berkas PDF rekam medis hasil upload per poli dari GCS Cloud Storage
        foreach ($jadwal->jadwalPoli as $jp) {
            if ($jp->file_path && Storage::disk('gcs')->exists($jp->file_path)) {
                try {
                    // Mengunduh isi data biner berkas PDF dari Google Cloud Storage Bucket
                    $fileContent = Storage::disk('gcs')->get($jp->file_path);
                    
                    // Menyimpan data biner dari cloud tersebut menjadi file fisik .pdf temporer di server lokal agar bisa dibaca oleh parser FPDI
                    $localTempPoli = $tempDirFullPath . '/poli_' . $jp->id . '_' . time() . '.pdf';
                    file_put_contents($localTempPoli, $fileContent);
                    
                    // Memasukkan path file lokal ke dalam array daftar antrean merge
                    $pdfFilesToMerge[] = $localTempPoli;
                    $tempFiles[] = $localTempPoli;
                    
                    \Log::info("GCS File Retrieved: " . $jp->file_path);
                } catch (\Exception $e) {
                    \Log::error("Gagal mengambil file GCS: " . $jp->file_path . " Error: " . $e->getMessage());
                }
            }
        }

        // Menginisialisasi objek pustaka parser FPDI TCPDF untuk memulai proses perangkaian lembar halaman PDF
        $pdfMerger = new Fpdi();
        $pdfMerger->setPrintHeader(false); // Menonaktifkan layout running header bawaan TCPDF agar tidak merusak cetakan
        $pdfMerger->setPrintFooter(false); // Menonaktifkan layout running footer bawaan TCPDF

        // Melakukan perulangan baca lembar per lembar dari daftar path file PDF yang dikumpulkan di array antrean
        foreach ($pdfFilesToMerge as $file) {
            try {
                // Parser FPDI membaca jumlah halaman asli dari berkas terkait
                $pageCount = $pdfMerger->setSourceFile($file);
                for ($i = 1; $i <= $pageCount; $i++) {
                    // Mengimpor halaman spesifik ke penampung template
                    $template = $pdfMerger->importPage($i);
                    // Mendeteksi ukuran dan orientasi halaman asli (A4/Letter, Portrait/Landscape) agar halaman hasil gabungan tidak terpotong
                    $size = $pdfMerger->getTemplateSize($template);
                    // Menambahkan lembar halaman kosong baru ke dokumen utama dengan ukuran yang adaptif sesuai aslinya
                    $pdfMerger->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    // Menempelkan konten template halaman asli ke atas halaman kosong yang baru dibuat
                    $pdfMerger->useTemplate($template);
                }
            } catch (\Exception $e) {
                \Log::error("Gagal merge file: {$file}. Error: " . $e->getMessage());
            }
        }

        // PROSES PEMBERSIHAN (CLEANUP): Menghapus seluruh berkas temporer di server lokal agar tidak membebani kapasitas hardisk server
        foreach ($tempFiles as $tempFile) {
            if (file_exists($tempFile)) unlink($tempFile);
        }

        // Mentransfer data biner hasil kompilasi FPDI (Output string biner 'S') langsung sebagai HTTP Response Stream ke browser klien
        $fileName = 'Hasil_Lengkap_MCU_' . str_replace(' ', '_', $patientName) . '.pdf';
        return response($pdfMerger->Output($fileName, 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $fileName . '"');
    }
}