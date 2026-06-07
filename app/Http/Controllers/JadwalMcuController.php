<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalMcu; 
use App\Models\Karyawan; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class JadwalMcuController extends Controller
{
    /**
     * Menampilkan daftar jadwal MCU.
     */
    public function index(Request $request)
    {
        $tanggal_filter = $request->input('tanggal_filter');
        $status = $request->input('status');
        // 1. Tambahkan variabel untuk menangkap input search_sap
        $search_sap = $request->input('search_sap');

        $query = JadwalMcu::with('dokter', 'paketMcu', 'karyawan'); // Pastikan relasi karyawan dimuat

        // Apply date filter
        if (!empty($tanggal_filter)) {
            $query->whereDate('tanggal_mcu', $tanggal_filter);
        }

        // Apply status filter
        if (!empty($status)) {
            $query->where('status', $status);
        }

        // 2. Apply search filter untuk SAP
        if (!empty($search_sap)) {
            $query->where('no_sap', 'like', '%' . $search_sap . '%');
        }
        
        // Sort all results
        $jadwals = $query->orderBy('created_at', 'desc')->paginate(10); 

        // 3. QUERY UNTUK CARD ANTREAN POLI (HARI INI)
        $polis = \App\Models\Poli::with(['jadwalPoli' => function ($query) {
            $query->where('status', 'Waiting')
                  ->whereHas('jadwalMcu', function ($qJadwal) {
                      $qJadwal->where('status', 'Present')
                              ->whereDate('tanggal_mcu', \Carbon\Carbon::today());
                  })
                  ->with(['jadwalMcu.karyawan', 'jadwalMcu.pesertaMcu'])
                  ->orderBy('created_at', 'asc'); 
        }])->get();

        // MENGHITUNG KUOTA HARI INI
        $hariIni = \Carbon\Carbon::today();
        $kuotaTerisi = JadwalMcu::whereDate('tanggal_mcu', $hariIni)
                                ->where('status', '!=', 'Canceled')
                                ->count();
        $sisaKuota = 30 - $kuotaTerisi;
        // Pastikan sisa kuota tidak minus jika ada error data
        $sisaKuota = $sisaKuota < 0 ? 0 : $sisaKuota; // Cegah angka minus

        // 4. Kirimkan $search_sap ke view agar nilai tetap ada di input setelah cari
        return view('jadwal.index', [
            'jadwals' => $jadwals,
            'tanggal_filter' => $tanggal_filter, 
            'status' => $status,
            'search_sap' => $search_sap,
            'polis' => $polis,
            'kuotaTerisi' => $kuotaTerisi,
            'sisaKuota' => $sisaKuota,
        ]);
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
        //1.TAMBAHKAN LOGIKA PENGECEKAN KUOTA DI SINI
        $tanggal_mcu_cek = \Carbon\Carbon::parse($request->tanggal_mcu)->toDateString();

        $kuotaTerisi = JadwalMcu::whereDate('tanggal_mcu', $tanggal_mcu_cek)
                                ->where('status', '!=', 'Canceled') // Abaikan yang dibatalkan
                                ->count();

        if ($kuotaTerisi >= 30) {
            return back()
                ->with('error', 'Mohon maaf, kuota Medical Check Up untuk tanggal ' . \Carbon\Carbon::parse($tanggal_mcu_cek)->format('d-m-Y') . ' sudah penuh (30 orang). Silakan pilih hari lain.')
                ->withInput();
        }

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
            return redirect()->route('jadwal.index')->with('success', 'Medical Check up schedule successfully added!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while saving the schedule: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Memperbarui status jadwal.
     */
    public function updateStatus(Request $request, JadwalMcu $jadwal)
    {
        $validatedData = $request->validate([
            'status' => 'required|in:Present,Scheduled,Finished,Canceled',
        ]);
        
        $jadwal->update(['status' => $validatedData['status']]);

        // =========================================================
        // 🌟 SISTEM NOTIFIKASI KETIKA STATUS BERUBAH MENJADI FINISHED
        // =========================================================
        if ($jadwal->status === 'Finished') {
            
            $user = null;

            // Cari tahu akun login pasien berdasarkan tipe pasiennya
            if ($jadwal->tipe_pasien === 'ptst') {
                // Cari akun karyawan di model EmployeeLogin menggunakan no_sap / nik
                $user = \App\Models\EmployeeLogin::where('no_sap', $jadwal->no_sap)
                            ->orWhere('nik_karyawan', $jadwal->no_sap)
                            ->first();
            } else {
                // Cari akun pasien umum di model PesertaMcuLogin menggunakan nik_pasien
                $user = \App\Models\PesertaMcuLogin::where('nik_pasien', $jadwal->nik_pasien)->first();
            }

            if ($user) {
                // 1. Simpan riwayat notifikasi ke Database agar bisa dilihat di Inbox HP
                try {
                    // Catatan: Pastikan kolom user_id di tabel notifications siap menampung ID dari akun login ini
                    \App\Models\Notification::create([
                        'user_id' => $user->id,
                        'title' => 'Laporan MCU Selesai!',
                        'message' => "Medical Check Up Anda telah selesai. Laporan Gabungan lengkap dari semua poli sudah dapat diunduh.",
                        'is_read' => false, 
                    ]);
                } catch (\Exception $e) {
                    \Log::error("Gagal menyimpan notifikasi ke DB: " . $e->getMessage());
                }

                // 2. Kirim sinyal ke Firebase agar HP pasien berbunyi saat itu juga
                // (Pastikan tabel employee_logins dan peserta_mcu_logins memiliki kolom fcm_token)
                if (!empty($user->fcm_token)) {
                    $this->sendFcmNotification(
                        $user->fcm_token, 
                        "Laporan MCU Selesai!", 
                        "Medical Check Up Anda telah selesai. Laporan sudah dapat diunduh.",
                        $jadwal->id
                    );
                }
            }
        }
        // =========================================================
        
        return back()->with('success', 'Schedule status successfully updated!');
    }

    public function destroy(JadwalMcu $jadwal)
    {
        try {
            $jadwal->delete();
            return back()->with('success', 'Jadwal berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus jadwal: ' . $e->getMessage());
        }
    }
    
    public function edit(JadwalMcu $jadwal)
    {
        // Untuk form edit, kita arahkan ke view yang sama dengan create, 
        // namun dengan membawa data $jadwal untuk di-bind ke Livewire atau di-isi di form.
        // Asumsikan Livewire component yang menangani form dapat menerima $jadwal sebagai parameter.
        return view('jadwal.edit', compact('jadwal'));
    }

    /**
     * Memperbarui jadwal MCU di database.
     */
    public function update(Request $request, JadwalMcu $jadwal)
    {
        DB::beginTransaction();

        try {
            // Logika validasi data
            // Gunakan validasi yang sama seperti di store, disesuaikan untuk update
            $validatedData = [];
            $updateData = [
                'tanggal_mcu' => $request->tanggal_mcu,
                'dokter_id' => $request->dokter_id,
                'paket_mcus_id' => $request->paket_mcus_id,
            ];
            
            // Tambahkan validasi untuk form saat ini (Livewire)
            $validatedData = $request->validate([
                // Pastikan pasien sudah terpilih
                'karyawan_id' => 'nullable|exists:karyawans,id', // Di Livewire ini akan diisi jika tipe ptst
                'peserta_mcus_id' => 'nullable|exists:peserta_mcus,id', // Di Livewire ini akan diisi jika tipe non-ptst
                'tanggal_mcu' => 'required|date',
                'dokter_id' => 'required|exists:dokters,id',
                'paket_mcus_id' => 'required|exists:paket_mcus,id',
            ]);
            
            // Update data dasar jadwal
            $jadwal->update([
                'tanggal_mcu' => $validatedData['tanggal_mcu'],
                'dokter_id' => $validatedData['dokter_id'],
                'paket_mcus_id' => $validatedData['paket_mcus_id'],
                // Pastikan karyawan_id atau peserta_mcus_id juga di update jika ada perubahan pasien di form edit
                'karyawan_id' => $validatedData['karyawan_id'] ?? null,
                'peserta_mcus_id' => $validatedData['peserta_mcus_id'] ?? null,
                // Kolom lain (no_antrean, status) biasanya tidak diupdate di sini
            ]);


            DB::commit();
            return redirect()->route('jadwal.index')->with('success', 'Jadwal Medical Check up berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memperbarui jadwal: ' . $e->getMessage())->withInput();
        }
    }


    // =========================================================
    // 🌟 PRIVATE FUNCTION UNTUK MENGIRIM PUSH NOTIFICATION FCM
    // =========================================================
    private function sendFcmNotification($fcmToken, $title, $body, $jadwalId)
    {
        try {
            // 1. Tunjuk lokasi file JSON yang sudah kamu simpan di storage/app/
            $credentialsFilePath = storage_path('app/firebase-auth.json');

            // 2. Buat Akses Token (Google API)
            $client = new \Google\Client();
            $client->setAuthConfig($credentialsFilePath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            
            $tokenArray = $client->fetchAccessTokenWithAssertion();
            $accessToken = $tokenArray['access_token'];

            // 3. ID Proyek Firebase milikmu
            $projectId = 'stmc-62e04';
            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            // 4. Format Payload FCM v1
            $data = [
                "message" => [
                    "token" => $fcmToken,
                    "notification" => [
                        "title" => $title,
                        "body" => $body
                    ],
                    "android" => [
                        "notification" => [
                            "sound" => "default",
                            "channel_id" => "channel_panggilan_poli"
                        ]
                    ],
                    "data" => [
                        "type" => "mcu_finished",
                        "jadwal_id" => (string) $jadwalId,
                        // 🌟 TAMBAHAN LINK AGAR TOMBOL DI FLUTTER MUNCUL
                        "link" => "https://stmc-health.my.id" 
                    ]
                ]
            ];

            // 5. Tembakkan notifikasi
            \Illuminate\Support\Facades\Http::withToken($accessToken)->post($url, $data);

        } catch (\Exception $e) {
            \Log::error("FCM v1 Send Error: " . $e->getMessage());
        }
    }

}