<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Karyawan;
use App\Models\PesertaMcu;
use App\Models\JadwalMcu;

class QrScanner extends Component
{
    public $scannedUuid = null;
    public $patient = null;
    public $message = null;
    public $showPopup = false;
    public $jadwal = null;

    protected $listeners = ['qrCodeScanned' => 'handleScan'];

    public function handleScan($uuid)
    {
        // Reset state untuk setiap scan baru
        $this->reset(['scannedUuid', 'patient', 'message', 'showPopup', 'jadwal']);

        // Cari jadwal berdasarkan UUID.
        $this->jadwal = JadwalMcu::where('qr_code_id', $uuid)
                                 ->with(['pesertaMcu', 'karyawan'])
                                 ->first();

        if ($this->jadwal) {
            // Tentukan data pasien dari relasi yang ada
            $this->patient = $this->jadwal->pesertaMcu ?? $this->jadwal->karyawan;

            if ($this->patient) {
                $this->message = "QR Code pasien berhasil dipindai!";
                $this->showPopup = true;
            } else {
                // REVISI: Gunakan data langsung dari tabel jadwal_mcus jika relasi tidak ditemukan
                $this->message = "Data pasien tidak ditemukan untuk jadwal ini. Menggunakan data fallback.";
                $this->patient = (object) [
                    'nama_lengkap' => $this->jadwal->nama_pasien ?? 'Pasien Tidak Ditemukan',
                    'no_sap' => $this->jadwal->no_sap ?? 'N/A',
                ];
                $this->showPopup = true;
            }
        } else {
            $this->message = "Jadwal tidak ditemukan untuk QR Code ini.";
        }
        $this->scannedUuid = $uuid;
    }

    public function cancelRegistration()
    {
        $this->reset(['scannedUuid', 'patient', 'message', 'showPopup', 'jadwal']);
        $this->dispatch('qrScanResumed'); // Kirim event untuk melanjutkan pemindai
    }

    public function continueRegistration()
    {
        if ($this->patient) {
            if ($this->jadwal) {
                if ($this->jadwal->status === 'Scheduled') {
                    $this->jadwal->status = 'Present';
                    $this->jadwal->save();
                    $this->message = "Jadwal berhasil diperbarui. Status: Hadir!";
                } else {
                    $this->message = "Pasien sudah hadir.";
                }

                // Redirect ke halaman detail pasien setelah pembaruan
                return redirect()->route('qr-patient-detail', ['jadwal' => $this->jadwal->id]);
            }
        }
    }

    public function render()
    {
        return view('livewire.qr-scanner')
            ->layout('layouts.app');
    }
}