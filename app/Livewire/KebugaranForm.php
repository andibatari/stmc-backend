<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\KebugaranResult;
use App\Models\JadwalPoli;
use App\Models\Karyawan;
use App\Models\PesertaMcu;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
// Tambahkan Pustaka PDF
use Barryvdh\DomPDF\Facade\Pdf; 


class KebugaranForm extends Component
{
    // Properti dari parent component (diinisialisasi dari parent blade)
    public $jadwalPoliId;
    public $poliData;

    // Properti yang dimuat di mount()
    public $patient;
    public $isKaryawan = false;
    public $instansiPasien;
    
    // Properti untuk data input form dan hasil
    public $durasi_menit;
    public $beban_latihan;
    public $denyut_nadi;
    public $vo2_max;
    public $hasilKebugaran = null;
    public $keterangan = null;
    public $umur;
    public $bb;
    public $jenisKelamin;
    public $kebugaranDataId = null;

    protected $rules = [
        'durasi_menit' => 'required|integer|min:1',
        'beban_latihan' => 'required|string',
        'denyut_nadi' => 'required|integer|min:1',
        'vo2_max' => 'required|numeric|min:0.01',
    ];

    public function mount($patient, $jadwalPoliId, $poliData)
    {
        $this->patient = $patient;
        $this->jadwalPoliId = $jadwalPoliId;
        $this->poliData = $poliData;

        // Tentukan tipe pasien dan muat data dari ID Jadwal
        $jadwalMcu = $poliData->jadwalMcu ?? null;
        $karyawanId = $jadwalMcu->karyawan_id ?? null;
        $pesertaMcuId = $jadwalMcu->peserta_mcus_id ?? null;

        if ($karyawanId) {
            $this->patient = Karyawan::with('unitKerja')->find($karyawanId); // Mengganti ini agar data lengkap
            $this->isKaryawan = true;
        } elseif ($pesertaMcuId) {
            $this->patient = PesertaMcu::find($pesertaMcuId);
            $this->isKaryawan = false;
        }

        // Tentukan nama perusahaan/instansi
        if ($this->patient) {
            if ($this->isKaryawan) {
                $unitKerja = $this->patient->unitKerja->nama_unit_kerja ?? 'Unit Kerja Tidak Diketahui';
                $this->instansiPasien = "({$unitKerja})";
                $this->patient->nama_lengkap = $this->patient->nama_karyawan ?? $jadwalMcu->nama_pasien ?? 'N/A';
                $this->patient->nik_pasien = $this->patient->nik_karyawan ?? 'N/A';
                $this->patient->no_sap = $this->patient->id ?? 'N/A';
            } else {
                $perusahaanAsal = $this->patient->perusahaan_asal ?? $jadwalMcu->perusahaan_asal ?? null;
                $this->instansiPasien = $perusahaanAsal ?? 'NON-KARYAWAN/UMUM';
                $this->patient->nama_lengkap = $this->patient->nama_lengkap ?? $jadwalMcu->nama_pasien ?? 'N/A';
                $this->patient->nik_pasien = $this->patient->nik_pasien ?? 'N/A';
                $this->patient->no_sap = 'N/A';
            }
            $this->patient->alamat = $this->patient->alamat ?? 'N/A';
            $this->patient->jenis_kelamin = $this->patient->jenis_kelamin ?? 'N/A';
            $this->patient->tanggal_lahir = $this->patient->tanggal_lahir ?? now()->toDateString();
        } else {
            $this->patient = (object)['no_sap' => 'N/A', 'nama_lengkap' => 'Pasien Tidak Ditemukan', 'nik_pasien' => 'N/A', 'tanggal_lahir' => now()->toDateString(), 'alamat' => 'N/A', 'jenis_kelamin' => 'N/A',];
            $this->instansiPasien = 'N/A';
        }

        // Inisialisasi data pasien untuk perhitungan
        $this->umur = Carbon::parse($this->patient->tanggal_lahir)->age;
        $this->bb = $this->patient->berat_badan ?? 65;
        $this->jenisKelamin = $this->patient->jenis_kelamin ?? 'PRIA';
        
        // Cari atau buat instance KebugaranResult
        $kebugaran = KebugaranResult::where('jadwal_poli_id', $this->jadwalPoliId)->first();
        if ($kebugaran) {
            $this->kebugaranDataId = $kebugaran->id;
            $this->durasi_menit = $kebugaran->durasi_menit;
            $this->beban_latihan = $kebugaran->beban_latihan;
            $this->denyut_nadi = $kebugaran->denyut_nadi;
            $this->vo2_max = $kebugaran->vo2_max;
            $this->hasilKebugaran = $kebugaran->indeks_kebugaran;
            $this->keterangan = $kebugaran->kategori;
        } else {
            $this->durasi_menit = null; $this->beban_latihan = null; $this->denyut_nadi = null; $this->vo2_max = null;
        }
    }

    public function calculateAndSaveKebugaran()
    {
        $this->validate();

        $usia = $this->umur;
        $bb = $this->bb;
        $vo2Max = $this->vo2_max;
        $jenisKelamin = strtoupper($this->jenisKelamin);

        if ($bb <= 0) {
            session()->flash('error', 'Berat Badan Pasien tidak boleh nol atau negatif.');
            return;
        }

        // --- PENYESUAIAN RUMUS BERDASARKAN JENIS KELAMIN ---
        // (Rumus ini dipertahankan dari versi sebelumnya, yang membedakan berdasarkan jenis kelamin)
        if ($jenisKelamin === 'WANITA' || $jenisKelamin === 'PEREMPUAN') {
            $faktorRisikoDasar = (1.208 - (0.009 * $usia));
        } else {
            $faktorRisikoDasar = (1.25 - (0.01 * $usia));
        }

        // Perhitungan Indeks Kebugaran Jasmani
        $this->hasilKebugaran = ($faktorRisikoDasar * $vo2Max * 1000) / $bb;
        // PENTING: Panggil getKeterangan untuk mendapatkan kategori yang benar
        $this->keterangan = $this->getKeterangan($this->hasilKebugaran, $usia, $this->jenisKelamin);

        $data = [
            'jadwal_poli_id' => $this->jadwalPoliId, 'vo2_max' => $this->vo2_max, 'durasi_menit' => $this->durasi_menit,
            'beban_latihan' => $this->beban_latihan, 'denyut_nadi' => $this->denyut_nadi, 
            'indeks_kebugaran' => $this->hasilKebugaran, 'kategori' => $this->keterangan,
        ];
        
        $kebugaran = KebugaranResult::updateOrCreate(['jadwal_poli_id' => $this->jadwalPoliId], $data);
        $this->kebugaranDataId = $kebugaran->id;

        // --- LOGIKA PEMBUATAN PDF DENGAN DOMPDF ---
        $patientIdentifier = $this->patient->nama_pasien ?? $this->patient->nama_karyawan ?? 'N/A';
        $safeIdentifier = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $patientIdentifier);
        $fileName = 'Hasil Pemeriksaan Kebugaran ' . $safeIdentifier . ' JadwalPoli ' . $this->jadwalPoliId . '.pdf';
        $storagePath = 'pdf_reports/' . $fileName; 

        try {
            $reportData = [
                'patient' => $this->patient, 'kebugaranResult' => $kebugaran, 
                'instansiPasien' => $this->instansiPasien, 'isKaryawan' => $this->isKaryawan,
            ];

            // 1. Render View ke PDF menggunakan Dompdf
            $pdf = Pdf::loadView('pdfs.kebugaran-report', $reportData);
            
            // 2. Simpan konten mentah ke disk 'public'
            Storage::disk('public')->put($storagePath, $pdf->output());
            
            // SIMPAN PATH FILE KE KEBUGARANRESULT
            $kebugaran->file_path = 'public/' . $storagePath; 
            $kebugaran->save();
            
            // 🔥 KRITIS: SALIN PATH KE TABEL JADWAL_POLI 🔥
            $this->poliData->file_path = $kebugaran->file_path; // Ambil path dari KebugaranResult
            
            // Update status poli
            $this->poliData->status = 'Done';
            $this->poliData->save();

            session()->flash('success', 'Perhitungan kebugaran berhasil disimpan dan Laporan PDF diperbarui.');

        } catch (\Exception $e) {
            Log::error('PDF Kebugaran GAGAL (Dompdf): ' . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
            session()->flash('error', 'Gagal membuat file PDF Kebugaran (Dompdf). Error: ' . $e->getMessage());
        }
    }

    private function getKeterangan($indeks, $usia, $jenisKelamin)
    {
        $usia = (int) $usia;
        $jenisKelamin = strtoupper($jenisKelamin);
        
        // --- DATA TABEL BATAS BAWAH (BERDASARKAN GAMBAR YANG DIKIRIM) ---
        // Catatan: Data ini menggunakan batas BAWAH untuk penentuan kategori.
        
        // Pria (Batas Bawah)
        $priaTabel = [
            '18-29' => ['Sangat Kurang' => 0.0, 'Kurang' => 38.1, 'Cukup' => 42.2, 'Baik' => 45.7, 'Baik sekali' => 51.1, 'Sangat Baik Sekali' => 56.1],
            '30-39' => ['Sangat Kurang' => 0.0, 'Kurang' => 36.7, 'Cukup' => 41.0, 'Baik' => 44.4, 'Baik sekali' => 48.9, 'Sangat Baik Sekali' => 54.2],
            '40-49' => ['Sangat Kurang' => 0.0, 'Kurang' => 34.6, 'Cukup' => 38.4, 'Baik' => 42.4, 'Baik sekali' => 46.8, 'Sangat Baik Sekali' => 52.8],
            '50-59' => ['Sangat Kurang' => 0.0, 'Kurang' => 31.1, 'Cukup' => 35.2, 'Baik' => 38.3, 'Baik sekali' => 43.3, 'Sangat Baik Sekali' => 49.6],
            '60-69' => ['Sangat Kurang' => 0.0, 'Kurang' => 27.4, 'Cukup' => 31.4, 'Baik' => 35.0, 'Baik sekali' => 39.5, 'Sangat Baik Sekali' => 46.0],
        ];
        
        // Wanita (Menggunakan batas bawah yang umum)
        $wanitaTabel = [
            '18-29' => ['Sangat Kurang' => 0.0, 'Kurang' => 29.9, 'Cukup' => 33.9, 'Baik' => 36.8, 'Baik sekali' => 41.1, 'Sangat Baik Sekali' => 46.9],
            '30-39' => ['Sangat Kurang' => 0.0, 'Kurang' => 28.0, 'Cukup' => 31.6, 'Baik' => 35.1, 'Baik sekali' => 38.9, 'Sangat Baik Sekali' => 45.2],
            '40-49' => ['Sangat Kurang' => 0.0, 'Kurang' => 25.5, 'Cukup' => 28.7, 'Baik' => 31.4, 'Baik sekali' => 35.2, 'Sangat Baik Sekali' => 40.0],
            '50-59' => ['Sangat Kurang' => 0.0, 'Kurang' => 23.7, 'Cukup' => 26.6, 'Baik' => 29.1, 'Baik sekali' => 32.3, 'Sangat Baik Sekali' => 37.0],
            '60-69' => ['Sangat Kurang' => 0.0, 'Kurang' => 21.4, 'Cukup' => 24.4, 'Baik' => 27.0, 'Baik sekali' => 30.0, 'Sangat Baik Sekali' => 34.0],
        ];


        $tabel = ($jenisKelamin === 'PRIA' || $jenisKelamin === 'LAKI-LAKI') ? $priaTabel : $wanitaTabel;
        $rentangUsia = $this->getRentangUsia($usia);
        
        if (!isset($tabel[$rentangUsia])) {
            return 'Data Usia Tidak Ditemukan';
        }
        
        $batas = $tabel[$rentangUsia];
        
        // --- LOGIKA PENENTUAN KATEGORI (Membandingkan dengan Batas Bawah) ---
        // Jika nilai indeks >= batas bawah suatu kategori, maka ia masuk kategori tersebut.
        
        if ($indeks >= $batas['Sangat Baik Sekali']) return 'Sangat Baik Sekali';
        if ($indeks >= $batas['Baik sekali']) return 'Baik sekali';
        if ($indeks >= $batas['Baik']) return 'Baik';
        if ($indeks >= $batas['Cukup']) return 'Cukup';
        if ($indeks >= $batas['Kurang']) return 'Kurang';
        
        return 'Sangat Kurang';
    }

    private function getRentangUsia($usia)
    {
        if ($usia >= 18 && $usia <= 29) return '18-29';
        if ($usia >= 30 && $usia <= 39) return '30-39';
        if ($usia >= 40 && $usia <= 49) return '40-49';
        if ($usia >= 50 && $usia <= 59) return '50-59';
        if ($usia >= 60 && $usia <= 69) return '60-69';
        
        return 'Tidak Ditemukan';
    }
    
    public function render()
    {
        $kebugaranResult = KebugaranResult::where('jadwal_poli_id', $this->jadwalPoliId)->first();
        
        return view('livewire.kebugaran-form', [
            'kebugaranResult' => $kebugaranResult
        ]);
    }
}
  