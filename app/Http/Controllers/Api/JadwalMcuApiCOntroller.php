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
use Carbon\Carbon;

class JadwalMcuApiController extends Controller
{
    public function store(Request $request)
    {
        $loginUser = auth()->user(); // ⬅️ LOGIN MODEL (EmployeeLogin / PesertaMcuLogin)

        if (!$loginUser) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        try {
            $request->validate([
                'tanggal_mcu' => 'required|date|after_or_equal:today',
                'paket_mcu' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        }

        /** ===============================
         *  TENTUKAN USER ASLI
         *  =============================== */
        if ($loginUser instanceof EmployeeLogin) {
            $user = $loginUser->karyawan;
            $checkColumn = 'karyawan_id';
        } else {
            $user = $loginUser->pasien;
            $checkColumn = 'peserta_mcus_id';
        }

        if (!$user) {
            return response()->json(['message' => 'Profil tidak ditemukan'], 404);
        }

        if (
            JadwalMcu::where($checkColumn, $user->id)
                ->where('status', 'Scheduled')
                ->exists()
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki jadwal MCU aktif.'
            ], 409);
        }

        try {
            $dokter = Dokter::inRandomOrder()->firstOrFail();

            $tanggalMcu = Carbon::parse($request->tanggal_mcu)->toDateString();
            $noAntrean = JadwalMcu::whereDate('tanggal_mcu', $tanggalMcu)->count() + 1;

            $data = [
                'qr_code_id' => (string) Str::uuid(),
                'tanggal_mcu' => $tanggalMcu,
                'paket_mcus_id' => $request->paket_mcu,
                'dokter_id' => $dokter->id,
                'no_antrean' => $noAntrean,
                'status' => 'Scheduled',
                'nama_pasien' => $user->nama ?? $user->nama_lengkap,
                'nik_pasien' => $user->nik ?? $user->nik_pasien,
                'perusahaan_asal' => $user->perusahaan ?? 'Pribadi',
                'karyawan_id' => $loginUser instanceof EmployeeLogin ? $user->id : null,
                'peserta_mcus_id' => $loginUser instanceof PesertaMcuLogin ? $user->id : null,
            ];

            $jadwal = JadwalMcu::create($data);

            return response()->json([
                'success' => true,
                'qr_code_id' => $jadwal->qr_code_id
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan jadwal'
            ], 500);
        }
    }

    public function getRiwayatByUser()
    {
        $loginUser = auth()->user();

        if (!$loginUser) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if ($loginUser instanceof EmployeeLogin) {
            if (!$loginUser->karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Relasi karyawan tidak ditemukan'
                ], 404);
            }

            $column = 'karyawan_id';
            $userId = $loginUser->karyawan->id;

        } elseif ($loginUser instanceof PesertaMcuLogin) {
            if (!$loginUser->pasien) {
                return response()->json([
                    'success' => false,
                    'message' => 'Relasi peserta MCU tidak ditemukan'
                ], 404);
            }

            $column = 'peserta_mcus_id';
            $userId = $loginUser->pasien->id;

        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tipe user tidak dikenali'
            ], 403);
        }

        $data = JadwalMcu::where($column, $userId)
            ->with(['dokter', 'paketMcu'])
            ->orderByDesc('tanggal_mcu')
            ->get();

        return response()->json([
            'success' => true,
            'data_aktif' => JadwalMcuResource::collection(
                $data->whereIn('status', ['Scheduled', 'Present'])
            ),
            'data_selesai' => JadwalMcuResource::collection(
                $data->whereIn('status', ['Finished', 'Canceled'])
            ),
        ]);
    }
}
