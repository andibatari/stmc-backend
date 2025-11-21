<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\AdminUser;
use App\Models\Dokter;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Hash;
use Livewire\WithPagination;

class TambahAdmin extends Component
{
    use WithPagination;

    public $adminId;
    public $no_sap, $nik, $nama_lengkap, $email, $password, $role = 'admin';
    public $editPasswordId = null;
    public $newPassword = '';
    public $isEditing = false;
    
    // Properti untuk Dokter
    public $listDokter = []; 
    public $selectedDokterId; // ID Dokter yang dipilih dari dropdown

    // --- PROPERTI BARU UNTUK PENCARIAN KARYAWAN (AUTOCOMPLETE) ---
    public $searchQuery = ''; // Field pencarian Karyawan (diisi No. SAP/Nama)
    public $searchedKaryawans = []; // Hasil daftar Karyawan yang ditemukan
    public $selectedKaryawanId = null; // ID Karyawan yang dikonfirmasi
    public $karyawanFound = false; // Status apakah Karyawan/Dokter sudah ditemukan dan diisi


    protected $rules = [
        'no_sap' => 'nullable|string|max:50|unique:admin_users,no_sap',
        'nama_lengkap' => 'required|string|max:255',
        'email' => 'required|email|unique:admin_users,email',
        'password' => 'required|min:6',
        'role' => 'required|string',
        'nik' => 'nullable|string',
    ];
    
    protected $passwordRules = [
        'newPassword' => 'required|min:6',
    ];

    public function mount()
    {
        $this->listDokter = Dokter::pluck('nama_lengkap', 'id');
    }

    public function getAdminUsersProperty()
    {
        // Gunakan with('dokter') untuk memuat data dokter yang berelasi
        // Query ini tetap mengembalikan koleksi model AdminUser, tapi dengan data dokter yang sudah dimuat
        return AdminUser::with('dokter')->latest()->paginate(10);
    }

    // Validasi email unik untuk mode UPDATE
    protected function getValidationRules()
    {
        $rules = $this->rules;
        $rules['email'] = [
            'required',
            'email',
            // Pastikan email unik, kecuali admin yang sedang di edit
            Rule::unique('admin_users', 'email')->ignore($this->adminId), 
        ];
        
        // Aturan validasi tambahan saat menyimpan
        if (!$this->isEditing && !$this->adminId) {
             $rules['password'] = 'required|min:6';
        }
        
        // Jika role dokter, No. SAP wajib diisi (manual)
        if ($this->role === 'dokter') {
             $rules['no_sap'] = 'required|string|max:50|unique:admin_users,no_sap,' . $this->adminId;
        }

        return $rules;
    }

    // --- LOGIKA PENCARIAN INSTAN KARYAWAN (AUTOCOMPLETE) ---
    public function updatedSearchQuery($value)
    {
        // Pastikan kolom NIK, Nama, Email direset jika pengguna mengubah query
        $this->reset(['nik', 'nama_lengkap', 'email', 'no_sap', 'selectedKaryawanId', 'karyawanFound']);
        
        // Hanya cari jika role adalah karyawan dan query minimal 2 karakter
        if ($this->role !== 'karyawan' || strlen($value) < 2) { 
            $this->searchedKaryawans = [];
            return;
        }

        // Cari berdasarkan No. SAP atau Nama Karyawan (maksimal 5 hasil)
        $this->searchedKaryawans = Karyawan::where('no_sap', 'like', '%'.$value.'%')
            ->orWhere('nama_karyawan', 'like', '%'.$value.'%')
            ->limit(5)
            ->get();
    }
    
    // --- PILIH HASIL PENCARIAN KARYAWAN (Mengisi semua field) ---
    public function selectKaryawan($id)
    {
        $karyawan = Karyawan::find($id);
        if ($karyawan) {
            // Isi form secara otomatis
            $this->no_sap = $karyawan->no_sap;
            $this->nama_lengkap = $karyawan->nama_karyawan;
            $this->nik = $karyawan->nik_karyawan;
            $this->email = $karyawan->email;
            
            // Simpan ID sumber
            $this->selectedKaryawanId = $id;
            $this->karyawanFound = true; 
            
            // Tutup hasil pencarian
            $this->searchQuery = $karyawan->no_sap . ' - ' . $karyawan->nama_karyawan;
            $this->searchedKaryawans = []; 
        }
    }
    
    // --- PILIH DOKTER DARI DROPDOWN ---
    public function updatedSelectedDokterId($value)
    {
        // Reset field yang terisi otomatis (kecuali password)
        $this->reset(['no_sap', 'nik', 'nama_lengkap', 'email', 'selectedKaryawanId', 'karyawanFound']);
        
        if (!empty($value)) {
            $dokter = Dokter::find($value);
            if ($dokter) {
                // Isi form
                $this->nama_lengkap = $dokter->nama_lengkap;
                $this->nik = $dokter->nik;
                $this->email = $dokter->email;
                // No. SAP DIBIARKAN KOSONG untuk diisi manual
                $this->karyawanFound = true; // Status ditemukan
            }
        } else {
            // Reset jika dropdown dikosongkan
            $this->reset(['nama_lengkap', 'nik', 'email', 'no_sap', 'karyawanFound']);
        }
    }

    public function updatedRole($value)
    {
        // Reset field yang dinamis saat role berubah
        $this->reset(['no_sap', 'nik', 'nama_lengkap', 'email', 'password', 'selectedDokterId', 'searchQuery', 'searchedKaryawans', 'selectedKaryawanId', 'karyawanFound']);
    }

    public function save()
    {
        // Aturan validasi dinamis: Password hanya required saat mode tambah
        $rules = $this->rules;
        if (!$this->isEditing) {
            $rules['password'] = 'required|min:6';
        }
        $this->validate($rules);
        
        $dokterId = null;
        $karyawanId = null;
        
        // Tentukan ID Sumber yang Benar
        if ($this->role === 'dokter') {
            $dokterId = $this->selectedDokterId;
            // Validasi tambahan jika role dokter, dokter_id harus terisi
            if (empty($dokterId)) {
                 session()->flash('error', 'Silakan pilih Dokter dari daftar.');
                 return;
            }
        } elseif ($this->role === 'karyawan') {
            $karyawanId = $this->selectedKaryawanId;
            // Validasi tambahan jika role karyawan, karyawan_id harus terisi dari lookup
            if (empty($karyawanId)) {
                session()->flash('error', 'Silakan cari dan pilih Karyawan yang valid.');
                return;
            }
        }

        AdminUser::create([
            'no_sap' => $this->no_sap,
            'nama_lengkap' => $this->nama_lengkap,
            'nik' => $this->nik, 
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'dokter_id' => $dokterId, 
            'karyawan_id' => $karyawanId, 
        ]);

        session()->flash('success', 'Admin baru berhasil ditambahkan.');
        $this->reset(['no_sap', 'nama_lengkap', 'nik', 'email', 'password', 'selectedDokterId', 'selectedKaryawanId', 'searchQuery', 'karyawanFound']);
    }

    public function edit($id)
    {
        $admin = AdminUser::findOrFail($id);
        $this->isEditing = true;
        $this->adminId = $admin->id;
        $this->no_sap = $admin->no_sap;
        $this->nama_lengkap = $admin->nama_lengkap;
        $this->nik = $admin->nik;
        $this->email = $admin->email;
        $this->role = $admin->role;

        // Tentukan ID sumber saat edit
        $this->selectedDokterId = $admin->dokter_id;
        $this->selectedKaryawanId = $admin->karyawan_id;
        $this->karyawanFound = ($admin->dokter_id || $admin->karyawan_id);
    }

    public function update()
    {
        $validatedData = $this->validate([
            'no_sap' => 'nullable|string|max:50|unique:admin_users,no_sap,' . $this->adminId,
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'nullable|string',
            'email' => 'required|email|unique:admin_users,email,' . $this->adminId,
            'role' => 'required|string',
        ]);

        $admin = AdminUser::findOrFail($this->adminId);
        $admin->update($validatedData);

        session()->flash('success', 'Data admin berhasil diperbarui.');
        $this->reset(['adminId', 'no_sap', 'nik','nama_lengkap', 'email', 'role']);
        $this->isEditing = false;
    }

    public function cancelEdit()
    {
        $this->reset(['adminId', 'no_sap', 'nama_lengkap', 'nik','email', 'role']);
        $this->isEditing = false;
    }

    public function editPassword($id)
    {
        $this->editPasswordId = $id;
        $this->newPassword = '';
    }

    public function updatePassword()
    {
        $this->validate($this->passwordRules);
        $admin = AdminUser::find($this->editPasswordId);
        $admin->update([
            'password' => Hash::make($this->newPassword),
        ]);
        session()->flash('success', 'Password admin berhasil diperbarui.');
        $this->reset(['editPasswordId', 'newPassword']);
    }

    public function delete($id)
    {
        AdminUser::find($id)->delete();
        session()->flash('success', 'Admin berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.admin.tambah-admin')
            ->layout('layouts.app');
    }
}