<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Models\JadwalMcu;
use App\Models\EmployeeLogin;
use App\Models\PesertaMcuLogin;
use App\Models\Dokter;
use App\Http\Resources\JadwalMcuResource;
use iio\libmergepdf\Merger;
use Carbon\Carbon;
use Exception;

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

            $jadwal = JadwalMcu::create([
                'qr_code_id'       => (string) Str::uuid(),
                'tanggal_mcu'      => $tanggal,
                'tanggal_pendaftaran' => now()->toDateString(),
                'paket_mcus_id'    => $request->paket_mcu,
                'dokter_id'        => null,
                'no_antrean'       => 'C' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT),
                'status'           => 'Scheduled',
                'nama_pasien'      => $user->nama ?? $user->nama_lengkap,
                'nik_pasien'       => $user->nik ?? $user->nik_pasien,
                'perusahaan_asal'  => $user->perusahaan ?? 'Pribadi',
                'karyawan_id'      => ($column == 'karyawan_id') ? $user->id : null,
                'peserta_mcus_id'  => ($column == 'peserta_mcus_id') ? $user->id : null,
            ]);

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
                ->with(['dokter', 'paketMcu'])
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
        $token = request()->query('token');
        if (!$token) {
            return response()->json(['message' => 'Token autentikasi diperlukan'], 401);
        }
        
        try {
            // 1. Pastikan data ditemukan beserta relasinya
            $jadwal = JadwalMcu::with('jadwalPoli')->findOrFail($id);
            
            // 2. Ambil semua file_path poli yang sudah "Done"
            $files = $jadwal->jadwalPoli->whereNotNull('file_path')->pluck('file_path')->toArray();

            if (empty($files)) {
                return response()->json(['message' => 'Tidak ada file laporan untuk digabungkan'], 404);
            }

            // 3. Inisialisasi Merger
            $merger = new Merger();
            $filesAdded = 0;

            foreach ($files as $filePath) {
                // 🔥 KRITIS: Cek keberadaan file di disk S3
                if (Storage::disk('s3')->exists($filePath)) {
                    // Ambil konten file dari S3 (Binary)
                    $fileContent = Storage::disk('s3')->get($filePath);
                    
                    // Tambahkan konten file langsung ke merger
                    $merger->addRaw($fileContent);
                    $filesAdded++;
                }
            }

            if ($filesAdded === 0) {
                return response()->json(['message' => 'File tidak ditemukan di Cloud Storage'], 404);
            }

            // 4. Proses Merger
            $output = $merger->merge();
            
            $downloadName = "Laporan_MCU_" . Str::slug($jadwal->nama_pasien) . "_" . $jadwal->no_antrean . ".pdf";

            // 5. Kembalikan response stream PDF
            return response($output)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', "attachment; filename=\"$downloadName\"");

        } catch (\Exception $e) {
            \Log::error("Gagal Merger API: " . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menggabungkan PDF',
                'error' => $e->getMessage() 
            ], 500);
        }
    }
}
