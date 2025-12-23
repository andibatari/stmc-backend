<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Departemen;
use App\Models\UnitKerja;
use App\Models\Provinsi;
use App\Models\Karyawan;
use App\Models\PesertaMcu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\PesertaMcuLogin;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AddKeluargaKaryawan extends Component
{
    // Properti data (Dideklarasikan di awal)
    public ?Karyawan $karyawan = null; 
    public $karyawan_id;
    public $tipe_anggota = '';
    public $no_sap;
    public $nik_pasien;
    public $nama_lengkap;
    public $jenis_kelamin;
    public $tempat_lahir;
    public $tanggal_lahir;
    public $umur;
    public $golongan_darah;
    public $pendidikan;
    public $pekerjaan;
    public $perusahaan_asal;
    public $agama;
    public $no_hp;
    public $email;
    public $alamat;
    public $password;
    public $password_confirmation;
    public $tinggi_badan;
    public $berat_badan;

    // Properti Dropdown (Dideklarasikan di awal)
    public $departemens_id;
    public $unit_kerjas_id;
    public $provinsi_id;
    public $nama_kabupaten;
    public $nama_kecamatan;

    // Koleksi Dropdown
    public $departemens = [];
    public $unitKerjas = [];
    public $provinsis = [];
    // public $kabupatens = [];
    // public $kecamatans = [];

    public bool $isNonKaryawan = false;

    protected $listeners = ['updateUmur'];

    public function mount($karyawan_id = null)
    {
        $this->karyawan_id = $karyawan_id;
        
        if ($this->karyawan_id) {
            $this->karyawan = Karyawan::find($this->karyawan_id);
        }
        
        $this->isNonKaryawan = is_null($this->karyawan);
        
        $this->departemens = Departemen::orderBy('nama_departemen')->get();
        $this->provinsis = Provinsi::orderBy('nama_provinsi')->get();

        if ($this->isNonKaryawan) {
            $this->tipe_anggota = 'Non-Karyawan';
        } else {
            $this->tipe_anggota = 'Istri'; 
        }

        $this->updatedDepartemensId($this->departemens_id);
    }

    protected function rules()
    {
        $rules = [
            'tipe_anggota' => 'required|string',
            'no_sap' => 'nullable|string|max:255',
            // FIX: NIK unik, tapi hanya di tabel login (peserta_mcu_logins)
            'nik_pasien' => 'required|string|max:255|unique:peserta_mcu_logins,nik_pasien', 
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|string',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'umur' => 'nullable|integer',
            'golongan_darah' => 'nullable|string',
            'pendidikan' => 'nullable|string',
            'pekerjaan' => 'nullable|string|max:255',
            'perusahaan_asal' => $this->tipe_anggota == 'Non-Karyawan' ? 'required|string|max:255' : 'nullable|string|max:255',
            'agama' => 'nullable|string',
            'no_hp' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string',
            'provinsi_id' => 'nullable|exists:provinsis,id',
            'nama_kabupaten' => 'nullable|string|max:255',
            'nama_kecamatan' => 'nullable|string|max:255',
            'tinggi_badan' => 'nullable|numeric|min:1',
            'berat_badan' => 'nullable|numeric|min:1',
        ];

        if ($this->tipe_anggota == 'Non-Karyawan' || in_array($this->tipe_anggota, ['Istri', 'Suami'])) {
            $rules['password'] = 'required|string|min:6|confirmed';
            $rules['password_confirmation'] = 'required|string|min:6';
            $rules['departemens_id'] = 'nullable';
            $rules['unit_kerjas_id'] = 'nullable';
        }

        return $rules;
    }
    
    public function updateUmur($age)
    {
        $this->umur = $age;
    }

    public function updatedTipeAnggota($value)
    {
        // ...
    }
    
    public function updatedDepartemensId($value)
    {
        $this->unit_kerjas_id = null;
        $this->unitKerjas = $value ? UnitKerja::where('departemens_id', $value)->orderBy('nama_unit_kerja')->get() : collect();
    }

    public function updatedProvinsiId($value)
    {
        $this->nama_kabupaten = null; // Reset input teks Kabupaten
        $this->nama_kecamatan = null; // Reset input teks Kecamatan
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();
            
            // -----------------------------------------------------
            // FIX: Gunakan array properti yang hanya berisi data model
            // -----------------------------------------------------
            $data = $this->all(); // Mengambil semua properti publik

            // $data = $this->only([
            //     'no_sap', 'nik_pasien', 'nama_lengkap', 'jenis_kelamin', 'tempat_lahir', 
            //     'tanggal_lahir', 'umur', 'golongan_darah', 'pendidikan', 'pekerjaan', 
            //     'perusahaan_asal', 'agama', 'no_hp', 'email', 'alamat', 'provinsi_id', 
            //     'nama_kabupaten', 'nama_kecamatan', 'tinggi_badan', 'berat_badan', 
            //     'departemens_id', 'unit_kerjas_id', 'tipe_anggota', 'karyawan_id'
            // ]);
            
            // Logika Tipe Anggota
            if ($this->tipe_anggota == 'Non-Karyawan') {
                $data['karyawan_id'] = null;
                $data['departemens_id'] = null;
                $data['unit_kerjas_id'] = null;
            } else {
                $data['karyawan_id'] = $this->karyawan_id;
            }

            // Simpan data ke tabel peserta_mcus
            $pesertaMcu = PesertaMcu::create($data); 
            
            // Simpan data login
            if ($this->tipe_anggota == 'Non-Karyawan' || in_array($this->tipe_anggota, ['Istri', 'Suami'])) {
                PesertaMcuLogin::create([
                    'peserta_mcu_id' => $pesertaMcu->id, 
                    'nik_pasien' => $this->nik_pasien,
                    'password' => Hash::make($this->password),
                ]);
            }
            
            DB::commit();
            $this->dispatch($this->tipe_anggota == 'Non-Karyawan' ? 'pesertaSaved' : 'karyawanSaved');

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::warning('VALIDATION FAILED (Add Keluarga): ' . json_encode($e->errors()));
            $this->dispatch('show-error-popup', ['message' => 'Gagal menyimpan data karena kesalahan validasi.']);
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            // Logging error EKSPLISIT: Ini menangkap Mass Assignment atau Query Error
            Log::error('DB TRANSACTION FAILED: ' . $e->getMessage() . ' | Data Attempt: ' . json_encode($data));
            $this->dispatch('show-error-popup', ['message' => 'Terjadi kesalahan serius saat menyimpan data. Pastikan semua kolom terdaftar di $fillable model PesertaMcu.']);
        }
    }

    public function render()
    {
        return view('livewire.add-keluarga-karyawan');
    }
}