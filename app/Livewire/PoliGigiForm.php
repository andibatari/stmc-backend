<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\JadwalPoli; // Model untuk data jadwal poli
use App\Models\Karyawan; // Model untuk data karyawan (pasien tipe Karyawan)
use App\Models\PesertaMcu; // Model untuk data peserta MCU (pasien tipe Non-Karyawan)
use App\Models\PoliGigiResult; // Model untuk menyimpan hasil pemeriksaan Poli Gigi
use App\Models\UnitKerja; // Model untuk mendapatkan data Unit Kerja Karyawan
use App\Models\Dokter; // Model untuk data Dokter (digunakan untuk memilih dokter penanggung jawab)
use Spatie\Browsershot\Browsershot; // Library untuk mengkonversi HTML menjadi PDF (membutuhkan Node/Puppeteer)
use Illuminate\Support\Facades\Storage; // Facade untuk operasi penyimpanan file (PDF)
use Illuminate\Support\Facades\View; // Facade untuk merender view Blade menjadi string (HTML untuk PDF)
use Illuminate\Support\Facades\Log; // Facade untuk mencatat log, penting untuk debugging PDF
use Illuminate\Support\Facades\URL; // Facade untuk operasi URL
// use Illuminate\Support\Facades\Auth; // Facade untuk mendapatkan data user yang sedang login

/**
 * Livewire Component untuk form pemeriksaan dan hasil Poli Gigi.
 * Bertanggung jawab memuat data pasien (Karyawan/Peserta MCU),
 * menangani input form, validasi, penyimpanan hasil, dan pembuatan PDF.
 */
class PoliGigiForm extends Component
{
    // --- Properti Publik (State Livewire) ---
    
    public $jadwalId; // ID dari entitas JadwalPoli yang sedang diproses
    public $patient; // Objek data pasien (bisa Karyawan atau PesertaMcu)
    public $poliData; // Objek JadwalPoli yang dilewatkan dari parent component/route
    public $poliGigiResult; // Objek model PoliGigiResult (hasil pemeriksaan)
    public $dokterId; // ID Dokter yang dipilih untuk penanggung jawab hasil
    public $listDokter; // Koleksi daftar dokter yang akan ditampilkan di dropdown
    public $isKaryawan = false; // Flag boolean untuk membedakan tipe pasien
    public $instansiPasien; // Nama Perusahaan/Instansi Pasien (ditentukan di mount)
    
    // Properti untuk data form pemeriksaan (nested array)
    public $dataForm;
    // Array asosiatif untuk status klinis gigi (e.g., ['18' => 'Caries', '35' => 'Tambal'])
    public $gigiKlinis = []; 
    public $kesimpulan; // Input kesimpulan hasil pemeriksaan
    public $keterangan; // Input keterangan/saran tambahan
    
    // Definisikan aturan validasi (Contoh sederhana)
    protected $rules = [
        // Aturan untuk dataForm.ekstraOral
        'dataForm.ekstraOral.kelenjar_submandibular' => 'nullable',
        'dataForm.ekstraOral.kelenjar_leher' => 'nullable',
        
        // Aturan untuk dataForm.intraOral (harus diisi/required)
        'dataForm.intraOral.oklusi' => 'required',
        'dataForm.intraOral.torus_palatinus' => 'required',
        'dataForm.intraOral.torus_mandibularis' => 'required',
        'dataForm.intraOral.palatum' => 'required',
        'dataForm.intraOral.diastema' => 'required',
        'dataForm.intraOral.gigi_anomali' => 'required',
        'dataForm.intraOral.ginggiva' => 'required',
        'dataForm.intraOral.karang_gigi' => 'required',
        'dataForm.intraOral.lain_lain' => 'nullable',
        
        // Aturan untuk data hasil lainnya
        'keterangan' => 'nullable|string',
        'kesimpulan' => 'nullable|string',
        'gigiKlinis' => 'nullable|array',
        'dokterId' => 'required|exists:dokters,id', // Dokter harus dipilih dan ada di tabel 'dokters'
    ]; 
    
    /**
     * Metode yang dijalankan pertama kali saat komponen di-load.
     */
    public function mount($jadwalId, $poliData)
    {
        $this->jadwalId = $jadwalId;
        $this->poliData = $poliData; 
        
        // Muat daftar semua dokter untuk dropdown
        try {
            $this->listDokter = Dokter::all();
        } catch (\Exception $e) {
            Log::error('Gagal memuat daftar dokter: ' . $e->getMessage());
            $this->listDokter = collect([]); 
        }

        // Ambil data Jadwal MCU yang terkait
        $jadwalMcu = $poliData->jadwalMcu ?? null;
        $karyawanId = $jadwalMcu->karyawan_id ?? null;
        $pesertaMcuId = $jadwalMcu->peserta_mcus_id ?? null;

        // --- 1. TENTUKAN TIPE PASIEN DAN MUAT DATA DARI ID JADWAL ---
        if ($karyawanId) {
            $this->patient = Karyawan::with('unitKerja')->find($karyawanId); 
            $this->isKaryawan = true;
        } elseif ($pesertaMcuId) {
            $this->patient = PesertaMcu::find($pesertaMcuId);
            $this->isKaryawan = false;
        } else {
            $this->patient = null;
            $this->isKaryawan = false;
        } 
        
        // --- 2. LOGIKA PENENTUAN NAMA PERUSAHAAN/INSTANSI (3 ATURAN) & STANDARISASI DATA ---
        if ($this->patient) {
            
            if ($this->isKaryawan) {
                $unitKerja = $this->patient->unitKerja->nama_unit_kerja ?? 'Unit Kerja Tidak Diketahui';
                $this->instansiPasien = "PT SEMEN TONASA (Unit: {$unitKerja})";
                
                $this->patient->nama_lengkap = $this->patient->nama_karyawan ?? $jadwalMcu->nama_pasien ?? 'N/A';
                $this->patient->nik_pasien = $this->patient->nik_karyawan ?? 'N/A';
                $this->patient->no_sap = $this->patient->id ?? 'N/A';
                $this->patient->nomor_hp = $this->patient->nomor_hp ?? 'N/A';
            } else {
                $perusahaanAsal = $this->patient->perusahaan_asal ?? $jadwalMcu->perusahaan_asal ?? null; 
                
                if ($perusahaanAsal) {
                    $this->instansiPasien = $perusahaanAsal;
                } else {
                    $this->instansiPasien = 'NON-KARYAWAN/UMUM';
                }
                
                $this->patient->nama_lengkap = $this->patient->nama_lengkap ?? $jadwalMcu->nama_pasien ?? 'N/A';
                $this->patient->nik_pasien = $this->patient->nik_pasien ?? 'N/A';
                $this->patient->no_sap = 'N/A'; // Non-Karyawan tidak punya No SAP
                $this->patient->nomor_hp = $this->patient->nomor_hp ?? 'N/A';
            }

            $this->patient->alamat = $this->patient->alamat ?? 'N/A';
            $this->patient->jenis_kelamin = $this->patient->jenis_kelamin ?? 'N/A';
            $this->patient->tanggal_lahir = $this->patient->tanggal_lahir ?? now()->toDateString(); 
        } else {
            // Fallback jika Pasien dan Jadwal tidak ditemukan (Error Handling)
            $this->patient = (object)[
                'no_sap' => 'N/A', 
                'nama_lengkap' => $jadwalMcu->nama_pasien ?? 'Pasien Tidak Ditemukan', 
                'nik_pasien' => $jadwalMcu->nik_pasien ?? 'N/A', 
                'tanggal_lahir' => now()->toDateString(),
                'alamat' => 'N/A',
                'nomor_hp' => 'N/A',
                'jenis_kelamin' => 'N/A',
            ];
            $this->instansiPasien = 'N/A';
        }
        // --- END SAFE PATIENT INITIALIZATION ---

        // Cari atau buat instance PoliGigiResult berdasarkan jadwal_poli_id.
        $this->poliGigiResult = PoliGigiResult::firstOrNew(['jadwal_poli_id' => $this->poliData->id]);

        if ($this->poliGigiResult->exists) {
            // Jika data sudah ada, muat data lama ke properti Livewire
            $data = $this->poliGigiResult->data_pemeriksaan; // Kolom JSON/array di DB
            $this->dataForm = $data['dataForm'] ?? [];
            $this->gigiKlinis = $data['gigiKlinis'] ?? [];
            $this->kesimpulan = $this->poliGigiResult->kesimpulan;
            $this->keterangan = $this->poliGigiResult->keterangan;
            $this->dokterId = $this->poliGigiResult->dokter_id; // Muat ID Dokter yang sebelumnya disimpan
        } else {
            // Jika data belum ada, inisialisasi data form dengan nilai default
            $this->dataForm = [
                'ekstraOral' => [
                    'kelenjar_submandibular' => 'Tak ada', 
                    'kelenjar_leher' => 'Tak ada'
                ],
                'intraOral' => [
                    'oklusi' => 'Normal', 
                    'torus_palatinus' => 'Tidak ada', 
                    'torus_mandibularis' => 'Tidak ada', 
                    'palatum' => 'Dalam/Sedang/Rendah', 
                    'diastema' => 'Tidak Ada', 
                    'gigi_anomali' => 'Tidak Ada', 
                    'ginggiva' => 'Normal', 
                    'karang_gigi' => 'Tak ada', 
                    'lain_lain' => null
                ],
            ];
            $this->gigiKlinis = [];
            $this->dokterId = null; // Biarkan null, user harus memilih
        }
    }

    /**
     * Metode untuk mengubah status klinis gigi.
     */
    public function toggleGigiKlinis($gigiId)
    {
        $currentStatus = $this->gigiKlinis[$gigiId] ?? 'Normal';
        
        $nextStatus = match ($currentStatus) {
            'Normal' => 'Caries',
            'Caries' => 'Missing',
            'Missing' => 'Tambal',
            'Tambal' => 'Normal',
            default => 'Normal', // Fallback
        };

        if ($nextStatus === 'Normal') {
            unset($this->gigiKlinis[$gigiId]);
        } else {
            $this->gigiKlinis[$gigiId] = $nextStatus;
        }
    }

    /**
     * Helper method yang menghasilkan string CSS dinamis
     * berdasarkan status gigi di $this->gigiKlinis.
     */
    public function getDynamicCssProperty()
    {
        $cssString = '';
        foreach ($this->gigiKlinis as $gigiId => $status) {
            $styles = '';
            // Tentukan style berdasarkan status klinis
            if ($status === 'Caries') {
                $styles = 'background-color: #DC2626; border-color: #B91C1C; color: #FFFFFF; font-weight: bold;'; // Merah
            } elseif ($status === 'Missing') {
                $styles = 'background-color: #6B7280; border-color: #4B5563; color: #000000; font-weight: bold;'; // Abu-abu gelap/Hitam
            } elseif ($status === 'Tambal') {
                $styles = 'background-color: #10B981; border-color: #059669; color: #FFFFFF; font-weight: bold;'; // Hijau
            }
            // Tambahkan CSS rule ke string utama
            if (!empty($styles)) {
                $cssString .= "#gigi-{$gigiId} { {$styles} }";
            }
        }
        return $cssString;
    }

    /**
     * Metode utama untuk menyimpan hasil pemeriksaan ke database dan membuat laporan PDF.
     */
    public function simpanHasil()
    {
        // Jalankan validasi sesuai $rules yang telah didefinisikan
        $this->validate();

        // Muat objek Dokter lengkap berdasarkan ID yang dipilih
        $dokter = Dokter::find($this->dokterId); 
        if (!$dokter) {
            session()->flash('error', 'Gagal: Data dokter yang dipilih tidak ditemukan.');
            return; 
        }

        // Kumpulkan semua data form pemeriksaan ke dalam satu array JSON untuk disimpan di DB
        $dataPemeriksaan = [
            'dataForm' => $this->dataForm,
            'gigiKlinis' => $this->gigiKlinis,
        ];
        
        // Isi properti model PoliGigiResult
        $this->poliGigiResult->jadwal_poli_id = $this->poliData->id;
        $this->poliGigiResult->fill([
            'data_pemeriksaan' => $dataPemeriksaan, // Data JSON
            'kesimpulan' => $this->kesimpulan,
            'keterangan' => $this->keterangan,
            'dokter_id' => $this->dokterId, // ID Dokter
        ]);

        // --- Proses Pembuatan dan Penyimpanan PDF Otomatis ---

        // Buat nama file yang aman dan unik
        $patientIdentifier = $this->patient->nama_pasien ?? $this->patient->nama_karyawan ?? 'N/A';
        // Bersihkan string identifier untuk nama file
        $safeIdentifier = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $patientIdentifier);
        $safeIdentifier = str_replace(' ', '_', $safeIdentifier);
        $safeIdentifier = substr($safeIdentifier, 0, 50); 

        $fileName = 'Hasil Pemeriksaan Poli Gigi ' . $safeIdentifier . ' Jadwal ' . $this->poliGigiResult->jadwal_poli_id .' '. time(). '.pdf';
        
        // Path penyimpanan di dalam disk 'public'
        $folderPath = 'pdf_reports';
        $storagePath = $folderPath . '/' . $fileName; // pdf_reports/nama_file.pdf

        try {
            Log::info("Mencoba membuat PDF Poli Gigi dengan Dompdf. Target Path: " . $storagePath);
            
            // Siapkan data yang akan diteruskan ke view Blade PDF
            $data = [
                'dynamicCss' => $this->getDynamicCssProperty(), // CSS untuk status gigi
                'patient' => $this->patient,
                'ekstraOral' => $this->dataForm['ekstraOral'] ?? [],
                'intraOral' => $this->dataForm['intraOral'] ?? [],
                'keterangan' => $this->keterangan,
                'kesimpulan' => $this->kesimpulan,
                'gigiKlinis' => $this->gigiKlinis,
                'dokter' => $dokter, // Objek Dokter yang sudah dimuat
                'isKaryawan' => $this->isKaryawan,
                'instansiPasien' => $this->instansiPasien,
            ];

            // 1. Render View ke PDF menggunakan Dompdf
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdfs.poli-gigi-report', $data);

            // Simpan ke S3 atau Public Disk (Pilih salah satu)
            // Jika Anda ingin digabungkan ke laporan utama (S3), gunakan 's3'
            Storage::disk('s3')->put($storagePath, $pdf->output());
                        
            // 3. SIMPAN PATH FILE KE DATABASE POLI_GIGI_RESULTS
            $this->poliGigiResult->file_path = $fileName;
            $this->poliGigiResult->save(); // Simpan hasil pemeriksaan dan path file ke tabel poli_gigi_results

            // KRITIS: Simpan path LENGKAP ke database
            $this->poliData->file_path = $storagePath; // Hasil: pdf_reports/Hasil_Pemeriksaan_...pdf
            $this->poliData->status = 'Done';
            $this->poliData->save();
            
            session()->flash('success', 'Hasil pemeriksaan gigi dan laporan PDF berhasil disimpan!');
            
        } catch (\Exception $e) {
            // Tangani kegagalan pembuatan PDF
            Log::error('PDF Generation GAGAL (Poli Gigi): ' . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
            session()->flash('error', 'Gagal membuat file PDF. Error: ' . $e->getMessage());
        }

        // Setelah semua tersimpan (baik PDF berhasil atau gagal), update status jadwal
        $this->poliData->status = 'Done';
        $this->poliData->save();

        $this->dispatch('status-updated', ['message' => 'Poli Gigi Selesai.']);
    }

    /**
     * Metode render Livewire.
     */
    public function render()
    {
        // Memastikan data terbaru (termasuk file_path jika ada) dimuat sebelum render
        $this->poliGigiResult = PoliGigiResult::firstOrNew(['jadwal_poli_id' => $this->poliData->id]);
        
        return view('livewire.poli-gigi-form');
    }
}
// ```

// ### Ringkasan Perubahan Kritis

// 1.  **Peralihan PDF:** Kode diubah agar menggunakan **`\Barryvdh\DomPDF\Facade\Pdf::loadView`** (Dompdf). Saya meninggalkan semua kode `Browsershot` (yang dikomentari di file asli Anda) karena Dompdf lebih stabil.
// 2.  **Penyimpanan Ganda (`JadwalPoli`):**
//     * Path disimpan ke `poli_gigi_results` (ini sudah benar untuk menyimpan hasil).
//     * **Baru:** Path disalin dan disimpan ke model **`$this->poliData`** (model `JadwalPoli`) menggunakan baris ini:
//       ```php
//       $this->poliData->file_path = 'public/' . $storagePath;
//       $this->poliData->save();
      
