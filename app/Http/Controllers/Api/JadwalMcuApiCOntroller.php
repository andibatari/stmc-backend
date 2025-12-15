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

        // 2. Cek Ketersediaan Slot & Duplikasi (Logika ini tetap benar)
        if (JadwalMcu::where('user_id', $user->id)->where('status', 'Scheduled')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki jadwal MCU yang aktif. Batalkan jadwal yang ada terlebih dahulu.'
            ], 409);
        }

        // 3. Simpan Pengajuan
        try {
            // [KOREKSI LOGIKA]: Tentukan Dokter ID Berdasarkan Tanggal (Simulasi Shift)
            $tanggal = Carbon::parse($request->tanggal_mcu);
            $hari = $tanggal->day;
            
            // Asumsi: Dokter dengan ID 1 untuk hari genap, ID 2 untuk hari ganjil.
            // Anda harus mengganti ini dengan logika bisnis shift Anda yang sebenarnya.
            $dokterIdUntukJadwal = ($hari % 2 == 0) ? 1 : 2; 

            JadwalMcu::create([
                'user_id' => $user->id,
                'tanggal_jadwal' => $request->tanggal_mcu,
                'paket_mcu' => $request->paket_mcu,
                'dokter_id' => $dokterIdUntukJadwal, // <--- MENGGUNAKAN ID DARI DATABASE
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
        $user = $request->user();

        // [KOREKSI PENTING]: Eager load relasi Dokter untuk mendapatkan nama dokter
        // Asumsi: Model JadwalMcu memiliki relasi belongsTo ke Model Dokter bernama 'dokter'
        $riwayat = JadwalMcu::where('user_id', $user->id)
                            ->with('dokter') // <--- EAGER LOADING DOKTER
                            ->orderBy('tanggal_jadwal', 'desc')
                            ->get();

        $aktif = $riwayat->filter(fn($j) => in_array($j->status, ['Scheduled', 'Present']));
        $selesai = $riwayat->filter(fn($j) => in_array($j->status, ['Finished', 'Canceled']));

        return response()->json([
            'success' => true,
            // Data dikirim ke Resource, yang harusnya menampilkan NAMA DOKTER
            'data_aktif' => JadwalMcuResource::collection($aktif), 
            'data_selesai' => JadwalMcuResource::collection($selesai),
        ], 200);
    }
}