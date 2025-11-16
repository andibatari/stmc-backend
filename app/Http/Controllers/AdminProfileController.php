<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\AdminUser; // Asumsikan nama model untuk Admin Anda adalah AdminUser

class AdminProfileController extends Controller
{
    /**
     * Menampilkan form untuk mengedit profil Admin yang sedang login.
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        // Ambil data admin yang sedang login
        $admin = Auth::guard('admin_users')->user();
        
        // Tampilkan view edit profil dan kirim data admin
        return view('admin.profile.edit', compact('admin'));
    }

    /**
     * Menyimpan pembaruan data profil Admin (termasuk foto profil, jika diunggah).
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $admin = Auth::guard('admin_users')->user();
        
        // 1. Validasi data
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                // Pastikan email unik, kecuali email admin saat ini
                Rule::unique('admin_users', 'email')->ignore($admin->id),
            ],
            'password' => 'nullable|string|min:6|confirmed',
            // KRITIS: Validasi untuk foto_profil (nullable karena tidak wajib diubah)
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Maks 2MB
        ]);
        
        // 2. Inisialisasi data untuk update
        $dataToUpdate = [
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
        ];

        // 3. Tangani Upload Foto Profil
        if ($request->hasFile('foto_profil')) {
            $file = $request->file('foto_profil');
            
            // Hapus foto lama JIKA path-nya ada di database DAN file-nya ada di storage
            if ($admin->foto_profil && Storage::exists($admin->foto_profil)) {
                Storage::delete($admin->foto_profil);
            }
            
            // Simpan file di folder 'public/profile_photos'
            // Metode store() menyimpan file ke disk default (biasanya 'local') di bawah 'storage/app/public/'
            $path = $file->store('public/profile_photos'); 
            
            // Simpan path relatif ke database
            $dataToUpdate['foto_profil'] = $path;
        }

        // 4. Tangani Update Password
        if ($request->filled('password')) {
            // Gunakan Hash::make() atau bcrypt()
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        // 5. Update Model
        $admin->update($dataToUpdate);

        return redirect()->route('admin.profile.edit')
                         ->with('success', 'Profil berhasil diperbarui!');
    }
}
