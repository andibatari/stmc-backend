<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Karyawan; // Asumsi model Karyawan atau EmployeeLogin digunakan

class AuthController extends Controller
{
    // Logika untuk menampilkan halaman login (WEB)
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Logika untuk menangani proses permintaan login (WEB dan API)
     * Rute /login (WEB) dan /api/login (API) akan memanggil metode ini.
     * Menggunakan Request->expectsJson() untuk membedakan.
     */
    public function login(Request $request)
    {
        // Validasi data input
        $credentials = $request->validate([
            'no_sap' => 'required|string',
            'password' => 'required|min:6',
        ]);

        $customCredentials = [
            'no_sap' => $credentials['no_sap'],
            'password' => $credentials['password'],
        ];

        // --- PENANGANAN API (Untuk Flutter) ---
        if ($request->expectsJson()) {
            
            // Coba otentikasi menggunakan guard employee_logins (yang terhubung ke Karyawan)
            if (Auth::guard('employee_logins')->attempt($customCredentials)) {
                
                // Ambil model User/EmployeeLogin yang berhasil login
                $user = Auth::guard('employee_logins')->user();
                
                // Asumsi: Anda memiliki relasi atau data Karyawan yang terhubung ke EmployeeLogin
                $karyawan = Karyawan::where('no_sap', $user->no_sap)->first();
                
                // Buat token Sanctum untuk aplikasi Flutter
                $token = $user->createToken('flutter-auth-token')->plainTextToken;
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Login API berhasil.',
                    'token' => $token,
                    'user_profile' => $karyawan // Kembalikan detail Karyawan untuk Flutter
                ], 200);
            }

            // Jika otentikasi API gagal
            return response()->json([
                'status' => 'error', 
                'message' => 'Nomor SAP atau password salah.'
            ], 401);
        }

        // --- PENANGANAN WEB (Lanjutan dari kode Anda) ---
        
        // Coba otentikasi sebagai Admin (WEB)
        if (Auth::guard('admin_users')->attempt($customCredentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard');
        } 

        // Coba otentikasi sebagai Karyawan (WEB)
        if (Auth::guard('employee_logins')->attempt($customCredentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/karyawan/dashboard');
        }

        // Jika otentikasi WEB gagal
        return back()->withErrors([
            'no_sap' => 'No SAP atau password salah.',
        ]) -> onlyInput('no_sap');
    }

    /**
     * Logika untuk menangani permintaan logout (WEB dan API)
     */
    public function logout(Request $request)
    {
        // --- PENANGANAN API (Untuk Flutter) ---
        if ($request->expectsJson()) {
            // Hapus token Sanctum yang sedang digunakan
            $request->user()->currentAccessToken()->delete();
            
            return response()->json([
                'status' => 'success', 
                'message' => 'Berhasil logout dari API.'
            ], 200);
        }

        // --- PENANGANAN WEB ---
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('message', 'Anda telah berhasil logout.');
    }
}