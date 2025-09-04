<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //Logika untuk menampilkan halaman login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    //Logika untuk menangani proses permintaan login
    public function login(Request $request)
    {
        //validasi data input dari formulir login
        $credentials = $request->validate([
            'no_sap' => 'required',
            'password' => 'required|min:6',
        ]);

        //Kustomisasi array crentials untuk menggunakan 'no_sap'
        $customCredentials = [
            'no_sap' => $credentials['no_sap'],
            'password' => $credentials['password'],
        ];

        //Coba otentikasi sebagai Admin atau karyawan dengan 'no_sap'
        if (Auth::guard('admin_users')->attempt($customCredentials)) 
        {
            $request->session()->regenerate();
            // Autentikasi berhasil sebagai Admin, redirect ke halaman yang diinginkan
            return redirect()->intended('/admin/dashboard');
        } 

        if (Auth::guard('employee_logins')->attempt($customCredentials)) 
        {
            $request->session()->regenerate();
            // Autentikasi berhasil sebagai Karyawan, redirect ke halaman yang diinginkan
            return redirect()->intended('/karyawan/dashboard');
        }
        return back()->withErrors([
            'no_sap' => 'No SAP atau password salah.',
        ]) -> onlyInput('no_sap');
    }

    //Logika untuk menangani permintaan logout
    public function logout(Request $request)
    {
       Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Redirect ke halaman login setelah logout
    return redirect('/login')->with('message', 'Anda telah berhasil logout.');
        return redirect('/login')->with('message', 'Anda telah berhasil logout.');
    }

    // Logika untuk memproses permintaan login dari API
    public function apiLogin(Request $request)
    {
        $credentials = $request->validate([
            'no_sap' => 'required',
            'password' => 'required',
        ]);

        if (Auth::guard('employee_logins')->attempt($credentials)) {
            $user = Auth::guard('employee_logins')->user();
            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json(['token' => $token]);
        }

        return response()->json(['message' => 'Nomor SAP atau password salah.'], 401);
    }

    // Logika untuk logout dari API
    public function apiLogout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Berhasil logout.']);
    }
}
