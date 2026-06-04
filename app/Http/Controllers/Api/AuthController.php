<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\EmployeeLogin;
use App\Models\PesertaMcuLogin;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    /**
     * LOGIN API (Employee & Peserta MCU)
     * POST /api/login
     */
    public function login(Request $request)
    {
        try {
            $data = $request->validate([
                'identifier' => 'required|string',
                'password'   => 'required|string',
            ]);

            $loginUser = $this->findAndAuthenticateApiUser(
                $data['identifier'],
                $data['password']
            );

            if (!$loginUser) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Identitas atau password salah'
                ], 401);
            }

            // 🔑 BUAT TOKEN SANCTUM
            $token = $loginUser
                ->createToken('api-token')
                ->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'token' => $token,
                'user_profile' => $this->getProfileData($loginUser)
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error saat login',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * LOGOUT API
     * POST /api/logout
     */
    public function logout(Request $request)
    {
        $user = $request->user('sanctum'); // Sanctum user (login table)

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil logout'
        ]);
    }

    /**
     * CHANGE PASSWORD API
     * POST /api/change-password
     */
    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6|confirmed',
            ]);

            $user = $request->user('sanctum'); // EmployeeLogin / PesertaMcuLogin

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Password lama salah'
                ], 401);
            }

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Password berhasil diubah'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // ===============================
    // HELPER
    // ===============================

    protected function findAndAuthenticateApiUser(string $input, string $password)
    {
        $loginUser = null;

        // ===== EMPLOYEE =====
        if (is_numeric($input)) {
            $field = strlen($input) <= 6 ? 'no_sap' : 'nik_karyawan';
            $loginUser = EmployeeLogin::where($field, $input)->first();
        } elseif (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $loginUser = EmployeeLogin::whereHas('karyawan', function ($q) use ($input) {
                $q->where('email', $input);
            })->first();
        }

        // ===== PESERTA MCU =====
        if (!$loginUser) {
            if (is_numeric($input)) {
                $loginUser = PesertaMcuLogin::where('nik_pasien', $input)->first();
            } elseif (filter_var($input, FILTER_VALIDATE_EMAIL)) {
                $loginUser = PesertaMcuLogin::whereHas('pasien', function ($q) use ($input) {
                    $q->where('email', $input);
                })->first();
            }
        }

        return ($loginUser && Hash::check($password, $loginUser->password))
            ? $loginUser
            : null;
    }

    protected function getProfileData($loginUser)
    {
        // ======================
        // KARYAWAN
        // ======================
        if ($loginUser instanceof EmployeeLogin) {

            $karyawan = $loginUser->karyawan()
                ->with([
                    'departemen',
                    'unitKerja',
                    'provinsi'
                ])->first();

            return $karyawan ? [
                'type' => 'Karyawan',
                'id' => $karyawan->id,
                'nama' => $karyawan->nama_karyawan,
                'no_sap' => $karyawan->no_sap,
                'nik' => $karyawan->nik_karyawan,
                'departemen' => $karyawan->departemen->nama_departemen ?? null,
                'unit_kerja' => $karyawan->unitKerja->nama_unit_kerja ?? null,
                'email' => $karyawan->email,
                'no_hp' => $karyawan->no_hp,
                'foto_path' => $karyawan->foto_profil,
                'foto' => !empty($karyawan->foto_profil)
                    ? Storage::disk('public')->url($karyawan->foto_profil)
                    : null,
                'jabatan' => $karyawan->jabatan,
                'tanggal_lahir' => $karyawan->tanggal_lahir,
                'umur' => $karyawan->umur,
                'jenis_kelamin' => $karyawan->jenis_kelamin,
                'agama' => $karyawan->agama,
                'alamat' => $karyawan->alamat,
                'provinsi' => $karyawan->provinsi->nama_provinsi ?? null,
                'kabupaten' => $karyawan->nama_kabupaten,
                'kecamatan' => $karyawan->nama_kecamatan,
                'tinggi_badan' => $karyawan->tinggi_badan,
                'berat_badan' => $karyawan->berat_badan,
                'golongan_darah' => $karyawan->golongan_darah,
                'is_employee' => true
            ] : null ;
        }

        // ======================
        // PESERTA MCU
        // ======================
        if ($loginUser instanceof PesertaMcuLogin) {
            $pasien = $loginUser->pasien;
            if (!$pasien) {
                return [
                    'type' => 'Pasien',
                    'message' => 'Data profil pasien tidak ditemukan di database',
                    'is_employee' => false
                ];
            }

            return [
                'type' => 'Pasien',
                'id' => $pasien->id,
                'nama' => $pasien->nama_lengkap,
                'nik' => $pasien->nik_pasien,
                'email' => $pasien->email,
                'no_hp' => $pasien->no_hp,
                'foto' => !empty($pasien->foto_profil)
                    ? Storage::disk('public')->url($pasien->foto_profil)
                    : null,
                'tanggal_lahir' => $pasien->tanggal_lahir,
                'umur' => $pasien->umur,
                'jenis_kelamin' => $pasien->jenis_kelamin,
                'agama' => $pasien->agama,
                'alamat' => $pasien->alamat,
                'provinsi' => $pasien->provinsi->nama_provinsi ?? null,
                'kabupaten' => $pasien->nama_kabupaten,
                'kecamatan' => $pasien->nama_kecamatan,
                'tinggi_badan' => $pasien->tinggi_badan,
                'berat_badan' => $pasien->berat_badan,
                'golongan_darah' => $pasien->golongan_darah,
                'pendidikan' => $pasien->pendidikan,
                'pekerjaan' => $pasien->pekerjaan,
                'perusahaan' => $pasien->perusahaan_asal,
                'is_employee' => false
            ];
        }

        return null;
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user('sanctum');

            if ($user instanceof EmployeeLogin) {
                $profile = $user->karyawan;
            } elseif ($user instanceof PesertaMcuLogin) {
                $profile = $user->pasien;
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak dikenali'
                ], 403);
            }

            // 1. KEMBALIKAN VALIDASI MENJADI NULLABLE AGAR BISA UPDATE TEKS JUGA
            $request->validate([
                'nik'           => 'nullable|string',
                'nama'          => 'nullable|string',
                'email'         => 'nullable|email',
                'no_hp'         => 'nullable|string',
                'tinggi_badan'  => 'nullable|numeric',
                'berat_badan'   => 'nullable|numeric',
                'alamat'        => 'nullable|string',
                'provinsi'      => 'nullable|string',
                'kabupaten'     => 'nullable|string',
                'kecamatan'     => 'nullable|string',
                'foto_profil'   => 'nullable|image|mimes:jpg,jpeg,png,webp,heic|max:5120',
            ]);

            // 2. PROSES UPLOAD FOTO PROFIL
            if ($request->hasFile('foto_profil')) {
                $file = $request->file('foto_profil');
                
                if ($file->isValid()) {
                    // Hapus foto lama di GCS jika ada (Diberi try-catch agar tidak error jika file lama hilang)
                    if (!empty($profile->foto_profil)) {
                        try {
                            Storage::disk('public')->delete($profile->foto_profil);
                        } catch (\Exception $e) {}
                    }

                    // Simpan ke Google Cloud Storage (GCS)
                    $path = $file->store('profile_photos', 'public');

                    // Jika GCS gagal menyimpan secara diam-diam
                    if (!$path) {
                        return response()->json([
                            'status' => 'error', 
                            'message' => 'Gagal menyimpan foto ke Cloud Storage.'
                        ], 500);
                    }

                    // Memasukkan nama path foto ke variabel model
                    $profile->foto_profil = $path;
                } else {
                    return response()->json([
                        'status' => 'error', 
                        'message' => 'File gambar corrupt saat diterima server.'
                    ], 422);
                }
            }

            // 3. PROSES UPDATE DATA TEKS
            $updateData = [
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
                'tinggi_badan' => $request->tinggi_badan,
                'berat_badan' => $request->berat_badan,
                'nama_kabupaten' => $request->kabupaten,
                'nama_kecamatan' => $request->kecamatan,
            ];

            if ($user instanceof EmployeeLogin) {
                $updateData['nik_karyawan'] = $request->nik;
                $updateData['nama_karyawan'] = $request->nama;
            } else {
                $updateData['nik_pasien'] = $request->nik;
                $updateData['nama_lengkap'] = $request->nama;
            }

            // Saring data null agar tidak menimpa data lama dengan kosong
            $profile->fill(array_filter(
                $updateData,
                fn($value) => !is_null($value)
            ));

            // 4. PAKSA SIMPAN KE DATABASE SECARA MUTLAK
            $profile->save();

            // Refresh user dari database untuk memastikan perubahan terbaca
            $user->refresh();

            // Ambil struktur profil terbaru
            $newProfileData = $this->getProfileData($user);

            // 🚨 PENJAGA TERAKHIR: Jika foto sukses masuk tapi gagal di generate URL-nya
            if ($request->hasFile('foto_profil') && empty($newProfileData['foto'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Database tidak tersimpan atau GCS belum terkonfigurasi dengan benar di sistem Laravel Anda.',
                    'debug_db_path' => $profile->foto_profil ?? 'Kosong'
                ], 500);
            }

            // 5. BERHASIL! KEMBALIKAN DATA KE FLUTTER
            return response()->json([
                'status' => 'success',
                'message' => 'Profil berhasil diperbarui',
                'user_profile' => $newProfileData
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Exception: ' . $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ], 500);
        }
    }
}