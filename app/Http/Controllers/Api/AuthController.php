<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; // Wajib
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\EmployeeLogin;
use App\Models\PesertaMcuLogin;
use App\Models\Karyawan;
use App\Models\PesertaMcu;

class AuthController extends Controller // Nama file harus AuthController.php di folder Api
{
    /**
     * Logika Login API (Aplikasi Flutter) - Multi-Identity.
     * Endpoint: POST /api/login
     */
    public function login(Request $request) // Menggunakan nama 'login' untuk rute /api/login
    {
        // Validasi Input Dasar API (menggunakan identifier tunggal)
        $identifier = $request->validate([
            'identifier' => 'required|string', // no_sap, nik, atau email
            'password' => 'required|string',
        ]);
        
        $input = $identifier['identifier'];
        $password = $identifier['password'];

        // 1. Identifikasi Pengguna & Coba Otentikasi API
        $loginUser = $this->findAndAuthenticateApiUser($input, $password);

        if ($loginUser) {
            
            // Buat token Sanctum pada model login yang berhasil
            $token = $loginUser->createToken('flutter-auth-token')->plainTextToken;
            
            // Ambil data Profil (Karyawan/Pasien)
            $profile = $this->getProfileData($loginUser);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Login API berhasil.',
                'token' => $token,
                'user_profile' => $profile
            ], 200);
        }

        // Jika otentikasi API gagal
        return response()->json([
            'status' => 'error', 
            'message' => 'Identitas (SAP/NIK/Email) atau password salah.'
        ], 401);
    }
    
    /**
     * Logika Logout API (Menggunakan Token Sanctum).
     * Endpoint: POST /api/logout
     */
    public function logout(Request $request)
    {
        // Hapus token Sanctum yang sedang digunakan
        $request->user()->currentAccessToken()->delete(); 
        
        return response()->json([
            'status' => 'success', 
            'message' => 'Berhasil logout dari API.'
        ], 200);
    }

    public function changePassword(Request $request)
    {
        // 1. Validasi Input
        try {
            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6|confirmed', // 'confirmed' mencari field 'new_password_confirmation'
            ], [
                'new_password.min' => 'Kata sandi baru minimal 6 karakter.',
                'new_password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        }

        $user = $request->user(); // Mendapatkan user yang sedang login via Sanctum

        // 2. Verifikasi Password Lama
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kata sandi lama salah.',
            ], 401);
        }

        // 3. Update Password
        try {
            $user->fill([
                'password' => Hash::make($request->new_password)
            ])->save();

            // Opsional: Batalkan semua token sesi lama setelah password diubah
            // $user->tokens()->where('name', 'app-token')->delete(); 

            return response()->json([
                'success' => true,
                'message' => 'Kata sandi berhasil diubah.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah kata sandi di database.'
            ], 500);
        }
    }
    
    // --- Metode Pembantu (Harus disertakan) ---
    
    protected function findAndAuthenticateApiUser(string $input, string $password)
    {
        // ... (Logika Otentikasi Manual yang telah kita buat)
        // KARENA INI HANYA UNTUK API, kita bisa menghilangkan logika Web dan fokus pada pencarian model login.

        $loginUser = null;
        
        // --- 1. Coba Karyawan (SAP/NIK/Email) ---
        if (is_numeric($input)) {
            $loginField = (strlen($input) <= 6) ? 'no_sap' : 'nik_karyawan'; 
            $loginUser = EmployeeLogin::where($loginField, $input)->first();
        } elseif (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $loginUser = EmployeeLogin::whereHas('karyawan', function($q) use ($input) {
                $q->where('email', $input);
            })->first();
        }
        
        // --- 2. Coba Pasien Non-Karyawan (NIK/Email) ---
        if (!$loginUser) {
            if (is_numeric($input)) {
                $loginUser = PesertaMcuLogin::where('nik_pasien', $input)->first();
            } elseif (filter_var($input, FILTER_VALIDATE_EMAIL)) {
                $loginUser = PesertaMcuLogin::whereHas('pasien', function($q) use ($input) {
                    $q->where('email', $input);
                })->first();
            }
        }
        
        // 3. Verifikasi Password dan Set User
        if ($loginUser && Hash::check($password, $loginUser->password)) {
            $guard = $loginUser instanceof EmployeeLogin ? 'karyawan_api' : 'peserta_api';
            Auth::guard($guard)->setUser($loginUser);
            return $loginUser;
        }

        return null;
    }
    
    protected function getProfileData($loginUser)
    {
        // Ambil data profil dari relasi karyawan() atau pasien()
        if ($loginUser instanceof EmployeeLogin) {
             $karyawan = $loginUser->karyawan()->with('departemen')->first(); 
             return [
                'type' => 'Karyawan',
                'id' => $karyawan->id,
                'nama' => $karyawan->nama_karyawan,
                'no_sap' => $karyawan->no_sap,
                'nik' => $karyawan->nik_karyawan,
                'departemen' => $karyawan->departemen->nama_departemen ?? null,
                'unit_kerja' => $karyawan->unitKerja->nama_unit_kerja ?? null,
                'email' => $karyawan->email,
                'no_hp' => $karyawan->no_hp,
                'foto' => $karyawan->foto_profil ? asset('storage/' . $karyawan->foto_profil) : null,
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
             ];
        } elseif ($loginUser instanceof PesertaMcuLogin) {
             $pasien = $loginUser->pasien()->first();
             return [
                'type' => 'Pasien',
                'id' => $pasien->id,
                'nama' => $pasien->nama_lengkap,
                'nik' => $pasien->nik_pasien,
                'email' => $pasien->email,
                'no_hp' => $pasien->no_hp,
                'foto' => $pasien->foto_profil ? asset('storage/' . $pasien->foto_profil) : null,
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
}