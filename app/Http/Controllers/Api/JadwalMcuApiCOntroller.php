<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Jika perlu query raw
use Illuminate\Validation\ValidationException;
// Import Model yang dibutuhkan (misal: JadwalMcu, User/Karyawan)
use App\Models\JadwalMcu; // Ganti dengan nama Model Jadwal MCU Anda
use App\Models\Karyawan;
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

        // 2. Cek Ketersediaan Slot & Duplikasi (Logika ini tetap benar)
        if (JadwalMcu::where('user_id', $user->id)->where('status', 'Scheduled')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki jadwal MCU yang aktif. Batalkan jadwal yang ada terlebih dahulu.'
            ], 409);
        }

        // 3. Simpan Pengajuan
        try {
            // --- LOGIKA PENENTUAN DOKTER PIKET DARI DATABASE ---
            $dokterPiket = Dokter::inRandomOrder()->first();

            if (!$dokterPiket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menentukan jadwal. Tidak ada data dokter yang tersedia.'
                ], 500); // 500 Internal Server Error jika tabel Dokter kosong
            }

            // Mendapatkan no_antrean (Simulasi: hitung total jadwal hari ini + 1)
            $tanggalMcu = Carbon::parse($request->tanggal_mcu)->toDateString();
            $noAntrean = JadwalMcu::whereDate('tanggal_mcu', $tanggalMcu)->count() + 1;

            JadwalMcu::create([
                // Data Karyawan (ASUMSI $user memiliki kolom ini atau Anda dapat mengambilnya dari $user)
                'peserta_mcus_id' => $user->id, // Asumsi ini adalah ID User/Karyawan
                'karyawan_id' => $user->id, // Asumsi ID Karyawan sama dengan User ID
                'no_sap' => $user->no_sap ?? $user->nik, // Asumsi kolom ini ada di Model $user
                'nama_pasien' => $user->nama ?? $user->name, // Asumsi kolom ini ada di Model $user
                'nik_pasien' => $user->nik,
                'perusahaan_asal' => $user->perusahaan ?? 'PT. STMC', // Asumsi

                // Data Pengajuan
                'tanggal_mcu' => $tanggalMcu,
                'paket_mcus_id' => $request->paket_mcu, // Jika Anda menyimpan ID Paket
                // Jika kolom di DB adalah 'paket_mcu' (string), ganti menjadi: 'paket_mcu' => $request->paket_mcu,
                
                // Data Penetapan Backend
                'dokter_id' => $dokterPiket->id, // <--- ID DOKTER DARI DB DOKTER
                'no_antrean' => $noAntrean, // Nomor antrean otomatis
                'status' => 'Scheduled',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan jadwal MCU berhasil. Mohon tunggu konfirmasi.'
            ], 201);

        } catch (\Exception $e) {
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

            // [KOREKSI PENTING]: Eager load relasi Dokter untuk mendapatkan nama dokter
            // Asumsi: Model JadwalMcu memiliki relasi belongsTo ke Model Dokter bernama 'dokter'
            $riwayat = JadwalMcu::where(function ($query) use ($user) {
                // Jika user adalah instance dari Model Karyawan
                if ($user instanceof \App\Models\Karyawan) {
                    $query->where('karyawan_id', $user->id); 
                } 
                // Jika user adalah instance dari Model PesertaMcu (Non-Karyawan)
                elseif ($user instanceof \App\Models\PesertaMcu) {
                    $query->where('peserta_mcus_id', $user->id); // Asumsi ini FK untuk non-karyawan
                }
            })
            ->with('dokter') 
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
                'message' => 'Gagal mengambil riwayat jadwal MCU.'
            ], 500);
        }
    }
}