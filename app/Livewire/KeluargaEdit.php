<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Keluarga; 
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Departemen;
use App\Models\UnitKerja;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Models\PesertaMcuLogin; // Digunakan untuk validasi NIK

class KeluargaEdit extends Component
{
    public Keluarga $keluarga;
    public $nik_pasien, $nama_lengkap, $jenis_kelamin, $tempat_lahir, $tanggal_lahir, $umur, $golongan_darah;
    public $pendidikan, $pekerjaan, $agama, $alamat, $no_hp, $email, $perusahaan_asal;
    public $provinsi_id = null;
    public $kabupaten_id = null;
    public $kecamatan_id = null;
    public $departemens_id = null;
    public $unit_kerjas_id = null;
    
    // TAMBAHAN UNTUK PASSWORD (Walaupun tidak ada di model Keluarga, tapi di form ada)
    public $password;
    public $password_confirmation;

    public $departemens = [];
    public $unitKerjas = [];
    public $provinsis = [];
    public $kabupatens = [];
    public $kecamatans = [];

    protected function rules()
    {
        return [
            // FIX NIK: Abaikan NIK milik record yang sedang di-edit (diasumsikan NIK ada di tabel Keluarga)
            'nik_pasien' => ['nullable', 'string', 'max:255', 
                            Rule::unique('peserta_mcu_logins', 'nik_pasien')->ignore($this->keluarga->id, 'peserta_mcu_id')],
            
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
            
            // Password hanya jika diisi (untuk update akun login)
            'password' => 'nullable|string|min:6|confirmed',
            'password_confirmation' => 'nullable|required_with:password',
        ];
    }

    public function mount(Keluarga $keluarga)
    {
        $this->keluarga = $keluarga->load('karyawan');
        
        // Isi properti Livewire dengan data dari model Keluarga
        $this->fill($keluarga->toArray());
        
        // Jika model menggunakan tanggal string, format kembali ke Y-m-d untuk input type="date"
        if ($this->tanggal_lahir) {
            $this->tanggal_lahir = Carbon::parse($this->tanggal_lahir)->format('Y-m-d');
            $this->umur = Carbon::parse($this->tanggal_lahir)->age;
        }

        // Muat data untuk dropdown lokasi dan organisasi
        $this->provinsis = Provinsi::orderBy('nama_provinsi')->get();
        $this->departemens = Departemen::orderBy('nama_departemen')->get();

        // Muat dropdown dinamis awal
        $this->updatedProvinsiId($this->provinsi_id);
        $this->updatedDepartemensId($this->departemens_id);
    }

    public function updatedTanggalLahir($value)
    {
        $this->umur = $value ? Carbon::parse($value)->age : null;
    }

    public function updatedProvinsiId($value)
    {
        $this->kabupatens = $value ? Kabupaten::where('provinsi_id', $value)->orderBy('nama_kabupaten')->get() : collect();
        $this->kabupaten_id = null;
        $this->kecamatan_id = null;
    }

    public function updatedKabupatenId($value)
    {
        $this->kecamatans = $value ? Kecamatan::where('kabupaten_id', $value)->orderBy('nama_kecamatan')->get() : collect();
        $this->kecamatan_id = null;
    }

    public function updatedDepartemensId($value)
    {
        $this->unitKerjas = $value ? UnitKerja::where('departemens_id', $value)->orderBy('nama_unit_kerja')->get() : collect();
        $this->unit_kerjas_id = null;
    }

    public function updateKeluarga()
    {
        $this->validate();

        $validatedData = $this->only([
            'nik_pasien', 'nama_lengkap', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 
            'golongan_darah', 'pendidikan', 'pekerjaan', 'agama', 'alamat', 'no_hp', 
            'email', 'provinsi_id', 'kabupaten_id', 'kecamatan_id', 
            'departemens_id', 'unit_kerjas_id', 'perusahaan_asal'
        ]);
        
        $validatedData['umur'] = $this->umur;
        
        try {
            $this->keluarga->update($validatedData);

            // FIX: Update password di tabel peserta_mcu_logins jika password diisi
            if ($this->password) {
                $login = PesertaMcuLogin::where('nik_pasien', $this->keluarga->nik_pasien)->first();
                if ($login) {
                    $login->update(['password' => Hash::make($this->password)]);
                } else {
                    // Jika login tidak ditemukan, buat akun baru jika diperlukan
                    // (Logika ini bergantung pada bagaimana Anda mengelola akun)
                }
            }

            session()->flash('success', 'Data pasien berhasil diperbarui.');

            // Arahkan kembali ke halaman detail karyawan (asumsi Keluarga memiliki relasi karyawan)
            return redirect()->route('karyawan.show', $this->keluarga->karyawan_id);

        } catch (\Exception $e) {
            report($e);
            session()->flash('error', 'Gagal memperbarui data: ' . $e->getMessage());
            return back();
        }
    }

    public function render()
    {
        return view('livewire.keluarga-edit', [
            'provinsis' => $this->provinsis,
            'kabupatens' => $this->kabupatens,
            'kecamatans' => $this->kecamatans,
            'departemens' => $this->departemens,
            'unitKerjas' => $this->unitKerjas,
        ]);
    }
}