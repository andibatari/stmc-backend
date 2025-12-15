<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Jika perlu query raw
use Illuminate\Validation\ValidationException;
// Import Model yang dibutuhkan (misal: JadwalMcu, User/Karyawan)
use App\Models\JadwalMcu; // Ganti dengan nama Model Jadwal MCU Anda
use App\Models\Karyawan;
use App\Models\PesertaMcu;
use App\Models\Dokter; // Asumsi ada model Dokter untuk relasi
use App\Http\Resources\JadwalMcuResource; // Asumsi Anda menggunakan Resource untuk format output
use Carbon\Carbon;

class JadwalMcuApiController extends Controller
{
    /**
     * Menyimpan pengajuan jadwal MCU dari pengguna.
     * Endpoint: POST /api/jadwal-mcu/ajukan
     */
    public function store(Request $request)
    {
        $user = $request->user(); // Karyawan yang sedang login

        // 1. Validasi Input
        try {
            $request->validate([
                'tanggal_mcu' => 'required|date|after_or_equal:today',
                'paket_mcu' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: Cek format tanggal atau paket MCU.',
                'errors' => $e->errors(),
            ], 422);
        }

        // --- PENENTUAN KOLOM PENCARIAN BERDASARKAN JENIS USER ---
        $isKaryawan = $user instanceof \App\Models\Karyawan;
        $checkColumn = $isKaryawan ? 'karyawan_id' : 'peserta_mcus_id';

        // 2. Cek Ketersediaan Slot & Duplikasi
        if (JadwalMcu::where($checkColumn, $user->id)->where('status', 'Scheduled')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki jadwal MCU yang aktif. Batalkan jadwal yang ada terlebih dahulu.'
            ], 409);
        }

        // 3. Simpan Pengajuan
        try {
            // A. Penentuan Dokter Piket (Logika ini tetap di sisi server)
            $dokterPiket = Dokter::inRandomOrder()->first();

            if (!$dokterPiket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menentukan jadwal. Tidak ada data dokter yang tersedia.'
                ], 500);
            }

            // B. Penentuan Antrean & Data Dasar
            $tanggalMcu = Carbon::parse($request->tanggal_mcu)->toDateString();
            $noAntrean = JadwalMcu::whereDate('tanggal_mcu', $tanggalMcu)->count() + 1;
            $userId = $user->id;

            // C. Persiapan Data untuk CREATE
            $dataToCreate = [
                // Data Pengajuan dari Request
                'tanggal_mcu' => $tanggalMcu,
                'paket_mcus_id' => $request->paket_mcu, // Menggunakan nilai dari request
                
                // Data Penetapan Backend
                'dokter_id' => $dokterPiket->id, 
                'no_antrean' => $noAntrean,
                'status' => 'Scheduled',
            ];

            // D. Pengisian Data Karyawan/Peserta MCU (Disesuaikan dengan provider guard)
            if ($isKaryawan) {
                // Jika User adalah Karyawan
                $dataToCreate['karyawan_id'] = $userId;
                $dataToCreate['peserta_mcus_id'] = null;
                
                // Asumsi kolom ada di Model Karyawan:
                $dataToCreate['no_sap'] = $user->no_sap ?? $user->nik; 
                $dataToCreate['nama_pasien'] = $user->nama ?? $user->name;
                $dataToCreate['nik_pasien'] = $user->nik;
                $dataToCreate['perusahaan_asal'] = $user->perusahaan ?? 'PT. STMC';
            } else {
                // Jika User adalah Peserta MCU (Non-Karyawan)
                $dataToCreate['peserta_mcus_id'] = $userId;
                $dataToCreate['karyawan_id'] = null;
                
                // Asumsi kolom ada di Model PesertaMcu:
                $dataToCreate['no_sap'] = null; 
                $dataToCreate['nama_pasien'] = $user->nama ?? $user->name;
                $dataToCreate['nik_pasien'] = $user->nik;
                $dataToCreate['perusahaan_asal'] = $user->perusahaan ?? 'Pribadi';
            }
            
            JadwalMcu::create($dataToCreate);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan jadwal MCU berhasil. Mohon tunggu konfirmasi.'
            ], 201);

        } catch (\Exception $e) {
            // Jika Anda masih mendapatkan 500, tambahkan $e->getMessage() untuk debugging:
            // return response()->json([
            //     'success' => false,
            //     'message' => 'Gagal menyimpan pengajuan jadwal. Error: ' . $e->getMessage(),
            // ], 500);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan pengajuan jadwal.'
            ], 500);
        }
    }

    /**
     * Mengambil semua riwayat jadwal MCU untuk pengguna yang sedang login.
     * Endpoint: GET /api/jadwal-mcu/riwayat
     */
    public function getRiwayatByUser(Request $request)
    {
        try {
            $user = $request->user();

            // 1. Tentukan kolom ID yang akan digunakan
            $column = null;
            $userId = null;
            
            if ($user instanceof Karyawan) {
                $column = 'karyawan_id';
                $userId = $user->id;
            } elseif ($user instanceof PesertaMcu) {
                $column = 'peserta_mcus_id';
                $userId = $user->id;
            } else {
                // Jika user terautentikasi tetapi bukan Karyawan atau PesertaMcu 
                // (misal: Guard baru yang belum ditangani), kembalikan 403 atau data kosong.
                return response()->json([
                    'success' => false,
                    'message' => 'Tipe pengguna tidak dikenali untuk mengakses riwayat ini.'
                ], 403);
            }

            // 2. Query data riwayat menggunakan kolom yang ditentukan
            $riwayat = JadwalMcu::where($column, $userId)
                                ->with('dokter') // Memastikan relasi dokter ada
                                ->orderBy('tanggal_mcu', 'desc')
                                ->get();

            $aktif = $riwayat->filter(fn($j) => in_array($j->status, ['Scheduled', 'Present']));
            $selesai = $riwayat->filter(fn($j) => in_array($j->status, ['Finished', 'Canceled']));

            return response()->json([
                'success' => true,
                // Data dikirim ke Resource, yang harusnya menampilkan NAMA DOKTER
                'data_aktif' => JadwalMcuResource::collection($aktif), 
                'data_selesai' => JadwalMcuResource::collection($selesai),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat jadwal MCU. Cek log server untuk detail exception.'
            ], 500);
        }
    }
}