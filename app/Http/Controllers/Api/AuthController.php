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

            // optional: hapus semua token
            // $user->tokens()->delete();

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
                'foto' => $karyawan->foto_profil 
                    ? Storage::disk('s3')->url($profile->foto_profil) 
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
            $pasien = $loginUser->pasien()
                ->with(['provinsi'])
                ->first();

            if (!$pasien) {
                return null;
            }

            return $pasien ? [
                'type' => 'Pasien',
                'id' => $pasien->id,
                'nama' => $pasien->nama_lengkap,
                'nik' => $pasien->nik_pasien,
                'email' => $pasien->email,
                'no_hp' => $pasien->no_hp,
                'foto' => $pasien->foto_profil
                    ? Storage::disk('s3')->url($pasien->foto_profil)
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
            ] : null;
        }

        return null;
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user('sanctum');
        // Memastikan model profil yang tepat diambil
        $profile = ($user instanceof \App\Models\EmployeeLogin) ? $user->karyawan : $user->pasien;

        // 1. Perluas Validasi sesuai field yang dikirim dari Flutter
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
            'foto_profil'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. Handle Upload Foto
        if ($request->hasFile('foto_profil')) {
            // 1. Hapus foto lama dari S3 jika ada
            if ($profile->foto_profil) {
                Storage::disk('s3')->delete($profile->foto_profil);
            }
            
            // 2. Simpan ke S3 (DigitalOcean Spaces)
            // Pastikan folder 'profile_photos' ada di bucket Anda
            $path = $request->file('foto_profil')->store('profile_photos', 's3');
            
            // 3. Simpan path relatif ke database
            $profile->foto_profil = $path;
        }

        // 3. Mapping data dari Flutter ke kolom Database
        // Sesuaikan kunci di request (kiri) dengan kunci yang dikirim Flutter
        $updateData = [
            'no_hp'          => $request->no_hp,
            'alamat'         => $request->alamat,
            'tinggi_badan'   => $request->tinggi_badan,
            'berat_badan'    => $request->berat_badan,
            'nama_kabupaten' => $request->kabupaten, // Mapping ke kolom database Anda
            'nama_kecamatan' => $request->kecamatan, // Mapping ke kolom database Anda
        ];

        // Khusus Karyawan vs Pasien jika nama kolomnya berbeda
        if ($user instanceof \App\Models\EmployeeLogin) {
            $updateData['nik_karyawan'] = $request->nik;
            $updateData['nama_karyawan'] = $request->nama;
            $updateData['email'] = $request->email;
        } else {
            $updateData['nik_pasien']   = $request->nik;
            $updateData['nama_lengkap'] = $request->nama;
            $updateData['email']        = $request->email;
        }

        $profile->update(array_filter($updateData)); // array_filter agar tidak menimpa data dengan null jika tidak dikirim
        $profile->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui',
            'user_profile' => $this->getProfileData($user) // Mengirim data terbaru
        ]);
    }
}
