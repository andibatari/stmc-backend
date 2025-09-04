<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\JadwalMcuController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\HasilMcuController;
use App\Http\Controllers\AuthController; // Tambahkan ini
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Di sini Anda dapat mendaftarkan rute API untuk aplikasi Anda. Rute-rute ini
| dimuat oleh RouteServiceProvider dalam grup yang memiliki middleware "api".
| Nikmati membangun API Anda!
|
*/

// Rute untuk mendapatkan Unit Kerja berdasarkan Departemen ID
Route::get('/departemens/{departemen}/unit-kerjas', [KaryawanController::class, 'getUnitsByDepartemen']) 
    ->name('departemen.unitkerjas');

// Rute untuk mencari data karyawan berdasarkan No. SAP untuk autofill
Route::get('/karyawan/lookup-by-sap/{noSap}', [KaryawanController::class, 'lookupBySap']);





// Rute yang TIDAK memerlukan otentikasi (seperti login)
Route::post('/login', [AuthController::class, 'apiLogin']); // Tambahkan rute login API

// Rute yang membutuhkan otentikasi (menggunakan guard API, e.g., Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Rute untuk logout
    Route::post('/logout', [AuthController::class, 'apiLogout']); // Tambahkan rute logout API

    // Rute untuk mendapatkan jadwal MCU karyawan
    Route::get('/jadwal', [JadwalMcuController::class, 'daftarJadwal']);
    
    // Rute untuk pendaftaran MCU dari aplikasi mobile
    Route::post('/jadwal/daftar', [JadwalMcuController::class, 'simpanPendaftaran']);

    // Rute untuk melihat notifikasi
    Route::get('/notifikasi', [NotificationController::class, 'index']);
    Route::post('/notifikasi/{notif}/read', [NotificationController::class, 'markAsRead']);

    // Rute untuk melihat hasil MCU
    Route::get('/hasil-mcu', [HasilMcuController::class, 'lihatHasil']);
});
