<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalMcu; 
use App\Models\Karyawan; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // <-- Tambahkan baris ini


class JadwalMcuController extends Controller
{
    /**
     * Menampilkan daftar jadwal MCU.
     */
    public function index(Request $request)
    {
        // Mendapatkan nilai filter dari request
        $tanggal_filter = $request->input('tanggal_filter', Carbon::today()->format('Y-m-d'));
        $tipe_pasien = $request->input('tipe_pasien');
        $status = $request->input('status');
        
        // Membangun query dasar
        $jadwals = JadwalMcu::with('karyawan')
                            ->when($tanggal_filter, function ($query, $tanggal_filter) {
                                return $query->whereDate('tanggal_mcu', $tanggal_filter);
                            })
                            ->when($tipe_pasien, function ($query, $tipe_pasien) {
                                return $query->where('tipe_pasien', $tipe_pasien);
                            })
                            ->when($status, function ($query, $status) {
                                return $query->where('status', $status);
                            })
                            ->paginate(15);
                            
        // Mengirimkan semua variabel filter ke view
        return view('jadwal.index', compact('jadwals', 'tanggal_filter', 'tipe_pasien', 'status'));
    }

    /**
     * Menampilkan formulir untuk membuat jadwal baru.
     */
    public function create()
    {
        return view('jadwal.create');
    }

    /**
     * Menyimpan jadwal MCU baru ke database.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Logika nomor antrean otomatis
            $tanggal_mcu = \Carbon\Carbon::parse($request->tanggal_mcu);
            $prefix = $tanggal_mcu->format('Ymd');
            $lastQueue = JadwalMcu::where('tanggal_mcu', $tanggal_mcu)
                                  ->orderBy('no_antrean', 'desc')
                                  ->first();
            
            $nextQueueNumber = 1;
            if ($lastQueue) {
                $lastNumber = intval(substr($lastQueue->no_antrean, -3));
                $nextQueueNumber = $lastNumber + 1;
            }
            
            $antrean = $prefix . '_' . str_pad($nextQueueNumber, 3, '0', STR_PAD_LEFT);

            if ($request->tipe_pasien === 'ptst') {
                $validatedData = $request->validate([
                    'karyawan_id' => 'required|exists:karyawans,id',
                    'tanggal_mcu' => 'required|date',
                ]);

                $karyawan = Karyawan::findOrFail($validatedData['karyawan_id']);

                JadwalMcu::create([
                    'tipe_pasien' => 'ptst',
                    'karyawan_id' => $karyawan->id,
                    'no_sap' => $karyawan->no_sap,
                    'tanggal_mcu' => $validatedData['tanggal_mcu'],
                    'tanggal_pendaftaran' => now(),
                    'no_antrean' => $antrean, // Simpan nomor antrean
                    'status' => 'Scheduled',
                ]);

            } elseif ($request->tipe_pasien === 'non-ptst') {
                $validatedData = $request->validate([
                    'nama_pasien' => 'required|string|max:255',
                    'no_identitas' => 'required|string',
                    'perusahaan_afiliasi' => 'required|string|max:255',
                    'tanggal_lahir' => 'required|date',
                    'tanggal_mcu' => 'required|date',
                ]);

                JadwalMcu::create([
                    'tipe_pasien' => 'non-ptst',
                    'nama_pasien' => $validatedData['nama_pasien'],
                    'no_identitas' => $validatedData['no_identitas'],
                    'perusahaan_afiliasi' => $validatedData['perusahaan_afiliasi'],
                    'tanggal_lahir' => $validatedData['tanggal_lahir'],
                    'tanggal_mcu' => $validatedData['tanggal_mcu'],
                    'tanggal_pendaftaran' => now(),
                    'no_antrean' => $antrean, // Simpan nomor antrean
                    'status' => 'Scheduled',
                ]);

            } else {
                return back()->withErrors(['tipe_pasien' => 'Tipe pasien tidak valid.'])->withInput();
            }

            DB::commit();
            return redirect()->route('jadwal.index')->with('success', 'Jadwal MCU berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan jadwal: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Memperbarui status jadwal.
     */
    public function updateStatus(Request $request, JadwalMcu $jadwal)
    {
        $validatedData = $request->validate([
            'status' => 'required|in:Waited,Scheduled,Finished,Canceled',
        ]);
        
        $jadwal->update(['status' => $validatedData['status']]);
        
        return back()->with('success', 'Status jadwal berhasil diperbarui!');
    }
}