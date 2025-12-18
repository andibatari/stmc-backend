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
        $this->validate();
        $jadwalPoli = $this->jadwal->jadwalPoli->firstWhere('poli_id', $poliId);
        if (!$jadwalPoli) {
            $this->dispatch('error', ['message' => 'Data jadwal poli tidak ditemukan.']);
            return;
        }
        
        if (isset($this->pdfFiles[$poliId])) {
            $file = $this->pdfFiles[$poliId];
            
            // Dapatkan nama file yang bersih
            $patientName = $this->patient->nama_lengkap ?? $this->patient->nama_karyawan ?? 'Pasien';
            $safePatientName = preg_replace('/[^A-Za-z0-9\_]/', '', str_replace(' ', '_', $patientName));
            $fileName = 'HasilPemeriksaan' . '_' . $safePatientName . '_' . $jadwalPoli->poli->nama_poli . '_' . now()->timestamp . '.pdf';
            
            // 🔥 KRITIS: Definisikan path folder *tanpa* menyebutkan disk 'public/' di dalamnya
            // Nama folder di dalam storage/app/public/ adalah 'pdf_reports'
            $folderPath = 'pdf_reports'; 

            try {
                // 🔥 AKSI KRITIS: Simpan file ke disk 'public'
                // storeAs(folder_di_dalam_disk, nama_file, nama_disk)
                $path = $file->storeAs($folderPath, $fileName, 'public'); 
                
                \Log::info("PDF Upload SUKSES untuk Poli {$poliId}. Path Relatif Disk: " . $path); 
                
                // Simpan HANYA NAMA FILE ke database, karena folder sudah diketahui
                // Jika Anda menyimpan $fileName saja, Anda tidak perlu lagi melakukan basename() di Blade
                $jadwalPoli->file_path = $fileName; 
                $jadwalPoli->status = 'Done';
                $jadwalPoli->save();

                $this->uploadedFileNames[$poliId] = $fileName;
                $this->dispatch('file-uploaded', ['message' => 'File berhasil diunggah dan status diatur ke Done.']);
                $this->pdfFiles[$poliId] = null;

            } catch (\Throwable $e) {
                \Log::error("PDF Upload GAGAL KRITIS: " . $e->getMessage());
                // Tambahkan pesan yang meminta pengguna periksa Izin Tulis
                $this->dispatch('error', ['message' => 'Gagal menyimpan file PDF. Ini masalah Izin Tulis folder storage/app/public/.']);
                return;
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