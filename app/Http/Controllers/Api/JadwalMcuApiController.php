<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Models\JadwalMcu;
use App\Models\EmployeeLogin;
use App\Models\PesertaMcuLogin;
use App\Models\Dokter;
use App\Http\Resources\JadwalMcuResource;
use iio\libmergepdf\Merger;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;


class JadwalMcuApiController extends Controller
{
    public function store(Request $request)
    {
        $loginUser = auth('sanctum')->user(); // ⬅️ LOGIN MODEL (EmployeeLogin / PesertaMcuLogin)

        if (!$loginUser) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        try {
            $request->validate([
                'tanggal_mcu' => 'required|date|after_or_equal:today',
                'paket_mcu'   => 'required|exists:paket_mcus,id',
            ]);

            // Tentukan User Profil
            if ($loginUser instanceof EmployeeLogin) {
                $user = $loginUser->karyawan;
                $column = 'karyawan_id';
            } elseif ($loginUser instanceof PesertaMcuLogin) {
                $user = $loginUser->pasien;
                $column = 'peserta_mcus_id';
            } 

            // Cek Jadwal Aktif
            if (JadwalMcu::where($column, $user->id)->whereIn('status', ['Scheduled', 'Present'])->exists()) {
                return response()->json(['success' => false, 'message' => 'Anda sudah memiliki jadwal MCU aktif.'], 409);
            }

            $tanggal = Carbon::parse($request->tanggal_mcu)->toDateString();

            // ==============================================================
            // ⬇️ TAMBAHAN FITUR: CEK KUOTA MAKSIMAL 30 ORANG/HARI ⬇️
            // ==============================================================
            $kuotaTerisi = JadwalMcu::whereDate('tanggal_mcu', $tanggal)
                                    ->where('status', '!=', 'Canceled') // Abaikan yang sudah dibatalkan
                                    ->count();

            if ($kuotaTerisi >= 30) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Mohon maaf, kuota jadwal untuk tanggal tersebut sudah penuh. Silakan pilih hari yang lain.'
                ], 422); // Gunakan 422 Unprocessable Entity
            }
            // ==============================================================
            // ⬆️ AKHIR TAMBAHAN FITUR ⬆️
            // ==============================================================

            // 1. Ambil nomor antrean terakhir KHUSUS pada tanggal yang dipilih
            $lastAntrean = JadwalMcu::whereDate('tanggal_mcu', $tanggal)
                ->orderBy('id', 'desc')
                ->first();

            if ($lastAntrean && $lastAntrean->no_antrean) {
                // Mengambil angka dari string 'C001' -> menjadi integer 1
                $lastNumber = (int) substr($lastAntrean->no_antrean, 1); 
                $nextNumber = $lastNumber + 1;
            } else {
                // Jika belum ada antrean di tanggal tersebut, mulai dari 1
                $nextNumber = 1;
            }

            $jadwalDokter = \App\Models\JadwalDokter::whereDate('tanggal', $tanggal)->first();
            $dokterId = $jadwalDokter ? $jadwalDokter->dokter_id : null;

            $jadwal = JadwalMcu::create([
                'qr_code_id'       => (string) Str::uuid(),
                'tanggal_mcu'      => $tanggal,
                'tanggal_pendaftaran' => now()->toDateString(),
                'paket_mcus_id'    => $request->paket_mcu,
                'dokter_id'        => $dokterId,
                'no_antrean'       => 'C' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT),
                'status'           => 'Scheduled',
                'nama_pasien'      => $user->nama ?? $user->nama_lengkap,
                'nik_pasien'       => $user->nik ?? $user->nik_pasien,
                'no_sap'             => $user->no_sap ?? null,
                'perusahaan_asal'  => $user->perusahaan ?? 'Pribadi',
                'karyawan_id'      => ($column == 'karyawan_id') ? $user->id : null,
                'peserta_mcus_id'  => ($column == 'peserta_mcus_id') ? $user->id : null,
            ]);

            // ==============================================================
            // TAMBAHAN: GENERATE JADWAL POLI OTOMATIS BERDASARKAN PAKET MCU
            // ==============================================================
            $paket = \App\Models\PaketMcu::find($request->paket_mcu);
            
            // Cek apakah paketnya ada dan ambil relasi polis-nya
            if ($paket && $paket->poli()->exists()) {
                foreach ($paket->poli as $itemPoli) {
                    \App\Models\JadwalPoli::create([
                        'jadwal_mcus_id' => $jadwal->id,
                        'poli_id'        => $itemPoli->id,
                        'status'         => 'Pending',
                    ]);
                }
            }
            // ==============================================================

            return response()->json(['success' => true, 'qr_code_id' => $jadwal->qr_code_id, 'message' => 'Jadwal berhasil dibuat'], 201);

        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getRiwayatByUser()
    {
        try {
            $loginUser = auth('sanctum')->user();
            if (!$loginUser) return response()->json(['message' => 'Unauthenticated'], 401);

            // Pastikan filter ID menggunakan ID Karyawan/Pasien yang tepat
            if ($loginUser instanceof EmployeeLogin) {
                $column = 'karyawan_id';
                $userId = $loginUser->karyawan_id;
            } elseif ($loginUser instanceof PesertaMcuLogin) {
                $column = 'peserta_mcus_id';
                $userId = $loginUser->peserta_mcu_id;
            } else {
                return response()->json(['message' => 'User tidak dikenali'], 403);
            }

            // AMBIL DATA BERDASARKAN USER ID (Penting agar tidak tertukar)
            $riwayat = JadwalMcu::where($column, $userId)
                ->with(['dokter', 'paketMcu', 'jadwalPoli.poli'])
                ->orderBy('id', 'asc') // Urutan asc agar iterasi #1, #2 benar
                ->get();

            // Mengembalikan data menggunakan Resource agar iteration_number diproses
            return JadwalMcuResource::collection($riwayat);

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Download: Fungsi Merger PDF Gabungan
     */
    public function downloadLaporanGabungan($id)
    {
        // 1. Validasi Token dari URL (untuk url_launcher Flutter)
        $token = request()->query('token');
        if (!$token) {
            return response()->json(['message' => 'Token autentikasi diperlukan'], 401);
        }

        try {
            // 2. Ambil data Jadwal beserta relasinya
            $jadwal = JadwalMcu::with(['jadwalPoli', 'karyawan.departemen', 'karyawan.unitKerja', 'karyawan.provinsi', 'pesertaMcu', 'dokter', 'paketMcu'])
                ->findOrFail($id);

            // Mendukung Pasien Karyawan maupun Pasien Umum
            $patient = $jadwal->karyawan ?? $jadwal->pesertaMcu;
            if (!$patient) {
                return response()->json(['message' => 'Data pasien tidak ditemukan'], 404);
            }

            $tanggalMcuFormatted = Carbon::parse($jadwal->tanggal_mcu)->format('d/m/Y');

            // 🌟 3. AMBIL DATA SETTING (SAMA SEPERTI VERSI ADMIN)
            $namaKepalaKlinik = \App\Models\Setting::where('key', 'nama_kepala_klinik')->value('value') ?? 'Dr. Penanggung Jawab';
            $teksDisclaimerRaw = \App\Models\Setting::where('key', 'teks_disclaimer')->value('value') ?? 'Pada Pemeriksaan Kesehatan Berkala di Klinik Semen Tonasa Medical Centre yang dilakukan pada tanggal <b>[TANGGAL]</b>...';
            $teksDisclaimerFinal = str_replace('[TANGGAL]', $tanggalMcuFormatted, $teksDisclaimerRaw);

            // 🌟 FUNGSI PENGUBAH GAMBAR KE BASE64
            $getBase64Image = function($dbPath, $defaultPath) {
                $fullPath = null;
                if (!empty($dbPath)) {
                    $cleanPath = str_replace('storage/', '', $dbPath);
                    $storagePath = storage_path('app/public/' . $cleanPath);
                    if (file_exists($storagePath)) $fullPath = $storagePath;
                }
                if (!$fullPath && file_exists(public_path($defaultPath))) {
                    $fullPath = public_path($defaultPath);
                }
                if ($fullPath) {
                    $type = pathinfo($fullPath, PATHINFO_EXTENSION);
                    $data = file_get_contents($fullPath);
                    return 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
                return ''; 
            };

            // Konversi logo
            $logoStmcBase64 = $getBase64Image(\App\Models\Setting::where('key', 'logo_stmc')->value('value'), 'images/logo-stmc.png');
            $logoTonasaBase64 = $getBase64Image(\App\Models\Setting::where('key', 'logo_tonasa')->value('value'), 'images/logo-semen-tonasa.png');
            
            // Generate QR Code untuk segel tanda tangan
            $linkValidasiPublik = route('validasi.pdf', $jadwal->qr_code_id);
            $qrCode = new QrCode($linkValidasiPublik);
            $qrCode->setSize(150); 
            $qrCode->setMargin(0); 
            $qrCode->setEncoding(new Encoding('UTF-8')); 
            $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::High); 
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $qrCodeBase64 = base64_encode($result->getString());

            // 4. Siapkan Data untuk View Resume Blade
            $dataResume = [
                'jadwal' => $jadwal,
                'patient' => $patient,
                'patient_data' => [
                    'nama'           => $patient->nama_karyawan ?? $patient->nama_lengkap ?? 'N/A',
                    'tgl_lahir'      => $patient->tanggal_lahir ?? 'N/A',
                    'alamat'         => $patient->alamat ?? 'N/A',
                    'jenis_kelamin'  => $patient->jenis_kelamin ?? 'N/A',
                    'nik_sap'        => $patient->no_sap ?? $patient->nik_karyawan ?? $patient->nik_pasien ?? 'N/A',
                    'paket_mcu'      => $jadwal->paketMcu->nama_paket ?? 'N/A',
                    'unit_kerja'     => $patient->unitKerja->nama_unit_kerja ?? 'N/A',
                ],
                
                'tanggal_mcu'     => $tanggalMcuFormatted,
                'resume_body_raw' => $jadwal->resume_body, 
                'resume_saran'    => $jadwal->resume_saran,
                'resume_kategori' => $jadwal->resume_kategori,
                'tanggal_cetak'   => now()->translatedFormat('d F Y'),
                'qrCodeBase64'    => $qrCodeBase64,
                
                'doctor_data'     => [
                    'nama' => $jadwal->dokter->nama_lengkap ?? 'Dokter Tidak Ditunjuk',
                    'nip'  => $jadwal->dokter->nip ?? '-'
                ],

                // 🌟 DATA SETTING DENGAN BASE64
                'setting_kepala_klinik' => $namaKepalaKlinik, 
                'setting_disclaimer'  => $teksDisclaimerFinal,
                'setting_logo_stmc'   => $logoStmcBase64, 
                'setting_logo_tonasa' => $logoTonasaBase64,
            ];

            // 5. Inisialisasi Merger
            $merger = new Merger();

            // --- LANGKAH A: GENERATE PDF RESUME (Halaman Pertama) ---
            $resumePdf = Pdf::loadView('pdfs.mcu_resume', $dataResume)->output();
            $merger->addRaw($resumePdf);

            // --- LANGKAH B: AMBIL SEMUA FILE POLI YANG SUDAH SELESAI ---
            // 🌟 PERBAIKAN: Mengambil file dari Local Storage (disk 'public') jika disk s3 gagal/tidak dipakai
            $files = $jadwal->jadwalPoli->whereNotNull('file_path')->pluck('file_path')->toArray();

            foreach ($files as $filePath) {
                // Jika kamu menyimpan file di local storage (seperti pada saat upload dari admin)
                if (Storage::disk('public')->exists($filePath)) {
                    $fileContent = Storage::disk('public')->get($filePath);
                    $merger->addRaw($fileContent);
                } 
                // Fallback jika file ternyata ada di S3
                elseif (Storage::disk('s3')->exists($filePath)) {
                    $fileContent = Storage::disk('s3')->get($filePath);
                    $merger->addRaw($fileContent);
                } else {
                    \Log::warning("File poli tidak ditemukan saat diunduh via HP: " . $filePath);
                }
            }

            // 6. Proses Penggabungan Akhir
            $output = $merger->merge();
            
            $namaAman = $patient->nama_karyawan ?? $patient->nama_lengkap ?? 'Pasien';
            $safeName = Str::slug($namaAman);
            $fileName = "Laporan_MCU_{$safeName}_{$jadwal->no_antrean}.pdf";

            // 7. Return sebagai Stream PDF ke Browser/Flutter
            return response($output)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', "inline; filename=\"$fileName\"");

        } catch (\Exception $e) {
            \Log::error("Gagal Download Laporan Gabungan (API): " . $e->getMessage() . " on line " . $e->getLine());
            return response()->json([
                'message' => 'Gagal memproses laporan gabungan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getPaketMcu()
    {
        try {
            // Ambil ID dan Nama Paket dari tabel paket_mcus
            // Sesuaikan nama kolom jika berbeda (misal: 'id' dan 'nama_paket')
            $pakets = \DB::table('paket_mcus')
                ->select('id', 'nama_paket as name') 
                ->get();

            return response()->json([
                'success' => true,
                'data' => $pakets
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data paket'
            ], 500);
        }
    }

    public function checkInPoli(Request $request)
    {
        // Pastikan user sedang login
        $loginUser = auth('sanctum')->user();
        if (!$loginUser) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Validasi input dari Flutter
        $request->validate([
            'id_jadwal_poli' => 'required|exists:jadwal_polis,id'
        ]);

        try {
            // Cari data antrean poli tersebut
            $jadwalPoli = \App\Models\JadwalPoli::findOrFail($request->id_jadwal_poli);

            // Pastikan statusnya masih Pending sebelum diubah
            if ($jadwalPoli->status !== 'Pending') {
                return response()->json([
                    'success' => false, 
                    'message' => 'Gagal: Antrean ini sudah diambil atau sudah selesai.'
                ], 400);
            }

            // Hitung Nomor Antrean Otomatis
            // Cari antrean tertinggi untuk poli yang sama pada tanggal MCU yang sama
            $currentCount = \App\Models\JadwalPoli::where('poli_id', $jadwalPoli->poli_id)
                ->whereHas('jadwalMcu', function($query) use ($jadwalPoli) {
                    $query->whereDate('tanggal_mcu', $jadwalPoli->jadwalMcu->tanggal_mcu);
                })
                ->where('status', '!=', 'Canceled') // Hanya hitung yang tidak batal
                ->where('status', '!=', 'Pending')  // Hitung yang sudah ambil antrean
                ->count();

            $nextNumber = $currentCount + 1; // Jika 1 orang sudah antre, maka ini nomor 2

            // 3. Update status menjadi Waiting sekaligus simpan nomor antrean
            $jadwalPoli->update([
                'status' => 'Waiting',
                'no_antrean_poli' => $nextNumber // Simpan nomor urut antrean
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Berhasil mengambil antrean!',
                'data' => [
                    'no_antrean_poli' => $nextNumber
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan di server: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkKetersediaan(Request $request)
    {
        try {
            $tanggal = $request->query('tanggal');

            // 1. Cek Kuota (Maksimal 30 orang per hari)
            $jumlahTerdaftar = \App\Models\JadwalMcu::whereDate('tanggal_mcu', $tanggal)
                ->where('status', '!=', 'Canceled') // Yang batal tidak dihitung
                ->count();
                
            $sisaKuota = 30 - $jumlahTerdaftar;
            if ($sisaKuota < 0) $sisaKuota = 0;

            // 2. Cari Jadwal Dokter pada tanggal tersebut
            $namaDokter = 'Dokter Piket (Belum Ditentukan)';

            // Kita cek dengan hati-hati agar tidak membuat sistem Crash jika tabel/kolom berbeda
            if (class_exists(\App\Models\JadwalDokter::class)) {
                // SESUAIKAN KATA 'tanggal' DI BAWAH INI DENGAN NAMA KOLOM ASLI DI DATABASE-MU
                $jadwalDokter = \App\Models\JadwalDokter::whereDate('tanggal', $tanggal)->with('dokter')->first();
                
                if ($jadwalDokter && $jadwalDokter->dokter) {
                    $namaDokter = $jadwalDokter->dokter->nama_lengkap;
                }
            }

            // 3. Kembalikan Respon Sukses
            return response()->json([
                'success' => true,
                'sisa_kuota' => $sisaKuota,
                'dokter' => $namaDokter
            ], 200);

        } catch (\Exception $e) {
            // JIKA ADA ERROR DATABASE, KIRIM KE FLUTTER AGAR MUNCUL DI LAYAR HP
            return response()->json([
                'success' => false,
                'message' => 'Error Server Laravel: ' . $e->getMessage()
            ], 500);
        }
    }
}
