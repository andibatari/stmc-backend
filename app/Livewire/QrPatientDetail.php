<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\JadwalMcu;
use App\Models\Karyawan;
use App\Models\PesertaMcu;
use App\Models\JadwalPoli;
use Livewire\WithFileUploads;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan Dompdf Facade tersedia

class QrPatientDetail extends Component
{
    use WithFileUploads;

    public $jadwal;
    public $patient;
    public $activeTab = 'summary';
    public $formInputs = [];
    public $qrCodeImage;
    public $pdfFiles = [];

    // Tambahkan properti untuk data Resume
    public $resumeData = [];
    public $resumeSaran;
    public $resumeKategori;
    
    // Tambahkan properti untuk menyimpan nama file yang telah diunggah
    public $uploadedFileNames = [];
    
    public $uploadablePoliNames = ['LABORATORIUM', 'SPIROMETRI', 'AUDIOMETRI', 'EKG', 'THORAX PHOTO', 'TREADMILL', 'USG'];

    protected $listeners = ['updatePoliStatus'];
    
    // Tambahkan properti ini di paling atas class untuk handle error Livewire Upload
    public function handleUploadError($name, $errors, $isMultiple) {
        $this->dispatch('error', ['message' => 'File terlalu besar atau koneksi terputus.']);
    }

    protected $rules = [
        'pdfFiles.*' => 'nullable|file|mimes:pdf|max:10240',

        // --- RULES BARU UNTUK RESUME DATA (ARRAY) ---
        'resumeData.bmi' => 'nullable|string|max:255',
        'resumeData.laboratorium' => 'nullable|string|max:255',
        'resumeData.ecg' => 'nullable|string|max:255',
        'resumeData.gigi' => 'nullable|string|max:255',
        'resumeData.mata' => 'nullable|string|max:255',
        'resumeData.spirometri' => 'nullable|string|max:255',
        'resumeData.audiometri' => 'nullable|string|max:255',
        'resumeData.kesegaran' => 'nullable|string|max:255',
        'resumeData.temuan_lain' => 'nullable|string|max:255',

        // --- RULES BARU UNTUK THORAX, TREADMILL, USG ---
        'resumeData.thorax_photo' => 'nullable|string|max:255',
        'resumeData.treadmill' => 'nullable|string|max:255',
        'resumeData.usg' => 'nullable|string|max:255',

        'resumeSaran' => 'nullable|string|max:5000',
        'resumeKategori' => 'nullable|string|max:255',
    ];
    
    public function mount(JadwalMcu $jadwal)
    {
        // Muat relasi JadwalPoli menggunakan nama relasi yang benar ('polis')
        // dan pastikan poli data juga dimuat
        // $jadwal->load(['polis.poli', 'karyawan', 'pesertaMcu', 'paketMcu.poli']);
        $this->jadwal = $jadwal;

        // Memuat data resume yang sudah ada (jika ada)
        // Gunakan getDefaultResumeData() untuk memastikan semua field baru terisi null jika belum ada data.
        $existingData = $jadwal->resume_body ? json_decode($jadwal->resume_body, true) : [];
        $this->resumeData = array_merge($this->getDefaultResumeData(), $existingData);

        $this->resumeSaran = $jadwal->resume_saran ?? '';
        $this->resumeKategori = $jadwal->resume_kategori ?? '';
    }

    // Metode untuk inisialisasi resume data
    protected function getDefaultResumeData()
    {
        return [
            'bmi' => null, 'laboratorium' => null, 'ecg' => null,
            'gigi' => null, 'mata' => null, 'spirometri' => null,
            'audiometri' => null, 'kesegaran' => null, 'temuan_lain' => null, 'thorax_photo' => null,
            'treadmill' => null, 'usg' => null,
        ];
    }

    // --- METODE BARU: SIMPAN KESIMPULAN/RESUME ---
    public function saveResume()
    {
        $this->validate();

        try {
            $this->jadwal->update([
                'resume_body' => json_encode($this->resumeData), // <-- UBAH: Simpan array sebagai JSON string
                'resume_saran' => $this->resumeSaran,
                'resume_kategori' => $this->resumeKategori,
                // 'status' => 'Finished', 
            ]);

            $this->dispatch('status-updated', ['message' => 'Resume berhasil disimpan!']);
            $this->jadwal->refresh(); 
            
        } catch (\Throwable $e) {
            \Log::error("Gagal menyimpan resume: " . $e->getMessage());
            $this->dispatch('error', ['message' => 'Gagal menyimpan resume. Silakan coba lagi.']);
        }
    }

    public function downloadAllPdfs()
    {
            // Force refresh the latest data from the database
        $this->jadwal->refresh(); 
        
        // Pengecekan ID tetap bagus untuk dilakukan
        if (!$this->jadwal || !$this->jadwal->id) {
            $this->dispatch('show-error', ['message' => 'ID Jadwal tidak dapat diakses untuk penggabungan file.']);
            return;
        }

        // PENTING: Panggil dispatch yang akan memicu window.open di JavaScript
        $this->dispatch('view-merged-pdf', jadwalId: $this->jadwal->id); 
    }

    public function saveStatus($poliId)
    {
        $status = $this->formInputs[$poliId]['status'] ?? null;
        if ($status) {
            $this->updatePoliStatus($poliId, $status);
        }
    }

    public function updatePoliStatus($poliId, $status) 
    {
        $this->jadwal->refresh();
        $jadwalPoli = $this->jadwal->jadwalPoli->firstWhere('poli_id', $poliId);
        
        if ($jadwalPoli) {
            $jadwalPoli->status = $status;
            $jadwalPoli->save();
            
            $this->jadwal->load('jadwalPoli.poli');
            $this->dispatch('status-updated', ['message' => 'Status poli berhasil diperbarui.']);
        }
    }

    public function savePdf($poliId)
    {
        // Validasi file sesuai rules (max 10MB, mimes:pdf)
        $this->validate([
            "pdfFiles.$poliId" => 'required|file|mimes:pdf|max:10240',
        ]);
        $jadwalPoli = $this->jadwal->jadwalPoli->firstWhere('poli_id', $poliId);
        if (!$jadwalPoli) {
            $this->dispatch('error', ['message' => 'Data jadwal poli tidak ditemukan.']);
            return;
        }
        
        if (isset($this->pdfFiles[$poliId])) {
            try {
                $file = $this->pdfFiles[$poliId];
                
                // Buat nama file yang unik
                $patientName = $this->patient->nama_lengkap ?? $this->patient->nama_karyawan ?? 'Pasien';
                $safePatientName = preg_replace('/[^A-Za-z0-9\_]/', '', str_replace(' ', '_', $patientName));
                $fileName = 'Hasil_' . $safePatientName . '_' . $poliId . '_' . now()->timestamp . '.pdf';
                
                // PATH FOLDER di dalam Bucket S3
                $folderPath = 'mcu_results'; 

                /**
                 * KRITIS: Gunakan disk 's3' agar file dipindahkan dari 
                 * livewire-tmp (di S3) ke folder tujuan (juga di S3).
                 */
                $path = $file->storeAs($folderPath, $fileName, 's3'); 
                
                // Simpan path lengkap atau nama file ke database
                $jadwalPoli->file_path = $path; 
                $jadwalPoli->status = 'Done';
                $jadwalPoli->save();

                // Update UI
                $this->uploadedFileNames[$poliId] = $fileName;
                $this->pdfFiles[$poliId] = null; // Reset input setelah berhasil
                
                $this->dispatch('status-updated', ['message' => 'File berhasil diunggah ke Cloud Storage!']);

            } catch (\Throwable $e) {
                \Log::error("S3 Upload Error: " . $e->getMessage());
                $this->dispatch('error', ['message' => 'Gagal mengunggah ke Cloud: ' . $e->getMessage()]);
            }
        } else {
            $this->dispatch('error', ['message' => 'Silakan pilih file PDF untuk diunggah.']);
        }
    }
    
    public function markAsDone($poliId)
    {
        $this->updatePoliStatus($poliId, 'Done');
    }
    
    public function markAsPending($poliId)
    {
        $this->updatePoliStatus($poliId, 'Pending');
    }
    
    public function render()
    {
        $this->jadwal->refresh()->load(['karyawan', 'pesertaMcu', 'paketMcu.poli', 'jadwalPoli.poli','dokter']);
        $relatedPatient = $this->jadwal->karyawan ?? $this->jadwal->pesertaMcu;
        
        if ($relatedPatient) {
            $this->patient = $relatedPatient;
        } else {
            $this->patient = (object) [
                'nama_lengkap' => $this->jadwal->nama_pasien ?? 'Pasien Tidak Ditemukan',
                'nama_karyawan' => $this->jadwal->nama_pasien ?? 'Pasien Tidak Ditemukan',
                'perusahaan_asal' => $this->jadwal->perusahaan_asal ?? 'N/A',
                'no_sap' => 'N/A',
                'nik_pasien' => 'N/A',
                'nik_karyawan' => 'N/A',
                'tanggal_lahir' => $this->jadwal->tanggal_mcu,
                'jenis_kelamin' => 'N/A',
            ];
        }

        $this->qrCodeImage = null;
        if (!empty($this->jadwal->qr_code_id)) {
            try {
                $qrCode = QrCode::create($this->jadwal->qr_code_id);
                $writer = new PngWriter();
                $result = $writer->write($qrCode);
                $this->qrCodeImage = base64_encode($result->getString());
            } catch (\Exception $e) {
                $this->qrCodeImage = null; 
            }
        }
    
        $polis = $this->jadwal->paketMcu ? $this->jadwal->paketMcu->poli : collect();
        $jadwalPoliData = $this->jadwal->jadwalPoli->keyBy('poli_id');

        foreach ($polis as $poli) {
            if (!isset($this->formInputs[$poli->id])) {
                $poliData = $jadwalPoliData[$poli->id] ?? (object)['status' => 'Pending', 'file_path' => null];
                $this->formInputs[$poli->id]['status'] = $poliData->status;
                // Inisialisasi nama file dari database
                $this->uploadedFileNames[$poli->id] = basename($poliData->file_path);
            }
        }

        return view('livewire.qr-patient-detail', compact('polis', 'jadwalPoliData'))
            ->layout('layouts.app', ['title' => 'Detail Pasien MCU']);
    }
}