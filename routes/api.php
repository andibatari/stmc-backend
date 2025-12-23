<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\JadwalMcuApiController;
use App\Http\Controllers\Api\LingkunganApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [ApiAuthController::class, 'login']);
Route::get('/jadwal-mcu/download-laporan-gabungan/{id}', [JadwalMcuApiController::class, 'downloadLaporanGabungan']);

/**
 * ===============================
 * ROUTE UMUM (SEMUA TOKEN SANCTUM)
 * ===============================
 */
Route::middleware('auth:sanctum')->group(function () {

    // ✅ Logout untuk SEMUA USER
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    // ✅ Ganti password untuk SEMUA USER
    Route::post('/change-password', [ApiAuthController::class, 'changePassword']);
    Route::post('/update-profile', [ApiAuthController::class, 'updateProfile']); 
    // PEMANTAUAN LINGKUNGAN
    Route::get('/lingkungan', [LingkunganApiController::class, 'index']);
    // TAMBAHKAN RUTE FILTER INI AGAR DROPDOWN DI FLUTTER BERFUNGSI:
    Route::get('/lingkungan/filters', [LingkunganApiController::class, 'getFilters']);

    // Rute Jadwal MCU
    Route::prefix('jadwal-mcu')->group(function () {
        Route::post('/ajukan', [JadwalMcuApiController::class, 'store']);
        Route::get('/riwayat', [JadwalMcuApiController::class, 'getRiwayatByUser']);
        Route::get('/paket', [JadwalMcuApiController::class, 'getPaketMcu']);
    });
});
