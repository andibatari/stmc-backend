<?php

namespace App\Http\Controllers;

use App\Models\JadwalMcu;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Tcpdf\Fpdi; // Import library untuk penggabungan

class McuPdfController extends Controller
{
    /**
     * Helper: Menghasilkan PDF Resume sebagai objek DomPDF yang siap digabungkan (tidak di-stream).
     */
    protected function generateResumePdfObject(JadwalMcu $jadwal)
    {
        $patient = $jadwal->karyawan ?? $jadwal->pesertaMcu;
        if (!$patient) {
             // Handle jika pasien tidak ditemukan
             return null;
        }

        $data = [
            'jadwal' => $jadwal,
            'patient' => $patient,
            'tanggal_mcu' => Carbon::parse($jadwal->tanggal_mcu)->format('d/m/Y'),
            'tanggal_cetak' => Carbon::now()->format('d/m/Y'),
            'resume_body_raw' => $jadwal->resume_body,
            'resume_saran' => $jadwal->resume_saran,
            'resume_kategori' => $jadwal->resume_kategori,
            'patient_data' => [
                'nama' => $patient->nama_lengkap ?? $patient->nama_karyawan,
                'alamat' => $patient->alamat ?? 'N/A',
                'nik_sap' => $patient->no_sap ?? $patient->nik_karyawan ?? 'N/A',
                'unit_kerja' => $patient->unitKerja->nama_unit_kerja ?? 'N/A',
                'nab_suhu_kerja' => 28.0 
            ]
        ];

        // Memuat view Blade khusus untuk PDF Resume
        return Pdf::loadView('pdfs.mcu_resume', $data);
    }
    
    /**
     * Fungsi yang dipanggil oleh download.resume.pdf. Hanya men-stream PDF Resume.
     */
    public function downloadResume($jadwalId)
    {
         $jadwal = JadwalMcu::with(['karyawan', 'pesertaMcu', 'paketMcu'])->findOrFail($jadwalId);
         $resumePdf = $this->generateResumePdfObject($jadwal);
         
         $patientName = ($jadwal->karyawan ?? $jadwal->pesertaMcu)->nama_lengkap ?? ($jadwal->karyawan ?? $jadwal->pesertaMcu)->nama_karyawan ?? 'Pasien';
         $fileName = 'Resume_MCU_' . str_replace(' ', '_', $patientName) . '_' . $jadwal->tanggal_mcu . '.pdf';
         
         return $resumePdf->stream($fileName);
    }


    /**
     * 🔥 KRITIS: Fungsi yang dipanggil oleh download.mcu.summary.
     * Menggabungkan PDF Resume (Generated) + PDF Poli (Uploaded).
     */
    public function downloadMcuSummary($jadwalId)
    {
        $jadwal = JadwalMcu::with(['jadwalPoli.poli', 'karyawan', 'pesertaMcu'])->findOrFail($jadwalId);
        $patient = $jadwal->karyawan ?? $jadwal->pesertaMcu;
        $patientName = $patient->nama_lengkap ?? $patient->nama_karyawan ?? 'Pasien';

        $pdfFilesToMerge = [];
        $folderPath = 'pdf_reports';

        // 1. GENERATE PDF RESUME DOKTER & Simpan ke file sementara
        $resumePdf = $this->generateResumePdfObject($jadwal);
        if (!$resumePdf) {
            abort(404, 'Data resume atau pasien tidak ditemukan.');
        }

        $tempDir = 'temp';
        $tempResumePath = storage_path($tempDir . '/resume_' . $jadwalId . '_' . time() . '.pdf');
        
        $tempDirFullPath = storage_path($tempDir);

        if (!file_exists($tempDirFullPath)) {
            // Buat folder 'temp' di dalam direktori 'storage/'
            mkdir($tempDirFullPath, 0777, true);
        }
        
        $resumePdf->save($tempResumePath);

        // ----------------------------------------------------------------
        // 🔥 PENTING: LOGIKA PENGUMPULAN PDF POLI DITAMBAHKAN DI SINI
        // ----------------------------------------------------------------

        // 2. Kumpulkan path semua file PDF Poli yang diunggah dari JadwalPoli
        foreach ($jadwal->jadwalPoli as $jp) {
            // Asumsi file_path menyimpan NAMA FILE saja, bukan path lengkap
            if ($jp->file_path) {
                // Path file mutlak di sistem, menggunakan disk public
                $fullPath = Storage::disk('public')->path($folderPath . '/' . $jp->file_path);
                
                if (file_exists($fullPath)) {
                    $pdfFilesToMerge[] = $fullPath;
                } else {
                    \Log::warning("File PDF Poli tidak ditemukan di storage: " . $fullPath);
                }
            }
        }

        // ----------------------------------------------------------------
        // 3. Gabungkan PDF Resume ke awal daftar PDF Poli
        // ----------------------------------------------------------------
        // Kita pindahkan PDF Resume ke awal array sebelum proses merging
        array_unshift($pdfFilesToMerge, $tempResumePath); 


        // 4. Gabungkan semua file menggunakan FPDI/TCPDF
        $pdfMerger = new Fpdi();
        $pdfMerger->setPrintHeader(false);
        $pdfMerger->setPrintFooter(false);
        $pdfMerger->SetAutoPageBreak(true, 10); 

        foreach ($pdfFilesToMerge as $file) {
            try {
                $pageCount = $pdfMerger->setSourceFile($file);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $template = $pdfMerger->importPage($i);
                    $pdfMerger->AddPage(); 
                    $pdfMerger->useTemplate($template);
                }
            } catch (\Exception $e) {
                \Log::error("Gagal menggabungkan file: {$file}. Error: " . $e->getMessage());
            }
        }
        
        // 5. Hapus file sementara PDF Resume
        if (file_exists($tempResumePath)) {
            unlink($tempResumePath);
        }

        // 6. Stream hasil penggabungan
        $fileName = 'Hasil_Lengkap_MCU_' . str_replace(' ', '_', $patientName) . '_' . $jadwal->tanggal_mcu . '.pdf';
        
        return response($pdfMerger->Output($fileName, 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $fileName . '"');
    }
}