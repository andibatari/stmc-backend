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

Route::get('/test-api', function () {
    return response()->json(['message' => 'API is running']);
});
Route::post('/login', [ApiAuthController::class, 'login']);

// Rute ini membutuhkan autentikasi standar (misalnya, Token Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    
    // 1. Rute Profil Admin/Pengguna
    // Endpoint: /api/user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Rute Logout
    Route::post('/logout', [ApiAuthController::class, 'logout']);

    // --- Rute Baru: Mengubah Kata Sandi ---
    // Endpoint: /api/change-password
    Route::post('/change-password', [ApiAuthController::class, 'changePassword']);

    // 2. Rute Jadwal MCU (Medical Check Up)
    Route::prefix('jadwal-mcu')->group(function () {
        
        // [BARU] Endpoint: /api/jadwal-mcu/ajukan (Pengajuan jadwal)
        Route::post('/ajukan', [App\Http\Controllers\Api\JadwalMcuApiController::class, 'store']); // <--- BARU

        // [BARU] Endpoint: /api/jadwal-mcu/riwayat (Mengambil riwayat jadwal per user)
        Route::get('/riwayat', [App\Http\Controllers\Api\JadwalMcuApiController::class, 'getRiwayatByUser']); // <--- BARU

        // Endpoint: /api/jadwal-mcu
        // Route::get('/', [App\Http\Controllers\JadwalMcuController::class, 'apiIndex']); // Hapus/Ganti dengan riwayat
        // Route::get('/{id}', [App\Http\Controllers\JadwalMcuController::class, 'apiShow']); // Hapus/Ganti dengan detail
    });

    // // 4. Rute Data Keluarga (Jika diperlukan untuk ditampilkan di profil)
    // Route::prefix('keluarga')->group(function () {
    //     // Endpoint: /api/keluarga/user
    //     // Mengambil data keluarga yang terkait dengan pengguna yang sedang login
    //     Route::get('/user', [App\Http\Controllers\KeluargaController::class, 'apiShowByUser']);
    // });

    // 5. Rute untuk Review (Asumsi endpoint dikirim ke controller)
    // Endpoint: /api/review
    Route::post('/review', [App\Http\Controllers\ReviewController::class, 'store']);
});

