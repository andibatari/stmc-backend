<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Karyawan;
use App\Models\EmployeeLogin;
use App\Models\Departemen;
use App\Models\UnitKerja;
use App\Models\Kecamatan;
use App\Models\Kabupaten;
use App\Models\Provinsi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class CreateKaryawanForm extends Component
{
    // Properti form
    public $no_sap, $nik_karyawan, $nama_karyawan, $pekerjaan;
    public $tempat_lahir, $tanggal_lahir, $umur = 0;
    public $jenis_kelamin, $golongan_darah, $agama, $hubungan;
    public $status_pernikahan, $kebangsaan, $jabatan, $eselon;
    public $pendidikan, $unit_kerjas_id, $departemens_id;
    public $provinsi_id, $kabupaten_id, $kecamatan_id;
    public $email, $password, $suami_istri, $pekerjaan_suami_istri;
    public $alamat, $no_hp;

    // Tambahkan properti baru
    public $tinggi_badan, $berat_badan;

    protected $listeners = [
        'departemenUpdated',
        'unitKerjaUpdated',
        'provinsiUpdated' => 'setProvinsiId', 
        'kabupatenUpdated' => 'setKabupatenId',
        'kecamatanUpdated',
    ];

    // Update event dropdown Departemen
    public function departemenUpdated($payload)
    {
        $this->departemens_id = $payload['id'] ?? null;
        $this->unit_kerjas_id = null;
    }

    // Update event dropdown Unit Kerja
    public function unitKerjaUpdated($payload)
    {
        $this->unit_kerjas_id = $payload['id'] ?? null;
    }

    // Update event lokasi berjenjang
    public function setProvinsiId($payload)
    {
        $this->provinsi_id = $payload['id'] ?? null;
    }

    public function setKabupatenId($payload)
    {
        $this->kabupaten_id = $payload['id'] ?? null;
    }

    public function kecamatanUpdated($payload)
    {
        $this->kecamatan_id = $payload['id'] ?? null;
    }

    // Hitung umur otomatis
    public function updatedTanggalLahir($value)
    {
        if (!empty($value)) {
            $birthDate = Carbon::parse($value);
            $this->umur = (int)$birthDate->diffInYears(Carbon::now());
        } else {
            $this->umur = null;
        }
    }

    // Hook kalau provinsi berubah
    public function updatedProvinsiId($value)
    {
        $this->kabupaten_id = null;
        $this->kecamatan_id = null;
        $this->dispatch('provinsiSelected', ['id' => $value]);
    }

    // Hook kalau kabupaten berubah
    public function updatedKabupatenId($value)
    {
        $this->kecamatan_id = null;
        $this->dispatch('kabupatenSelected', ['id' => $value]);
    }

    // Aturan validasi
    protected $rules = [
        'no_sap' => 'required|string|unique:karyawans,no_sap',
        'nik_karyawan' => 'required|string|unique:karyawans,nik_karyawan',
        'nama_karyawan' => 'nullable|string|max:255',
        'jenis_kelamin' => 'nullable|string',
        'unit_kerjas_id' => 'nullable|integer|exists:unit_kerjas,id',
        'departemens_id' => 'nullable|integer|exists:departemens,id',
        'tanggal_lahir' => 'nullable|date',
        'alamat' => 'nullable|string',
        'email' => 'nullable|email|unique:karyawans,email',
        'no_hp' => 'nullable|string',
        'password' => 'nullable|min:6',
        'pendidikan' => 'nullable|string',
        'kebangsaan' => 'nullable|string',
        'tempat_lahir' => 'nullable|string',
        'golongan_darah' => 'nullable|string',
        'pekerjaan' => 'nullable|string',
        'agama' => 'nullable|string',
        'status_pernikahan' => 'nullable|string',
        'hubungan' => 'nullable|string',
        'jabatan' => 'nullable|string',
        'eselon' => 'nullable|string',
        'suami_istri' => 'nullable|string',
        'pekerjaan_suami_istri' => 'nullable|string',
        'provinsi_id' => 'nullable|integer|exists:provinsis,id',
        'kabupaten_id' => 'nullable|integer|exists:kabupatens,id',
        'kecamatan_id' => 'nullable|integer|exists:kecamatans,id',
        // Tambahkan aturan validasi untuk tinggi dan berat badan
        'tinggi_badan' => 'nullable|numeric|min:1',
        'berat_badan' => 'nullable|numeric|min:1',
    ];

    public function saveKaryawan()
    {
        $this->validate();

        $karyawan = Karyawan::create([
            'no_sap' => $this->no_sap,
            'nik_karyawan' => $this->nik_karyawan,
            'nama_karyawan' => $this->nama_karyawan,
            'jenis_kelamin' => $this->jenis_kelamin,
            'pendidikan' => $this->pendidikan,
            'kebangsaan' => $this->kebangsaan,
            'tempat_lahir' => $this->tempat_lahir,
            'umur' => $this->umur,
            // Simpan data tinggi dan berat badan
            'tinggi_badan' => $this->tinggi_badan,
            'berat_badan' => $this->berat_badan,
            'golongan_darah' => $this->golongan_darah,
            'pekerjaan' => $this->pekerjaan,
            'agama' => $this->agama,
            'status_pernikahan' => $this->status_pernikahan,
            'hubungan' => $this->hubungan,
            'jabatan' => $this->jabatan,
            'eselon' => $this->eselon,
            'suami_istri' => $this->suami_istri,
            'pekerjaan_suami_istri' => $this->pekerjaan_suami_istri,
            'unit_kerjas_id' => $this->unit_kerjas_id,
            'departemens_id' => $this->departemens_id,
            'tanggal_lahir' => $this->tanggal_lahir,
            'alamat' => $this->alamat,
            'email' => $this->email,
            'no_hp' => $this->no_hp,
            'provinsi_id' => $this->provinsi_id,
            'kabupaten_id' => $this->kabupaten_id,
            'kecamatan_id' => $this->kecamatan_id,
        ]);

        EmployeeLogin::create([
            'karyawan_id' => $karyawan->id,
            'no_sap' => $this->no_sap,
            'password' => Hash::make($this->password),
        ]);

        $this->dispatch('karyawanSaved');

        $this->reset([
            'no_sap', 'nik_karyawan', 'nama_karyawan', 'jenis_kelamin',
            'pendidikan', 'kebangsaan', 'tempat_lahir', 'umur',
            'tinggi_badan', 'berat_badan', 'golongan_darah', 'pekerjaan', 'agama', 'status_pernikahan',
            'hubungan', 'jabatan', 'eselon', 'suami_istri',
            'pekerjaan_suami_istri', 'unit_kerjas_id', 'departemens_id',
            'tanggal_lahir', 'alamat', 'email', 'no_hp', 'password',
            'provinsi_id', 'kabupaten_id', 'kecamatan_id'
        ]);
    }

    public function render()
    {
        return view('livewire.create-karyawan-form');
    }
}