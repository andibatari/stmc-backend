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
    Route::post('/jadwal-mcu/ajukan', [JadwalMcuApiController::class, 'store']);
    Route::get('/jadwal-mcu/riwayat', [JadwalMcuApiController::class, 'getRiwayatByUser']);
});

// /**
//  * ===============================
//  * ROUTE KHUSUS MCU
//  * ===============================
//  */
// Route::middleware('auth:employee_api')->group(function () {
//     Route::post('/jadwal-mcu/ajukan', [JadwalMcuApiController::class, 'store']);
//     Route::get('/jadwal-mcu/riwayat', [JadwalMcuApiController::class, 'getRiwayatByUser']);
// });

// Route::middleware('auth:peserta_api')->group(function () {
//     Route::post('/jadwal-mcu/ajukan', [JadwalMcuApiController::class, 'store']);
//     Route::get('/jadwal-mcu/riwayat', [JadwalMcuApiController::class, 'getRiwayatByUser']);
// });
