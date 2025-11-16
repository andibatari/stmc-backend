<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\JadwalMcuController;
use App\Http\Controllers\HasilMcuController;
use App\Http\Controllers\KeluargaController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\PdfMergerController;
use App\Http\Controllers\McuPdfController;
use Livewire\Livewire;
// Tambahkan komponen Livewire yang baru di sini
use App\Livewire\Admin\TambahAdmin;
use App\Livewire\Admin\TambahDokter;
use App\Livewire\QrScanner;
use App\Livewire\PaketPoli;
use App\Livewire\PemantauanLingkunganIndex;
use App\Livewire\PemantauanLingkunganForm;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\PoliGigiResult;
use App\Models\KebugaranResult;
use App\Models\FisikResult;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Rute untuk Otentikasi (Login & Logout)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Grup Rute untuk Admin (membutuhkan otentikasi guard 'admin_users')
Route::middleware(['auth:admin_users'])->prefix('admin')->group(function () {
    
     // KRITIS: Rute Manajemen Profil Admin
        Route::get('/profile/edit', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
    // Rute untuk memproses pembaruan data (membutuhkan method PUT)
    // Pastikan baris ini ada, menggunakan Route::put
    Route::put('/admin/profile/update', [AdminProfileController::class, 'update'])->name('admin.profile.update');    
    // Tambahkan rute ini untuk dipanggil oleh AJAX
Route::get('/admin/dashboard/lingkungan-data', [DashboardController::class, 'getLingkunganDataJson'])->name('dashboard.data_lingkungan');
    // Dashboard Admin
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Manajemen Karyawan
    Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan.index');
    Route::get('/karyawan/{karyawan_id}/keluarga/{tipe}', [KaryawanController::class, 'showKeluarga'])->name('data.keluarga.show');
    Route::get('/karyawan/create', [KaryawanController::class, 'create'])->name('karyawan.create');
    Route::get('/karyawan/{karyawan}', [KaryawanController::class, 'show'])->name('karyawan.show');
    Route::get('/keluarga/{pesertaMcu}', [KeluargaController::class, 'show'])->name('pasien.show');
    Route::get('/karyawan/{karyawan}/edit', [KaryawanController::class, 'edit'])->name('karyawan.edit');
    Route::get('/keluarga/{keluarga}/edit', [KeluargaController::class, 'edit'])->name('keluarga.edit');
    Route::delete('/karyawan/{karyawan}', [KaryawanController::class, 'destroy'])->name('karyawan.destroy');
    Route::delete('/keluarga/{keluarga}', [KeluargaController::class, 'destroy'])->name('keluarga.destroy');
    Route::post('/karyawan/import', [KaryawanController::class, 'importExcel'])->name('karyawan.import');
    Route::get('/karyawan/download', [KaryawanController::class, 'downloadExcel'])->name('karyawan.download');
    Route::get('/karyawan/{karyawan_id}/add-keluarga', [KaryawanController::class, 'addKeluarga'])->name('karyawan.add.keluarga');
    Route::post('/karyawan/{karyawan_id}/store-keluarga', [KaryawanController::class, 'storeKeluarga'])->name('karyawan.store.keluarga');
    Route::get('/add-pasien-non-karyawan', [KaryawanController::class, 'addKeluarga'])->name('pasien.add.nonkaryawan');
    Route::post('/store-pasien-non-karyawan', [KaryawanController::class, 'storeKeluarga'])->name('pasien.store.nonkaryawan');
    Route::get('/peserta-mcu/download', [KaryawanController::class, 'pesertaMcuExcel'])->name('peserta.mcu.download');
    Route::post('/peserta-mcu/import', [KaryawanController::class, 'pesertaMcuImport'])->name('peserta-mcu.import');

    // Manajemen Jadwal MCU
    Route::resource('jadwal', JadwalMcuController::class);
    Route::get('/jadwal-mcu', [JadwalMcuController::class, 'index'])->name('jadwal.index');
    Route::get('/jadwal-mcu/create', [JadwalMcuController::class, 'create'])->name('jadwal.create');
    Route::delete('/jadwal/{jadwal}', [JadwalMcuController::class, 'destroy'])->name('jadwal.destroy');
    Route::get('/jadwal/{jadwal}/detail', \App\Livewire\QrPatientDetail::class)->name('qr-patient-detail');

    Route::post('jadwal/{jadwal}/update-status', [JadwalMcuController::class, 'updateStatus'])->name('jadwal.update-status');
    Route::get('/scan-qr', QrScanner::class)->name('scan.qr');
    Route::get('/jadwal-mcu/{jadwalMcu}/input-hasil', [HasilMcuController::class, 'showInputForm'])->name('hasil.create');
    Route::post('/jadwal-mcu/{jadwalMcu}/input-hasil', [HasilMcuController::class, 'simpanHasil'])->name('hasil.store');

    // Manajemen Pengguna (Tambah Admin & Dokter)
    Route::get('/tambah-admin', TambahAdmin::class)->name('admin.create');
    Route::get('/tambah-dokter', TambahDokter::class)->name('admin.tambah-dokter');
    Route::get('/kelola-paket-poli', PaketPoli::class)->name('paket-poli');

     Route::prefix('lingkungan')->group(function () {
        Route::get('pemantauan', PemantauanLingkunganIndex::class)->name('pemantauan.index');
        Route:
    });
    Route::prefix('lingkungan')->group(function () {
        Route::get('pemantauan', PemantauanLingkunganIndex::class)->name('pemantauan.index');
        Route::get('pemantauan/tambah', PemantauanLingkunganForm::class)->name('pemantauan.create');
    });

    // Rute untuk mengunduh PDF gabungan
    Route::get('/download-mcu-summary/{jadwalId}', [McuPdfController::class, 'downloadMcuSummary'])->name('download.mcu.summary');
    Route::get('/download/mcu-resume/{jadwalId}', [McuPdfController::class, 'downloadResume'])->name('download.resume.pdf');
    // Route::get('/kelola-paket-poli', KelolaLayanan::class)->name('paket-poli');
    // Route untuk menampilkan/mengunduh PDF secara aman dari folder storage
    // Rute untuk menampilkan/mengunduh PDF secara aman dari folder storage
    // Nama rute sekarang akan menjadi 'admin.pdf.view'
    Route::get('/pdf/view/{id}', function ($id) {
        $result = PoliGigiResult::where('jadwal_poli_id', $id)->firstOrFail();
        $filePath = $result->file_path;

        if ($filePath) {
            // Hapus 'public/' dari path untuk Flysystem
            $relativePath = str_replace('public/', '', $filePath); 

            if (Storage::disk('public')->exists($relativePath)) {
                // KIRIM RESPONSE HANYA MENGGUNAKAN PATH RELATIF
                return Storage::disk('public')->response($relativePath, basename($filePath), [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
                ]);
            }
        }
        
        abort(404, 'File PDF tidak ditemukan atau belum dibuat.');
    })->name('pdf.view');

    Route::get('/pdf/kebugaran/{id}', function ($id) {
        // 1. Cari hasilnya langsung di tabel KebugaranResult menggunakan jadwal_poli_id
        $result = App\Models\KebugaranResult::where('jadwal_poli_id', $id)->first();
        
        if (!$result || !$result->file_path) {
            // Jika data tidak ada, tampilkan error yang lebih spesifik
            abort(404, 'Hasil Kebugaran tidak ditemukan atau file PDF belum dibuat.');
        }
        
        $filePath = $result->file_path; 
        
        // Hapus 'public/' dari path jika menggunakan Storage::disk('public')
        // Path di DB: pdf_reports/Kebugaran_N_A_JadwalPoli_8.pdf
        $relativePath = str_replace('public/', '', $filePath); 

        if (Storage::disk('public')->exists($relativePath)) {
            return Storage::disk('public')->response($relativePath, basename($filePath), [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
            ]);
        }
        
        abort(404, 'File PDF Kebugaran ditemukan di database tetapi tidak ada di storage.');
    })->name('pdf.kebugaran.view');

    Route::get('/pdf/fisik/{id}', function ($id) {
        // Mencari data FisikResult menggunakan jadwal_poli_id
        $result = App\Models\FisikResult::where('jadwal_poli_id', $id)->firstOrFail();
        $filePath = $result->file_path; 

        if (!$filePath) {
            abort(404, 'File path tidak ditemukan di Fisik Result.');
        }
        
        // Hapus 'public/' dari path jika menggunakan Storage::disk('public')
        $relativePath = str_replace('public/', '', $filePath); 

        if (Storage::disk('public')->exists($relativePath)) {
            return Storage::disk('public')->response($relativePath, basename($filePath), [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
            ]);
        }
        
        abort(404, 'File PDF Fisik tidak ditemukan di storage.');
    })->name('pdf.fisik.view');

    Route::get('/download/{filePath}', function ($filePath) {
    
        // 1. Tentukan path relatif di dalam disk 'public'
        $relativePath = 'pdf_reports/' . $filePath; 

        // 2. Periksa apakah file ada di disk 'public'
        if (!Storage::disk('public')->exists($relativePath)) {
            abort(404, 'File ' . $filePath . ' tidak ditemukan di storage/app/public/pdf_reports/.');
        }

        // 3. Kembalikan respons untuk menampilkan file (inline)
        return Storage::disk('public')->response($relativePath, basename($filePath), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
        ]);

    })->where('filePath', '.*')->name('download');
});

// Grup Rute untuk Karyawan (jika ada guard terpisah)
Route::middleware(['auth:employee_logins'])->prefix('karyawan')->group(function () {
    // Dashboard Karyawan
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('karyawan.dashboard');
    
    // Pendaftaran MCU (dari sisi karyawan)
    Route::post('/daftar-mcu', [JadwalMcuController::class, 'simpanPendaftaran'])->name('karyawan.daftar-mcu');
});