<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Departemen;
use App\Models\UnitKerja;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Karyawan;
use App\Models\PesertaMcu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\PesertaMcuLogin;
use Illuminate\Validation\ValidationException;

class AddKeluargaKaryawan extends Component
{
    // Properti untuk data
    public ?Karyawan $karyawan = null; // Objek Karyawan jika menambahkan untuk karyawan tertentu
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
    public $alamat; // Diperbarui menjadi lebih spesifik
    public $password;
    public $password_confirmation;
    // Properti baru untuk tinggi dan berat badan
    public $tinggi_badan;
    public $berat_badan;

    // Properti untuk dropdown
    public $departemens_id;
    public $unit_kerjas_id;
    public $provinsi_id;
    public $kabupaten_id;
    public $kecamatan_id;

    // Koleksi untuk data dropdown
    public $departemens = [];
    public $unitKerjas = [];
    public $provinsis = [];
    public $kabupatens = [];
    public $kecamatans = [];

    public bool $isNonKaryawan = false;

    // Listener untuk update properti dari JavaScript
    protected $listeners = ['updateUmur']; // Perbaikan: tidak perlu '=>'

    public function mount($karyawan_id = null)
    {
        $this->karyawan_id = $karyawan_id;
        
        // Perbaikan: Hanya cari karyawan jika ID diberikan
        if ($this->karyawan_id) {
            $this->karyawan = Karyawan::find($this->karyawan_id);
        }
        
        $this->isNonKaryawan = is_null($this->karyawan); // Perbaikan: Gunakan properti karyawan
        
        $this->departemens = Departemen::orderBy('nama_departemen')->get();
        $this->provinsis = Provinsi::orderBy('nama_provinsi')->get();

        // Perbaikan: Gunakan isNonKaryawan untuk menentukan tipe
        if ($this->isNonKaryawan) {
            $this->tipe_anggota = 'Non-Karyawan'; // Atur default untuk non-karyawan
        } else {
            $this->tipe_anggota = 'Istri'; // Atur default untuk keluarga
        }
    }

    // Metode untuk validasi
    protected function rules()
    {
        $rules = [
            'tipe_anggota' => 'required|string',
            'no_sap' => 'nullable|string|max:255',
            'nik_pasien' => 'required|string|max:255|unique:peserta_mcu_logins,nik_pasien',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|string',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'umur' => 'nullable|integer',
            'golongan_darah' => 'nullable|string',
            'pendidikan' => 'nullable|string',
            'pekerjaan' => 'nullable|string|max:255',
            'perusahaan_asal' => 'nullable|string|max:255',
            'agama' => 'nullable|string',
            'no_hp' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string',
            'provinsi_id' => 'nullable|exists:provinsis,id',
            'kabupaten_id' => 'nullable|exists:kabupatens,id',
            'kecamatan_id' => 'nullable|exists:kecamatans,id',
            'tinggi_badan' => 'nullable|numeric|min:1',
            'berat_badan' => 'nullable|numeric|min:1',
        ];

        // Aturan validasi password & organisasi hanya jika diperlukan
        if ($this->isNonKaryawan || in_array($this->tipe_anggota, ['Istri', 'Suami'])) {
            $rules['password'] = 'required|string|min:6|confirmed';
            $rules['departemens_id'] = 'nullable|exists:departemens,id';
            $rules['unit_kerjas_id'] = 'nullable|exists:unit_kerjas,id';
        }

        return $rules;
    }
    
    // Metode untuk mengupdate properti umur dari JavaScript
    public function updateUmur($age)
    {
        $this->umur = $age;
    }

    public function updatedTipeAnggota($value)
    {
        // Logika ini sudah benar, tidak perlu diubah
    }
    
    // Metode yang akan dijalankan setiap kali properti $departemens_id diubah
    public function updatedDepartemensId($value)
    {
        $this->unit_kerjas_id = null;
        $this->unitKerjas = $value ? UnitKerja::where('departemens_id', $value)->orderBy('nama_unit_kerja')->get() : collect();
    }

    // Metode yang akan dijalankan setiap kali properti $provinsi_id diubah
    public function updatedProvinsiId($value)
    {
        $this->kabupaten_id = null;
        $this->kecamatan_id = null;
        $this->kabupatens = $value ? Kabupaten::where('provinsi_id', $value)->orderBy('nama_kabupaten')->get() : collect();
        $this->kecamatans = collect();
    }

    // Metode yang akan dijalankan setiap kali properti $kabupaten_id diubah
    public function updatedKabupatenId($value)
    {
        $this->kecamatan_id = null;
        $this->kecamatans = $value ? Kecamatan::where('kabupaten_id', $value)->orderBy('nama_kecamatan')->get() : collect();
    }

    public function save()
    {
        // Validasi
        $this->validate();

        try {
            DB::beginTransaction();

            $data = [
                'no_sap' => $this->no_sap,
                'nik_pasien' => $this->nik_pasien,
                'nama_lengkap' => $this->nama_lengkap,
                'jenis_kelamin' => $this->jenis_kelamin,
                'tempat_lahir' => $this->tempat_lahir,
                'tanggal_lahir' => $this->tanggal_lahir,
                'umur' => $this->umur,
                'golongan_darah' => $this->golongan_darah,
                'pendidikan' => $this->pendidikan,
                'pekerjaan' => $this->pekerjaan,
                'perusahaan_asal' => $this->perusahaan_asal,
                'agama' => $this->agama,
                'no_hp' => $this->no_hp,
                'email' => $this->email,
                'alamat' => $this->alamat,
                'provinsi_id' => $this->provinsi_id,
                'kabupaten_id' => $this->kabupaten_id,
                'kecamatan_id' => $this->kecamatan_id,
                'departemens_id' => $this->departemens_id,
                'unit_kerjas_id' => $this->unit_kerjas_id,
                'tinggi_badan' => $this->tinggi_badan,
                'berat_badan' => $this->berat_badan,
            ];

            // Tentukan status pasien: non-karyawan atau anggota keluarga
            if ($this->isNonKaryawan) {
                // Pasien non-karyawan. Tetapkan relasi ke null.
                $data['karyawan_id'] = null;
                $data['tipe_anggota'] = 'Non-Karyawan';
            } else {
                // Anggota keluarga dari karyawan.
                $karyawan = Karyawan::findOrFail($this->karyawan_id);
                $data['karyawan_id'] = $karyawan->id;
                $data['tipe_anggota'] = $this->tipe_anggota;
            }

            // Simpan data ke tabel peserta_mcus
            $pesertaMcu = PesertaMcu::create($data); 
            
            // Simpan data login hanya jika pasien adalah non-karyawan atau anggota keluarga
            if ($this->isNonKaryawan || in_array($this->tipe_anggota, ['Istri', 'Suami'])) {
                PesertaMcuLogin::create([
                    'peserta_mcu_id' => $pesertaMcu->id, 
                    'nik_pasien' => $this->nik_pasien,
                    'password' => Hash::make($this->password),
                ]);
            }
            
            // Arahkan kembali pengguna
            DB::commit();
            $message = $this->isNonKaryawan ? 'Data pasien non-karyawan berhasil disimpan.' : 'Anggota keluarga berhasil disimpan.';
            
            if ($this->isNonKaryawan) {
                $this->dispatch('pesertaSaved');
            } else {
                $this->dispatch('karyawanSaved');
            }

        } catch (ValidationException $e) {
            DB::rollBack();
            $this->dispatch('show-error-popup', ['message' => 'Gagal menyimpan data karena kesalahan validasi.']);
            throw $e; // Re-throw the exception to show Livewire validation errors
        } catch (\Exception $e) {
            DB::rollBack();
            report($e); // Log the exception
            $this->dispatch('show-error-popup', ['message' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.']);
        }
    }

    public function render()
    {
        return view('livewire.add-keluarga-karyawan');
    }
}
