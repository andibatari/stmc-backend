<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Karyawan;
use App\Models\EmployeeLogin;
use App\Models\Departemen;
use App\Models\UnitKerja;
use App\Models\Kecamatan;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log; // Import kelas Log untuk debugging

class EditKaryawanForm extends Component
{
    // Properti publik untuk form yang akan di-bind dengan wire:model
    public Karyawan $karyawan;
    public $no_sap;
    public $nik_karyawan;
    public $nama_karyawan;
    public $pekerjaan;
    public $tempat_lahir;
    public $tanggal_lahir;
    public $umur;
    public $jenis_kelamin;
    public $golongan_darah;
    public $agama;
    public $hubungan;
    public $status_pernikahan;
    public $kebangsaan;
    public $jabatan;
    public $eselon;
    public $pendidikan;
    public $suami_istri;
    public $pekerjaan_suami_istri;
    public $alamat;
    public $no_hp;
    public $email;
    public $password = ''; // Nilai awal kosong agar tidak terisi secara otomatis

    // Properti untuk dropdown
    public $departemens = [];
    public $unitKerjas = [];
    public $provinsis = [];
    public $kabupatens = [];
    public $kecamatans = [];
    
    // Properti yang terikat dengan dropdown
    public $departemens_id = null;
    public $unit_kerjas_id = null;
    public $provinsi_id = null;
    public $kabupaten_id = null;
    public $kecamatan_id = null;

    /**
     * Metode mount akan dijalankan saat komponen diinisialisasi.
     * Ini digunakan untuk memuat data awal dari objek Karyawan.
     */
    public function mount(Karyawan $karyawan)
    {
        $this->karyawan = $karyawan;

        // Mengisi properti Livewire dengan data karyawan yang ada
        $this->fill($karyawan->toArray());

        // Memastikan umur adalah integer saat form dimuat
        if ($this->tanggal_lahir) {
            $this->umur = (int)Carbon::parse($this->tanggal_lahir)->diffInYears(Carbon::now());
        }

        // Memuat ID dari relasi
        $this->departemens_id = $karyawan->departemens_id;
        $this->unit_kerjas_id = $karyawan->unit_kerjas_id;
        $this->kecamatan_id = $karyawan->kecamatan_id;
        
        // Memuat ID untuk chained location jika relasi ada
        if ($karyawan->kecamatan) {
            $this->provinsi_id = $karyawan->kecamatan->kabupaten->provinsi_id ?? null;
            $this->kabupaten_id = $karyawan->kecamatan->kabupaten_id ?? null;
        }

        // Memuat data awal untuk semua dropdown
        $this->loadDropdowns();
    }

    /**
     * Memuat data untuk semua dropdown saat komponen dimuat.
     */
    protected function loadDropdowns()
    {
        $this->departemens = Departemen::all();
        $this->provinsis = Provinsi::all();

        // Muat unit kerja jika departemen sudah dipilih
        if ($this->departemens_id) {
            $this->unitKerjas = UnitKerja::where('departemens_id', $this->departemens_id)->get();
        }

        // Muat kabupaten jika provinsi sudah dipilih
        if ($this->provinsi_id) {
            $this->kabupatens = Kabupaten::where('provinsi_id', $this->provinsi_id)->get();
        }

        // Muat kecamatan jika kabupaten sudah dipilih
        if ($this->kabupaten_id) {
            $this->kecamatans = Kecamatan::where('kabupaten_id', $this->kabupaten_id)->get();
        }
    }

    /**
     * Hook yang dijalankan saat properti tanggal_lahir diubah.
     * Digunakan untuk menghitung ulang umur.
     */
    public function updatedTanggalLahir($value)
    {
        if (!empty($value)) {
            $birthDate = Carbon::parse($value);
            $this->umur = (int)$birthDate->diffInYears(Carbon::now());
        } else {
            $this->umur = null;
        }
    }

    public function updatedDepartemensId($value)
    {
        $this->unit_kerjas_id = null; // Reset unit kerja saat departemen berubah
        $this->unitKerjas = UnitKerja::where('departemens_id', $value)->get();
    }
    
    /**
     * Logika dropdown berantai untuk Lokasi (Provinsi -> Kabupaten)
     * Dijalankan saat provinsi_id diubah.
     */
    public function updatedProvinsiId($value)
    {
        $this->kabupaten_id = null;
        $this->kecamatan_id = null;
        $this->kabupatens = Kabupaten::where('provinsi_id', $value)->get();
        $this->kecamatans = collect(); // Kosongkan kecamatan
    }
    
    /**
     * Logika dropdown berantai untuk Lokasi (Kabupaten -> Kecamatan)
     * Dijalankan saat kabupaten_id diubah.
     */
    public function updatedKabupatenId($value)
    {
        $this->kecamatan_id = null;
        $this->kecamatans = Kecamatan::where('kabupaten_id', $value)->get();
    }

    /**
     * Aturan validasi untuk form.
     */
    protected function rules()
    {
        return [
            'no_sap' => ['required', 'string', Rule::unique('karyawans', 'no_sap')->ignore($this->karyawan->id)],
            'nik_karyawan' => ['required', 'string', Rule::unique('karyawans', 'nik_karyawan')->ignore($this->karyawan->id)],
            'nama_karyawan' => 'required|string|max:255',
            'pekerjaan' => 'required|string',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'umur' => 'nullable|integer', // Mengubah aturan validasi untuk umur
            'jenis_kelamin' => 'required|string',
            'golongan_darah' => 'required|string',
            'agama' => 'required|string',
            'status_pernikahan' => 'required|string',
            'hubungan' => 'required|string',
            'kebangsaan' => 'required|string',
            'jabatan' => 'required|string',
            'eselon' => 'required|string',
            'pendidikan' => 'required|string',
            'departemens_id' => 'required|integer|exists:departemens,id',
            'unit_kerjas_id' => 'required|integer|exists:unit_kerjas,id',
            'provinsi_id' => 'required|integer|exists:provinsis,id',
            'kabupaten_id' => 'required|integer|exists:kabupatens,id',
            'kecamatan_id' => 'required|integer|exists:kecamatans,id',
            'email' => ['required', 'email', Rule::unique('karyawans', 'email')->ignore($this->karyawan->id)],
            'suami_istri' => 'nullable|string',
            'pekerjaan_suami_istri' => 'nullable|string',
            'alamat' => 'required|string',
            'no_hp' => 'required|string',
            'password' => 'nullable|min:6',
        ];
    }

    public function updateKaryawan()
    {
        // Validasi semua properti publik
        $validatedData = $this->validate();

        
        // Logging data yang divalidasi untuk debugging
        Log::info('Validated Data:', $validatedData);
        Log::info('Karyawan ID:', ['id' => $this->karyawan->id]);

        // Data yang akan di-update ke tabel karyawans
        // Hapus 'password' dari data yang akan di-update ke tabel karyawans
        $karyawanData = collect($validatedData)->except('password')->toArray();

        // Memperbarui data karyawan di tabel karyawans
        $this->karyawan->update($karyawanData);

        // Perbarui password jika diisi
        if ($this->password) {
            $this->karyawan->employeeLogin()->updateOrCreate(
                ['karyawan_id' => $this->karyawan->id],
                ['password' => Hash::make($this->password)]
            );
        }

        // Memancarkan event untuk menampilkan notifikasi
        $this->dispatch('karyawanUpdated');
    }

    // /**
    //  * Metode untuk memproses pembaruan data karyawan.
    //  */
    // public function updateKaryawan()
    // {
    //     // Validasi semua properti publik
    //     $validatedData = $this->validate();

    //     // Logging data yang divalidasi untuk debugging
    //     Log::info('Validated Data:', $validatedData);
    //     Log::info('Karyawan ID:', ['id' => $this->karyawan->id]);

    //     // Memperbarui data karyawan
    //     // Pastikan model Karyawan memiliki $fillable yang mencakup semua field di validatedData
    //     $this->karyawan->update($validatedData);

    //     // Perbarui password jika diisi
    //     if ($this->password) {
    //         $this->karyawan->employeeLogin()->updateOrCreate(
    //             ['karyawan_id' => $this->karyawan->id],
    //             ['password' => Hash::make($this->password)]
    //         );
    //     }

    //     // Memancarkan event untuk menampilkan notifikasi
    //     $this->dispatch('karyawanUpdated');
    // }

    /**
     * Render tampilan komponen.
     */
    public function render()
    {
        return view('livewire.edit-karyawan-form');
    }
}
