<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\JadwalMcuController;
use App\Http\Controllers\HasilMcuController;
use App\Http\Controllers\PemantauanLingkunganController;
use Livewire\Livewire;

Route::get('/', function () {
    return view('welcome');
});

// Rute untuk Otentikasi (Login & Logout)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Grup Rute untuk Admin (membutuhkan otentikasi guard 'admin_users')
Route::middleware(['auth:admin_users'])->prefix('admin')->group(function () {
    // Dashboard Admin
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Manajemen Karyawan (CRUD)
    Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan.index');
    Route::get('/karyawan/create', [KaryawanController::class, 'create'])->name('karyawan.create');
    // Route::post('/karyawan', [KaryawanController::class, 'store'])->name('karyawan.store');
    Route::get('/karyawan/{karyawan}', [KaryawanController::class, 'show'])->name('karyawan.show');
    Route::get('/karyawan/{karyawan}/edit', [KaryawanController::class, 'edit'])->name('karyawan.edit');
    // Route::put('/karyawan/{karyawan}', [KaryawanController::class, 'update'])->name('karyawan.update');
    Route::delete('/karyawan/{karyawan}', [KaryawanController::class, 'destroy'])->name('karyawan.destroy');
    Route::post('/karyawan/import', [KaryawanController::class, 'importExcel'])->name('karyawan.import');
    Route::get('/admin/karyawan/download', [KaryawanController::class, 'downloadExcel'])
     ->name('karyawan.download');

        // Manajemen Jadwal MCU
        // Cara terbaik: Menggunakan Route::resource untuk mendaftarkan rute CRUD secara otomatis
    Route::resource('jadwal', JadwalMcuController::class);
    // Atau, jika Anda hanya ingin menambahkan rute 'show' saja:
    Route::get('/jadwal/{jadwal}', [JadwalMcuController::class, 'show'])->name('jadwal.show');
    Route::get('/jadwal-mcu', [JadwalMcuController::class, 'index'])->name('jadwal.index');
    Route::get('/jadwal-mcu/create', [JadwalMcuController::class, 'create'])->name('jadwal.create');
    // Tambahkan rute khusus untuk memperbarui status
    Route::post('jadwal/{jadwal}/update-status', [JadwalMcuController::class, 'updateStatus'])->name('jadwal.update-status');
    Route::get('/jadwal-mcu/{jadwalMcu}/input-hasil', [HasilMcuController::class, 'showInputForm'])->name('hasil.create');
    Route::post('/jadwal-mcu/{jadwalMcu}/input-hasil', [HasilMcuController::class, 'simpanHasil'])->name('hasil.store');

    // Manajemen Pemantauan Lingkungan
    Route::get('/pemantauan-lingkungan', [PemantauanLingkunganController::class, 'index'])->name('pemantauan.index');
    Route::post('/pemantauan-lingkungan', [PemantauanLingkunganController::class, 'simpan'])->name('pemantauan.store');
});

// Grup Rute untuk Karyawan (membutuhkan otentikasi guard 'employee_logins')
Route::middleware(['auth:employee_logins'])->prefix('karyawan')->group(function () {
    // Dashboard Karyawan
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('karyawan.dashboard');
    
    // Pendaftaran MCU (dari sisi karyawan)
    Route::post('/daftar-mcu', [JadwalMcuController::class, 'simpanPendaftaran'])->name('karyawan.daftar-mcu');
});
