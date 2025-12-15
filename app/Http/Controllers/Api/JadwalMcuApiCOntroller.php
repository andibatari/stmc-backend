<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Jika perlu query raw
use Illuminate\Validation\ValidationException;
// Import Model yang dibutuhkan (misal: JadwalMcu, User/Karyawan)
use App\Models\JadwalMcu; // Ganti dengan nama Model Jadwal MCU Anda
use App\Http\Resources\JadwalMcuResource; // Asumsi Anda menggunakan Resource untuk format output

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

        // 2. Cek Ketersediaan Slot & Duplikasi
        // Logika bisnis: Cek apakah user sudah mengajukan jadwal aktif atau jadwal penuh
        // Di sini kita hanya cek apakah sudah ada jadwal 'Scheduled'
        $existingJadwal = JadwalMcu::where('user_id', $user->id) // Asumsi Model punya foreign key user_id
                                    ->where('status', 'Scheduled')
                                    ->exists();
        
        if ($existingJadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki jadwal MCU yang aktif. Batalkan jadwal yang ada terlebih dahulu.'
            ], 409); // 409 Conflict
        }

        // 3. Simpan Pengajuan
        try {
            // Asumsi: Kita tentukan dokter piket berdasarkan hari atau sistem internal
            $dokterPiket = 'dr. ' . ($request->tanggal_mcu % 2 == 0 ? 'Nurul' : 'Iwan');

            $jadwal = JadwalMcu::create([
                'user_id' => $user->id,
                'tanggal_jadwal' => $request->tanggal_mcu,
                'paket_mcu' => $request->paket_mcu,
                'dokter_piket' => $dokterPiket, // Data ditentukan di backend
                'status' => 'Scheduled',
                // 'keterangan_lain' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan jadwal MCU berhasil. Mohon tunggu konfirmasi.'
            ], 201); // 201 Created

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
        $user = $request->user();

        // Ambil semua jadwal, diurutkan dari yang terbaru (tanggal paling depan)
        $riwayat = JadwalMcu::where('user_id', $user->id)
                            ->orderBy('tanggal_jadwal', 'desc')
                            ->get();

        // Kelompokkan menjadi Aktif (Scheduled/Present) dan Selesai (Finished/Canceled)
        $aktif = $riwayat->filter(fn($j) => in_array($j->status, ['Scheduled', 'Present']));
        $selesai = $riwayat->filter(fn($j) => in_array($j->status, ['Finished', 'Canceled']));

        return response()->json([
            'success' => true,
            'data_aktif' => JadwalMcuResource::collection($aktif), // Gunakan Resource
            'data_selesai' => JadwalMcuResource::collection($selesai),
        ], 200);
    }
}