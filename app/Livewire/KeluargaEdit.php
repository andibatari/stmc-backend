<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Keluarga; 
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Departemen; // Impor model Departemen
use App\Models\UnitKerja;  // Impor model UnitKerja
use Carbon\Carbon;

class KeluargaEdit extends Component
{
    public $keluarga;
    public $nik_pasien, $nama_lengkap, $jenis_kelamin, $tempat_lahir, $tanggal_lahir, $umur, $golongan_darah;
    public $pendidikan, $pekerjaan, $agama, $alamat, $no_hp, $email, $perusahaan_asal;
    public $provinsi_id = null;
    public $kabupaten_id = null;
    public $kecamatan_id = null;
    public $departemens_id = null;
    public $unit_kerjas_id = null;
    
    public $departemens = [];
    public $unitKerjas = [];
    public $provinsis = [];
    public $kabupatens = [];
    public $kecamatans = [];

    protected $rules = [
        'nik_pasien' => 'nullable|string|max:255',
        'nama_lengkap' => 'required|string|max:255',
        'jenis_kelamin' => 'nullable|string|max:255',
        'tempat_lahir' => 'nullable|string|max:255',
        'tanggal_lahir' => 'nullable|date',
        'golongan_darah' => 'nullable|string|max:255',
        'pendidikan' => 'nullable|string|max:255',
        'pekerjaan' => 'nullable|string|max:255',
        'agama' => 'nullable|string|max:255',
        'alamat' => 'nullable|string|max:255',
        'no_hp' => 'nullable|string|max:255',
        'email' => 'nullable|email|max:255',
        'provinsi_id' => 'nullable|exists:provinsis,id',
        'kabupaten_id' => 'nullable|exists:kabupatens,id',
        'kecamatan_id' => 'nullable|exists:kecamatans,id',
        'departemens_id' => 'nullable|exists:departemens,id',
        'unit_kerjas_id' => 'nullable|exists:unit_kerjas,id',
        'perusahaan_asal' => 'nullable|string|max:255',
    ];

    public function mount(Keluarga $keluarga)
    {
        $this->keluarga = $keluarga;
        
        // Isi properti Livewire dengan data dari model Keluarga
        $this->fill($keluarga->toArray());

        // Hitung umur dari tanggal lahir
        if ($this->tanggal_lahir) {
            $this->umur = Carbon::parse($this->tanggal_lahir)->age;
        }

        // Muat data untuk dropdown lokasi dan organisasi
        $this->provinsis = Provinsi::all();
        $this->departemens = Departemen::all();

        if ($this->provinsi_id) {
            $this->kabupatens = Kabupaten::where('provinsi_id', $this->provinsi_id)->get();
        }
        if ($this->kabupaten_id) {
            $this->kecamatans = Kecamatan::where('kabupaten_id', $this->kabupaten_id)->get();
        }
        if ($this->departemens_id) {
            $this->unitKerjas = UnitKerja::where('departemens_id', $this->departemens_id)->get();
        }
    }

    public function updatedTanggalLahir($value)
    {
        $this->umur = $value ? Carbon::parse($value)->age : null;
    }

    public function updatedProvinsiId($value)
    {
        $this->kabupatens = $value ? Kabupaten::where('provinsi_id', $value)->get() : collect();
        $this->kabupaten_id = null;
        $this->kecamatan_id = null;
    }

    public function updatedKabupatenId($value)
    {
        $this->kecamatans = $value ? Kecamatan::where('kabupaten_id', $value)->get() : collect();
        $this->kecamatan_id = null;
    }

    // Metode baru untuk memuat unit kerja berdasarkan departemen
    public function updatedDepartemensId($value)
    {
        $this->unitKerjas = $value ? UnitKerja::where('departemens_id', $value)->get() : collect();
        $this->unit_kerjas_id = null;
    }

    public function updateKeluarga()
    {
        $this->validate();

        $this->keluarga->update([
            'nik_pasien' => $this->nik_pasien,
            'nama_lengkap' => $this->nama_lengkap,
            'jenis_kelamin' => $this->jenis_kelamin,
            'tempat_lahir' => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir,
            'umur' => $this->umur,
            'golongan_darah' => $this->golongan_darah,
            'pendidikan' => $this->pendidikan,
            'pekerjaan' => $this->pekerjaan,
            'agama' => $this->agama,
            'alamat' => $this->alamat,
            'no_hp' => $this->no_hp,
            'email' => $this->email,
            'provinsi_id' => $this->provinsi_id,
            'kabupaten_id' => $this->kabupaten_id,
            'kecamatan_id' => $this->kecamatan_id,
            'departemens_id' => $this->departemens_id, // Tambahkan ini
            'unit_kerjas_id' => $this->unit_kerjas_id, // Tambahkan ini
            'perusahaan_asal' => $this->perusahaan_asal, // Tambahkan ini
        ]);

        session()->flash('success', 'Data pasangan berhasil diperbarui.');

        // Arahkan kembali ke halaman detail karyawan
        return redirect()->route('karyawan.index', $this->keluarga->karyawan_id);
    }

    public function render()
    {
        return view('livewire.keluarga-edit', [
            'provinsis' => $this->provinsis,
            'kabupatens' => $this->kabupatens,
            'kecamatans' => $this->kecamatans,
            'departemens' => $this->departemens, // Kirimkan data departemen
            'unitKerjas' => $this->unitKerjas,   // Kirimkan data unit kerja
            'perusahaan_asal' => $this->perusahaan_asal,
        ]);
    }
}