<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController as ApiAuthController; // Beri alias untuk menghindari konflik

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Di sini Anda dapat mendaftarkan rute API untuk aplikasi Anda.
| Rute ini dimuat oleh RouteServiceProvider dalam grup yang diberi middleware "api".
| Nikmati membangun API Anda!
|
*/

// --- Rute API untuk Aplikasi Mobile Flutter ---

// --- Rute Non-Autentikasi (Misalnya untuk Login) ---
// Endpoint: /api/login
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);

// Rute ini membutuhkan autentikasi standar (misalnya, Token Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    
    // 1. Rute Profil Admin/Pengguna
    // Endpoint: /api/user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Rute Logout
    Route::post('/logout', [ApiAuthController::class, 'logout']);

    // 2. Rute Manajemen Karyawan (Pengguna Aplikasi)
    // Asumsi: Kita hanya butuh list data dan detail data untuk aplikasi mobile
    Route::prefix('karyawan')->group(function () {
        // Endpoint: /api/karyawan
        // Mengambil daftar semua karyawan
        Route::get('/', [App\Http\Controllers\KaryawanController::class, 'apiIndex']);

        // Endpoint: /api/karyawan/{id}
        // Mengambil detail karyawan tertentu
        Route::get('/{id}', [App\Http\Controllers\KaryawanController::class, 'apiShow']);
    });
    
    // 3. Rute Jadwal MCU (Medical Check Up)
    Route::prefix('jadwal-mcu')->group(function () {
        // Endpoint: /api/jadwal-mcu
        // Mengambil daftar jadwal MCU yang akan datang
        Route::get('/', [App\Http\Controllers\JadwalMcuController::class, 'apiIndex']);

        // Endpoint: /api/jadwal-mcu/{id}
        // Mengambil detail jadwal MCU tertentu
        Route::get('/{id}', [App\Http\Controllers\JadwalMcuController::class, 'apiShow']);
    });

    // 4. Rute Data Keluarga (Jika diperlukan untuk ditampilkan di profil)
    Route::prefix('keluarga')->group(function () {
        // Endpoint: /api/keluarga/user
        // Mengambil data keluarga yang terkait dengan pengguna yang sedang login
        Route::get('/user', [App\Http\Controllers\KeluargaController::class, 'apiShowByUser']);
    });

    // 5. Rute untuk Review (Asumsi endpoint dikirim ke controller)
    // Endpoint: /api/review
    Route::post('/review', [App\Http\Controllers\ReviewController::class, 'store']);
});

