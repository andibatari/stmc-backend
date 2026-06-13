<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\KebugaranResult;
use App\Models\JadwalPoli;
use App\Models\Karyawan;
use App\Models\PesertaMcu;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf; 

class KebugaranForm extends Component
{
    public $jadwalPoliId;
    public $poliData;
    public $patient;
    public $isKaryawan = false;
    public $instansiPasien;
    
    // Properti input
    public $durasi_menit;
    public $beban_latihan; // Sekarang menampung nilai Watt (Angka)
    public $denyut_nadi;
    
    // Properti output (hasil)
    public $vo2_max;
    public $hasilKebugaran = null;
    public $keterangan = null;
    public $umur;
    public $bb;
    public $jenisKelamin;
    public $kebugaranDataId = null;

    protected $rules = [
        'durasi_menit' => 'required|numeric|min:1',
        'beban_latihan' => 'required|numeric|min:1', // Validasi angka (Watt)
        'denyut_nadi' => 'required|numeric|min:1',
    ];

    public function mount($patient, $jadwalPoliId, $poliData)
    {
        $this->patient = $patient;
        $this->jadwalPoliId = $jadwalPoliId;
        $this->poliData = $poliData;

        $jadwalMcu = $poliData->jadwalMcu ?? null;
        $karyawanId = $jadwalMcu->karyawan_id ?? null;
        $pesertaMcuId = $jadwalMcu->peserta_mcus_id ?? null;

        if ($karyawanId) {
            $this->patient = Karyawan::with('unitKerja')->find($karyawanId);
            $this->isKaryawan = true;
        } elseif ($pesertaMcuId) {
            $this->patient = PesertaMcu::find($pesertaMcuId);
            $this->isKaryawan = false;
        }

        if ($this->patient) {
            if ($this->isKaryawan) {
                $unitKerja = $this->patient->unitKerja->nama_unit_kerja ?? 'Unit Kerja Tidak Diketahui';
                $this->instansiPasien = "({$unitKerja})";
                $this->patient->nama_lengkap = $this->patient->nama_karyawan ?? $jadwalMcu->nama_pasien ?? 'N/A';
            } else {
                $perusahaanAsal = $this->patient->perusahaan_asal ?? $jadwalMcu->perusahaan_asal ?? null;
                $this->instansiPasien = $perusahaanAsal ?? 'NON-KARYAWAN/UMUM';
                $this->patient->nama_lengkap = $this->patient->nama_lengkap ?? $jadwalMcu->nama_pasien ?? 'N/A';
            }
            $this->patient->tanggal_lahir = $this->patient->tanggal_lahir ?? now()->toDateString();
        }

        $this->umur = Carbon::parse($this->patient->tanggal_lahir)->age;
        $this->bb = $this->patient->berat_badan ?? 65;
        $this->jenisKelamin = $this->patient->jenis_kelamin ?? 'PRIA';
        
        $kebugaran = KebugaranResult::where('jadwal_poli_id', $this->jadwalPoliId)->first();
        if ($kebugaran) {
            $this->kebugaranDataId = $kebugaran->id;
            $this->durasi_menit = $kebugaran->durasi_menit;
            $this->beban_latihan = $kebugaran->beban_latihan;
            $this->denyut_nadi = $kebugaran->denyut_nadi;
            $this->vo2_max = $kebugaran->vo2_max;
            $this->hasilKebugaran = $kebugaran->indeks_kebugaran;
            $this->keterangan = $kebugaran->kategori;
        }
    }

    public function calculateAndSaveKebugaran()
    {
        $this->validate();

        $usia = (int) $this->umur;
        $bb = (float) $this->bb;
        $watt = (float) $this->beban_latihan;
        $hr = (float) $this->denyut_nadi;
        $jenisKelamin = strtoupper($this->jenisKelamin);

        if ($bb <= 0) {
            session()->flash('error', 'Berat Badan Pasien tidak valid (Nol).');
            return;
        }

        // ==========================================
        // RUMUS PERHITUNGAN SESUAI EXCEL
        // ==========================================
        
        // 1. Hitung VO2 Submaksimal
        // (1.8 * (Watt * 6) / BB) + 7
        $vo2_submax = ((1.8 * ($watt * 6)) / $bb) + 7;

        // 2. Faktor Koreksi Usia (Von Dobeln)
        $faktorUsia = ($jenisKelamin === 'WANITA' || $jenisKelamin === 'PEREMPUAN') 
            ? (1.208 - (0.009 * $usia)) 
            : (1.25 - (0.01 * $usia));

        // 3. Estimasi VO2 Max Total
        $hr_max = 220 - $usia;
        $vo2_max_calculated = $vo2_submax * ($hr_max / $hr) * $faktorUsia;

        $this->vo2_max = round($vo2_max_calculated, 2);
        $this->hasilKebugaran = $this->vo2_max;

        // 4. Kategori Sesuai Excel (=IF(J2<40,"Low",IF(J2<=50,"Average","Good")))
        if ($this->vo2_max < 40) {
            $this->keterangan = 'Low';
        } elseif ($this->vo2_max <= 50) {
            $this->keterangan = 'Average';
        } else {
            $this->keterangan = 'Good';
        }

        // ==========================================
        // SIMPAN DATA
        // ==========================================
        $data = [
            'jadwal_poli_id' => $this->jadwalPoliId, 
            'vo2_max' => $this->vo2_max, 
            'durasi_menit' => $this->durasi_menit,
            'beban_latihan' => $this->beban_latihan, 
            'denyut_nadi' => $this->denyut_nadi, 
            'indeks_kebugaran' => $this->hasilKebugaran, 
            'kategori' => $this->keterangan,
        ];
        
        $kebugaran = KebugaranResult::updateOrCreate(['jadwal_poli_id' => $this->jadwalPoliId], $data);
        $this->kebugaranDataId = $kebugaran->id;

        // GENERATE PDF
        $patientIdentifier = $this->patient->nama_lengkap ?? 'Pasien';
        $safeIdentifier = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $patientIdentifier);
        $fileName = 'Hasil_Kebugaran_' . $safeIdentifier . '_' . time() . '.pdf';
        $fullPath = 'pdf_reports/' . $fileName; 

        try {
            $pdf = Pdf::loadView('pdfs.kebugaran-report', [
                'patient' => $this->patient, 
                'kebugaranResult' => $kebugaran, 
                'instansiPasien' => $this->instansiPasien, 
                'isKaryawan' => $this->isKaryawan,
            ]);
            
            Storage::disk('public')->put($fullPath, $pdf->output());
            
            $kebugaran->file_path = $fullPath; 
            $kebugaran->save();

            $this->poliData->file_path = $fullPath; 
            $this->poliData->status = 'Finished';
            $this->poliData->save();

            session()->flash('success', 'Perhitungan VO2 Max berhasil disimpan!');
        } catch (\Exception $e) {
            Log::error('PDF Kebugaran Gagal: ' . $e->getMessage());
            session()->flash('error', 'Gagal memproses file dokumen.');
        }
    }

    public function render()
    {
        $kebugaranResult = KebugaranResult::where('jadwal_poli_id', $this->jadwalPoliId)->first();
        return view('livewire.kebugaran-form', compact('kebugaranResult'));
    }
}