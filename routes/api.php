<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\JadwalMcuApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [ApiAuthController::class, 'login']);
Route::get('/jadwal-mcu/download-laporan-gabungan/{id}', [JadwalMcuApiController::class, 'downloadLaporanGabungan'])
    ->middleware('auth:sanctum');

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
    // Rute Jadwal MCU
    Route::prefix('jadwal-mcu')->group(function () {
        Route::post('/ajukan', [JadwalMcuApiController::class, 'store']);
        Route::get('/riwayat', [JadwalMcuApiController::class, 'getRiwayatByUser']);
    });
});
