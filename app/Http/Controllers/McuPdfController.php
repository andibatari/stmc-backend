<?php

namespace App\Http\Controllers;

use App\Models\JadwalMcu;
use App\Models\JadwalPoli; // TAMBAHKAN INI
use App\Models\PoliGigiResult;
use App\Models\KebugaranResult;
use App\Models\FisikResult;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Tcpdf\Fpdi; // Import library untuk penggabungan

class McuPdfController extends Controller
{
    // Fungsi baru untuk melihat PDF dari S3
    public function viewPdf($id) {
        // Cari data di JadwalPoli karena file_path ada di sana
        $poliData = JadwalPoli::findOrFail($id);
        
        // Cek apakah file ada di S3 (DigitalOcean Spaces)
        if ($poliData->file_path && Storage::disk('s3')->exists($poliData->file_path)) {
            // Redirect langsung ke URL file di S3
            return redirect(Storage::disk('s3')->url($poliData->file_path));
        }
        
        abort(404, "File tidak ditemukan di Cloud Storage (S3).");
    }

    public function viewPdfGigi($id) {
        $result = PoliGigiResult::where('jadwal_poli_id', $id)->firstOrFail();
        return $this->redirectS3($result->file_path);
    }

    public function viewPdfKebugaran($id) {
        $result = KebugaranResult::where('jadwal_poli_id', $id)->firstOrFail();
        return $this->redirectS3($result->file_path);
    }

    public function viewPdfFisik($id) {
        $result = FisikResult::where('jadwal_poli_id', $id)->firstOrFail();
        return $this->redirectS3($result->file_path);
    }

    // Helper function agar kode tidak berulang
    private function redirectS3($filePath) {
        if ($filePath && Storage::disk('s3')->exists($filePath)) {
            return redirect(Storage::disk('s3')->url($filePath));
        }
        abort(404, "File tidak ditemukan di S3 Cloud Storage.");
    }
    
    /**
     * Helper: Menghasilkan PDF Resume sebagai objek DomPDF yang siap digabungkan (tidak di-stream).
     */
    protected function generateResumePdfObject(JadwalMcu $jadwal)
    {
        // PENTING: Eager load relasi dokter
        $jadwal->load(['dokter', 'paketMcu']);
        
        $patient = $jadwal->karyawan ?? $jadwal->pesertaMcu;
        if (!$patient) return null;
        
        // KRITIS: Mengambil data dokter
        $doctor = $jadwal->dokter;

        // Pengecekan keamanan: Pastikan $doctor adalah objek Model sebelum mengakses propertinya
        $doctorName = 'Dokter Tidak Ditunjuk';
        $doctorNip = 'NIP. N/A';

        if ($doctor) {
            // KRITIS: Coba ambil nama dari beberapa kemungkinan kolom di Model Dokter
            $doctorName = $doctor->nama_lengkap ?? $doctor->name ?? $doctor->nama ?? 'Dokter Tidak Ditunjuk';
            $doctorNip = $doctor->nip ?? 'NIP. XXXXXXXXXXXXX';
        }

        $data = [
            'jadwal' => $jadwal,
            'patient' => $patient,
            'tanggal_mcu' => Carbon::parse($jadwal->tanggal_mcu)->format('d/m/Y'),
            'tanggal_cetak' => Carbon::now()->format('d/m/Y'),
            'resume_body_raw' => $jadwal->resume_body,
            'resume_saran' => $jadwal->resume_saran,
            'resume_kategori' => $jadwal->resume_kategori,
            'doctor_data' => [
                'nama' => $doctorName,
                'nip' => $doctorNip,
            ],
            'patient_data' => [
                'nama' => $patient->nama_lengkap ?? $patient->nama_karyawan,
                'alamat' => $patient->alamat ?? 'N/A',
                'tgl_lahir' => $patient->tanggal_lahir ?? $patient->tanggal_lahir,
                'jenis_kelamin' => $patient->jenis_kelamin ?? ($patient->jenis_kelamin == 'M' ? 'Laki-laki' : 'Perempuan'),
                'paket_mcu' => $jadwal->paketMcu->nama_paket ?? 'N/A',
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
         $jadwal = JadwalMcu::with(['karyawan', 'pesertaMcu', 'paketMcu','dokter'])->findOrFail($jadwalId);
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

        $tempFiles = []; // Untuk tracking file yang perlu dihapus nanti
        $pdfFilesToMerge = [];

        // 1. BUAT FOLDER TEMP JIKA BELUM ADA
        $tempDirFullPath = storage_path('app/temp_pdf');
        if (!file_exists($tempDirFullPath)) {
            mkdir($tempDirFullPath, 0777, true);
        }

        // 2. GENERATE RESUME & SIMPAN LOKAL
        $resumePdf = $this->generateResumePdfObject($jadwal);
        if (!$resumePdf) abort(404, 'Data pasien tidak ditemukan.');

        $tempResumePath = $tempDirFullPath . '/resume_' . $jadwalId . '_' . time() . '.pdf';
        $resumePdf->save($tempResumePath);
        $pdfFilesToMerge[] = $tempResumePath;
        $tempFiles[] = $tempResumePath;

        // 3. AMBIL FILE DARI S3 (DigitalOcean Spaces)
        foreach ($jadwal->jadwalPoli as $jp) {
            if ($jp->file_path && Storage::disk('s3')->exists($jp->file_path)) {
                try {
                    // Ambil isi file dari S3
                    $fileContent = Storage::disk('s3')->get($jp->file_path);
                    
                    // Simpan sementara di server lokal agar bisa dibaca FPDI
                    $localTempPoli = $tempDirFullPath . '/poli_' . $jp->id . '_' . time() . '.pdf';
                    file_put_contents($localTempPoli, $fileContent);
                    
                    $pdfFilesToMerge[] = $localTempPoli;
                    $tempFiles[] = $localTempPoli;
                    
                    \Log::info("S3 File Downloaded: " . $jp->file_path);
                } catch (\Exception $e) {
                    \Log::error("Gagal mendownload file S3: " . $jp->file_path . " Error: " . $e->getMessage());
                }
            }
        }

        // 4. PROSES MERGING
        $pdfMerger = new Fpdi();
        $pdfMerger->setPrintHeader(false);
        $pdfMerger->setPrintFooter(false);

        foreach ($pdfFilesToMerge as $file) {
            try {
                $pageCount = $pdfMerger->setSourceFile($file);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $template = $pdfMerger->importPage($i);
                    $size = $pdfMerger->getTemplateSize($template);
                    $pdfMerger->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdfMerger->useTemplate($template);
                }
            } catch (\Exception $e) {
                \Log::error("Gagal merge file: {$file}. Error: " . $e->getMessage());
            }
        }

        // 5. CLEANUP: Hapus semua file sementara
        foreach ($tempFiles as $tempFile) {
            if (file_exists($tempFile)) unlink($tempFile);
        }

        // 6. OUTPUT
        $fileName = 'Hasil_Lengkap_MCU_' . str_replace(' ', '_', $patientName) . '.pdf';
        return response($pdfMerger->Output($fileName, 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $fileName . '"');
    }
}