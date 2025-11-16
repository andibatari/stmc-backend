<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\AdminUser;
use App\Models\Dokter;
use Illuminate\Support\Facades\Hash;
use Livewire\WithPagination;

class TambahAdmin extends Component
{
    use WithPagination;

    public $adminId;
    public $no_sap,$nik, $nama_lengkap, $email, $password, $role = 'admin';
    public $editPasswordId = null;
    public $newPassword = '';
    public $isEditing = false;
    public $listDokter = [];
    public $selectedDokterId; // Properti baru untuk menyimpan ID dokter yang dipilih


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

    public function updatedRole($value)
    {
        // Jika role berubah ke 'admin', reset semua field kecuali selectedDokterId
        if ($value === 'admin') {
            $this->reset(['no_sap', 'nama_lengkap', 'nik', 'email', 'password']);
        } else {
            // Jika role berubah ke 'dokter', reset No. SAP dan password
            // Biarkan field nama_lengkap, nik, email diisi oleh dropdown
            $this->reset(['no_sap', 'password']);
        }

        // Pastikan selectedDokterId direset agar dropdown 'Pilih Dokter' kembali kosong
        $this->reset('selectedDokterId');
    }

    public function updatedSelectedDokterId($value)
    {
        if (!empty($value)) {
            $dokter = Dokter::find($value);
            if ($dokter) {
                $this->nama_lengkap = $dokter->nama_lengkap;
                $this->nik = $dokter->nik;
                $this->email = $dokter->email;
            }
        } else {
            // Reset properti jika dropdown dikosongkan
            $this->reset(['nama_lengkap', 'nik', 'email']);
        }
    }

    public function save()
    {
        $this->validate();

        AdminUser::create([
            'no_sap' => $this->no_sap,
            'nama_lengkap' => $this->nama_lengkap,
            'nik' => $this->nik, // Pastikan kolom ini ada di migrasi admin_users
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'dokter_id' => $this->selectedDokterId, // Tambahkan baris ini
        ]);

         session()->flash('success', 'Admin baru berhasil ditambahkan.');
    $this->reset(['no_sap', 'nama_lengkap', 'nik', 'email', 'password', 'selectedDokterId']);
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
        // Password tidak di-load saat edit
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