<?php

namespace App\Http\Controllers\Api;

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
            $noAntrean = JadwalMcu::whereDate('tanggal_mcu', $tanggal)->count() + 1;

            $jadwal = JadwalMcu::create([
                'qr_code_id'       => (string) Str::uuid(),
                'tanggal_mcu'      => $tanggal,
                'tanggal_pendaftaran' => now()->toDateString(),
                'paket_mcus_id'    => $request->paket_mcu,
                'dokter_id'        => null,
                'no_antrean'       => 'A' . str_pad($noAntrean, 3, '0', STR_PAD_LEFT),
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
        try {
            $jadwal = JadwalMcu::with('jadwalPoli')->findOrFail($id);
            $files = $jadwal->jadwalPoli->whereNotNull('file_path')->pluck('file_path')->toArray();

            if (empty($files)) {
                return response()->json(['message' => 'File tidak tersedia'], 404);
            }

            $merger = new Merger();
            foreach ($files as $fileName) {
                $path = storage_path("app/public/pdf_reports/" . $fileName);
                if (file_exists($path)) $merger->addFile($path);
            }

            $output = $merger->merge();
            $name = "Laporan_MCU_" . Str::slug($jadwal->nama_pasien) . ".pdf";

            return response($output)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', "attachment; filename=\"$name\"");

        } catch (Exception $e) {
            return response()->json(['message' => 'Gagal menggabungkan PDF'], 500);
        }
    }
}
