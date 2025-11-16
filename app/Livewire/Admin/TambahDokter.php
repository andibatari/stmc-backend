<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Dokter; // Menggunakan model Dokter yang benar
use Livewire\WithPagination;

class TambahDokter extends Component
{
    use WithPagination;

    public $nik, $nama_lengkap, $email, $spesialisasi, $tanggal_lahir, $golongan_darah, $no_hp;
    public $daftarSpesialisasi = [];
    public $isEditing = false;
    public $dokterId;

    protected $rules = [
        'nik' => 'required|string|unique:dokters,nik',
        'nama_lengkap' => 'required|string|max:255',
        'email' => 'required|email|unique:dokters,email',
        'spesialisasi' => 'required|string',
        'tanggal_lahir' => 'nullable|date',
        'golongan_darah' => 'nullable|string|max:5',
        'no_hp' => 'nullable|string|max:20',
    ];

    public function mount()
    {
        $this->daftarSpesialisasi = [
            'Dokter Umum',
            'Dokter Gigi & Mulut',
            'Dokter Anak',
            'Dokter Kandungan',
            'Dokter Bedah',
            'Spesialis Mata',
            'Spesialis THT',
            'Spesialis Penyakit Dalam',
            'Lainnya',
        ];
    }

    public function getDokterUsersProperty()
    {
        return Dokter::latest()->paginate(10);
    }

    public function save()
    {
        $this->validate();

        Dokter::create([
            'nik' => $this->nik,
            'nama_lengkap' => $this->nama_lengkap,
            'email' => $this->email,
            'spesialisasi' => $this->spesialisasi,
            'tanggal_lahir' => $this->tanggal_lahir,
            'golongan_darah' => $this->golongan_darah,
            'no_hp' => $this->no_hp,
        ]);

        session()->flash('success', 'Akun dokter berhasil ditambahkan.');
        $this->reset(['nik', 'nama_lengkap', 'email', 'spesialisasi', 'tanggal_lahir', 'golongan_darah', 'no_hp']);
    }

    public function edit($id)
    {
        $dokter = Dokter::findOrFail($id);
        $this->isEditing = true;
        $this->dokterId = $dokter->id;
        $this->nik = $dokter->nik;
        $this->nama_lengkap = $dokter->nama_lengkap;
        $this->email = $dokter->email;
        $this->spesialisasi = $dokter->spesialisasi;
        $this->tanggal_lahir = $dokter->tanggal_lahir;
        $this->golongan_darah = $dokter->golongan_darah;
        $this->no_hp = $dokter->no_hp;
    }

    public function update()
    {
        $validatedData = $this->validate([
            'nik' => 'required|string|unique:dokters,nik,' . $this->dokterId,
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:dokters,email,' . $this->dokterId,
            'spesialisasi' => 'required|string',
            'tanggal_lahir' => 'nullable|date',
            'golongan_darah' => 'nullable|string|max:5',
            'no_hp' => 'nullable|string|max:20',
        ]);
        
        $dokter = Dokter::findOrFail($this->dokterId);
        $dokter->update($validatedData);

        session()->flash('success', 'Data dokter berhasil diperbarui.');
        $this->reset(['nik', 'nama_lengkap', 'email', 'spesialisasi', 'tanggal_lahir', 'golongan_darah', 'no_hp']);
        $this->isEditing = false;
    }

    public function cancelEdit()
    {
        $this->reset(['nik', 'nama_lengkap', 'email', 'spesialisasi', 'tanggal_lahir', 'golongan_darah', 'no_hp']);
        $this->isEditing = false;
    }

    public function delete($id)
    {
        Dokter::findOrFail($id)->delete();
        session()->flash('success', 'Akun dokter berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.admin.tambah-dokter')
            ->layout('layouts.app');
    }
}