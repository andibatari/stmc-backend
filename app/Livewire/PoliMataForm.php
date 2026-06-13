<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\JadwalPoli;
use App\Models\Karyawan;
use App\Models\PesertaMcu;
use App\Models\Dokter;
use App\Models\MataResult;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class PoliMataForm extends Component
{
    public $jadwalPoliId; 
    public $poliData; 
    public $patient;
    public $mataResult;
    public $listDokter;
    public $dokterId;
    
    public $dataMata;
    public $kesimpulan;
    public $keterangan;

    protected $rules = [
        'dokterId' => 'required|exists:dokters,id',
        'dataMata.visus_kanan' => 'required|string|max:100',
        'dataMata.visus_kiri' => 'required|string|max:100',
        'dataMata.add' => 'nullable|string|max:50',
        'dataMata.pd' => 'nullable|string|max:50',
        
        'kesimpulan' => 'nullable|string',
        'keterangan' => 'nullable|string',
    ];

    public function mount() 
    {
        try { $this->listDokter = Dokter::all(); } catch (\Exception $e) { $this->listDokter = collect([]); }

        $jadwalMcu = $this->poliData->jadwalMcu ?? null;
        $karyawanId = $jadwalMcu->karyawan_id ?? null;
        $pesertaMcuId = $jadwalMcu->peserta_mcus_id ?? null;

        if ($karyawanId) {
            $this->patient = Karyawan::with('unitKerja')->find($karyawanId);
        } elseif ($pesertaMcuId) {
            $this->patient = PesertaMcu::find($pesertaMcuId);
        }

        if (!($this->poliData instanceof JadwalPoli) || !isset($this->poliData->id)) {
            $this->mataResult = new MataResult();
        } else {
            $this->mataResult = MataResult::firstOrNew(['jadwal_poli_id' => $this->poliData->id]);
        }

        if ($this->mataResult->exists) {
            $this->dataMata = $this->mataResult->data_mata;
            $this->kesimpulan = $this->mataResult->kesimpulan;
            $this->keterangan = $this->mataResult->keterangan;
            $this->dokterId = $this->mataResult->dokter_id;
        } else {
            $this->dataMata = [
                'visus_kanan' => 'Plano 6/6',
                'visus_kiri' => 'Plano 6/6',
                'add' => '+2.00',
                'pd' => '-/60',
            ];
            $this->dokterId = null;
        }
    }

    public function simpanHasil()
    {
        $this->validate();

        $dataToSave = [
            'data_mata' => $this->dataMata,
            'kesimpulan' => $this->kesimpulan,
            'keterangan' => $this->keterangan,
            'dokter_id' => $this->dokterId,
        ];

        try {
            $this->mataResult = MataResult::updateOrCreate(
                ['jadwal_poli_id' => $this->poliData->id],
                $dataToSave
            );

            // ==========================================
            // LOGIKA PEMBANGKITAN PDF (GENERATE PDF)
            // ==========================================
            $dokter = Dokter::find($this->dokterId);
            $patientIdentifier = $this->patient->nama_pasien ?? $this->patient->nama_karyawan ?? 'N/A';
            $safeIdentifier = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $patientIdentifier);
            
            $fileName = 'Hasil_Pemeriksaan_Poli_Mata_' . $safeIdentifier . '_Jadwal_' . $this->poliData->id . '_' . time() . '.pdf';
            $folderPath = 'pdf_reports';
            $storagePath = $folderPath . '/' . $fileName; 

            // Siapkan data yang akan dikirim ke view PDF
            $reportData = [
                'patient' => $this->patient,
                'mataResult' => $this->mataResult,
                'dokter' => $dokter,
                'instansiPasien' => $this->instansiPasien,
            ];

            // Panggil view PDF yang baru saja kita buat
            $pdf = Pdf::loadView('pdfs.poli-mata-report', $reportData);

            // Simpan ke storage public
            $uploadSuccess = \Illuminate\Support\Facades\Storage::disk('public')->put($storagePath, $pdf->output());
            if (!$uploadSuccess) {
                throw new \Exception("Sistem Gagal Mengunggah PDF. Pastikan disk terkonfigurasi dengan benar.");
            }

            // Simpan lokasi file ke database
            $this->mataResult->file_path = $storagePath; 
            $this->mataResult->save();

            $this->poliData->file_path = $storagePath;
            // ==========================================

            $this->poliData->status = 'Finished';
            $this->poliData->save();

            session()->flash('success', 'Hasil pemeriksaan Poli Mata dan laporan PDF berhasil disimpan!');

        } catch (\Exception $e) {
            Log::error('Penyimpanan Poli Mata GAGAL: ' . $e->getMessage());
            session()->flash('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.poli-mata-form');
    }
}