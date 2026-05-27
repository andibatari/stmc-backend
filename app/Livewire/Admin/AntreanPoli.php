<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\JadwalPoli; // Pastikan nama model ini sesuai dengan model tabel jadwal_polis kamu
use App\Models\Poli;
use App\Events\StatusPoliUpdatedEvent;
use App\Events\PanggilPasienEvent;

class AntreanPoli extends Component
{
    public $selectedPoliId = null;
    public $listPoli = [];

    public function mount()
    {
        // Ambil daftar nama poli untuk dropdown filter (misal: Poli Gigi, Poli Mata)
        $this->listPoli = Poli::pluck('nama_poli', 'id')->toArray();
    }

    // Mengambil data antrean secara dinamis
    public function getDaftarAntreanProperty()
    {
        // Panggil data jadwal_polis yang statusnya Waiting atau Calling
        $query = JadwalPoli::with(['jadwal.karyawan', 'jadwal.pesertaMcu', 'poli'])
            ->whereIn('status', ['Waiting', 'Calling']);

        // Filter berdasarkan Poli yang dipilih perawat
        if ($this->selectedPoliId) {
            $query->where('poli_id', $this->selectedPoliId);
        }

        // Urutkan berdasarkan siapa yang klik duluan (waktu updated_at paling lama)
        return $query->orderBy('updated_at', 'asc')->get();
    }

    // Fungsi saat tombol "Mulai Periksa / Panggil" ditekan
    public function panggilPasien($id)
    {
        $jadwalPoli = JadwalPoli::with('poli')->find($id);

        if ($jadwalPoli && $jadwalPoli->status === 'Waiting') {
            // 1. Ubah status jadi Calling
            $jadwalPoli->update(['status' => 'Calling']);

            // 2. Trigger auto-reload UI HP Pasien (Jalan Sultan)
            event(new StatusPoliUpdatedEvent($jadwalPoli->jadwal_id));

            // 3. Trigger Pop-Up Alarm Panggilan di HP Pasien
            event(new PanggilPasienEvent($jadwalPoli->jadwal_id, $jadwalPoli->poli->nama_poli));

            session()->flash('success', 'Pasien berhasil dipanggil ke ruangan!');
        }
    }

    public function render()
    {
        return view('livewire.admin.antrean-poli', [
            'daftarAntrean' => $this->daftarAntrean
        ])->layout('layouts.app');
    }
}