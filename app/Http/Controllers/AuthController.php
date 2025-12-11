<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Karyawan; // Asumsi model Karyawan atau EmployeeLogin digunakan
use App\Models\AdminUser;

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

        // --- PENANGANAN WEB (Lanjutan dari kode Anda) ---
        
        // Coba otentikasi sebagai Admin (WEB)
        if (Auth::guard('admin_users')->attempt($customCredentials)) {
             $request->session()->regenerate();
             return redirect()->intended('/admin/dashboard');
        } 

        // Jika otentikasi WEB gagal
        return back()->withErrors([
             'no_sap' => 'Akses ditolak. Nomor SAP atau password salah (Hanya untuk Admin).',
        ])->onlyInput('no_sap');
    }

    /**
     * Logika untuk menangani permintaan logout (WEB dan API)
     */
    public function logout(Request $request)
    {

        // --- PENANGANAN WEB ---
        Auth::guard('admin_users')->logout();
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('message', 'Anda telah berhasil logout.');
    }

    
}