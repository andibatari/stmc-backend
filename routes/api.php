<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\JadwalMcuApiController;
use App\Http\Controllers\Api\LingkunganApiController;
use App\Http\Controllers\Api\JadwalDokterApiController;

// IMPORT BARU UNTUK KEBUTUHAN FCM
use Illuminate\Http\Request;
use App\Models\Karyawan;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [ApiAuthController::class, 'login']);
Route::get('/jadwal-mcu/download-laporan-gabungan/{id}', [JadwalMcuApiController::class, 'downloadLaporanGabungan']);

/**
 * ===============================
 * ROUTE UNTUK UPDATE FCM TOKEN (FLUTTER)
 * ===============================
 */
Route::post('/update-fcm-token', function (Request $request) {
    $request->validate([
        'karyawan_id' => 'required',
        'fcm_token' => 'required'
    ]);

    Karyawan::where('id', $request->karyawan_id)
            ->update(['fcm_token' => $request->fcm_token]);

    return response()->json([
        'status' => 'success', 
        'message' => 'FCM Token berhasil diperbarui'
    ]);
});


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
    Route::post('/jadwal-poli/checkin', [\App\Http\Controllers\Api\JadwalMcuApiController::class, 'checkInPoli']);
    // TAMBAHKAN RUTE FILTER INI AGAR DROPDOWN DI FLUTTER BERFUNGSI:
    Route::get('/lingkungan/filters', [LingkunganApiController::class, 'getFilters']);
    Route::get('/jadwal-dokter/events', [JadwalDokterApiController::class, 'getEvents']);
    
    // Rute Jadwal MCU
    Route::prefix('jadwal-mcu')->group(function () {
        Route::post('/ajukan', [JadwalMcuApiController::class, 'store']);
        Route::get('/riwayat', [JadwalMcuApiController::class, 'getRiwayatByUser']);
        Route::get('/paket', [JadwalMcuApiController::class, 'getPaketMcu']);
    });
});