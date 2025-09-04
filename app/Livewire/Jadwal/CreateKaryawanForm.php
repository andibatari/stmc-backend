<?php

namespace App\Livewire\Jadwal;

use Livewire\Component;
use App\Models\Karyawan; // Asumsikan model Karyawan sudah ada
use Illuminate\Support\Collection;
use App\Models\JadwalMcu; 

class CreateKaryawanForm extends Component
{
    public $search = '';
    public $karyawan_id;
    public $karyawan_nama;
    public $tanggal_mcu;
    public $results = []; // Ini akan di-cast otomatis oleh Livewire menjadi Collection saat digunakan
    public $selectedKaryawan; // <--- Properti baru untuk menyimpan objek karyawan
    public $tipe_pasien = 'ptst'; // Tambahkan properti ini

     public function mount()
    {
        $this->results = new Collection();
    }

    // Properti ini akan di-update secara real-time saat user mengetik
    public function updatedSearch()
    {
        if (strlen($this->search) < 2) {
            $this->results = new Collection();
            return;
        }

        // Query database untuk mencari karyawan berdasarkan nama, NIK, atau No. SAP
        $this->results = Karyawan::where('nama_karyawan', 'like', '%' . $this->search . '%')
                                 ->orWhere('nik_karyawan', 'like', '%' . $this->search . '%')
                                 ->orWhere('no_sap', 'like', '%' . $this->search . '%')
                                 ->limit(10) // Batasi hasil untuk performa
                                 ->get();
    }

    // Metode untuk memilih karyawan dari hasil pencarian
    public function selectKaryawan($id)
    {
        $this->karyawan_id = $id;
        $this->selectedKaryawan = Karyawan::with('departemen')->find($id);

        // Isi kotak pencarian dengan nama karyawan yang dipilih
        // $this->search = $this->selectedKaryawan->nama;
        
        // Sembunyikan daftar hasil
        $this->results = new Collection(); 
    }

    // Metode untuk menyimpan data jadwal
    public function save()
    {
        // 1. Validasi input
        $this->validate([
            'karyawan_id' => 'required',
            'tanggal_mcu' => 'required|date',
        ]);
    
        // 2. Cek apakah jadwal sudah ada untuk karyawan dan tanggal yang sama
        if (JadwalMcu::where('karyawan_id', $this->karyawan_id)->where('tanggal_mcu', $this->tanggal_mcu)->exists()) {
            session()->flash('error', 'Jadwal untuk karyawan ini pada tanggal tersebut sudah ada!');
            return;
        }
    
        // 3. Simpan data ke database
        JadwalMcu::create([
            'tipe_pasien' => $this->tipe_pasien, // Mengambil nilai dari properti
            'karyawan_id' => $this->karyawan_id,
            'tanggal_mcu' => $this->tanggal_mcu,
            'tanggal_pendaftaran' => now(), // Otomatis mengisi tanggal pendaftaran saat ini
            'no_antrean' => null, // Biarkan null, bisa diisi nanti
            'no_sap' => $this->selectedKaryawan->no_sap,
            'status' => 'Scheduled', // Atur status awal
        ]);
    
        // 4. Beri notifikasi sukses dan reset form
        session()->flash('success', 'Jadwal MCU berhasil ditambahkan!');
        $this->reset(['search', 'karyawan_id', 'tanggal_mcu', 'selectedKaryawan']);
    
        // 5. Opsional: Redirect ke halaman lain
        // return redirect()->route('jadwal.index');
    }


    public function render()
    {
        return view('livewire.jadwal.create-karyawan-form');
    }
}