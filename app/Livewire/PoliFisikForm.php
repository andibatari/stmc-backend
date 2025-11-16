<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\JadwalPoli;
use App\Models\Karyawan;
use App\Models\PesertaMcu;
use App\Models\Dokter;
use App\Models\FisikResult;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;


class PoliFisikForm extends Component
{
    // --- Properti dari Parent (DIISI OTOMATIS OLEH LIVEWIRE) ---
    public $jadwalId; // ID Jadwal MCU
    public $poliData; // Model JadwalPoli
    // ... (Properti lainnya) ...
    public $patient;
    public $fisikResult;
    public $listDokter;
    public $dokterId;
    public $isKaryawan = false;
    public $instansiPasien;
    public $dataFisik;
    public $kesimpulan;
    public $keterangan;

    // Default nilai untuk dropdown/radio
    protected $defaultOption = 'Dalam batas normal';
    protected $defaultRefleks = '+2';
    protected $defaultPajanan = 'Tidak';

    protected $rules = [
        // ... (Rules tetap sama) ...
        'dokterId' => 'required|exists:dokters,id',
        'dataFisik.tanda_vital.tinggi_badan' => 'required|numeric|min:10',
        'dataFisik.tanda_vital.berat_badan' => 'required|numeric|min:5',
        'dataFisik.tanda_vital.tekanan_darah_sistol' => 'required|integer|min:50|max:300',
        'dataFisik.tanda_vital.tekanan_darah_diastol' => 'required|integer|min:30|max:200',
        'dataFisik.tanda_vital.nadi' => 'required|integer|min:40|max:180',
        'dataFisik.tanda_vital.pernafasan' => 'required|integer|min:5|max:40',
        'dataFisik.tanda_vital.suhu' => 'required|numeric|min:35|max:42',
        'dataFisik.tanda_vital.spo2' => 'required|numeric|min:90|max:100',

        // Kepala dan Leher
        'dataFisik.kepala.anemi' => 'required|string', 'dataFisik.kepala.ikterus' => 'required|string',
        'dataFisik.kepala.dyspnoe' => 'required|string', 'dataFisik.kepala.cyanosis' => 'required|string',
        'dataFisik.kepala.refleks_pupil' => 'required|string', 'dataFisik.kepala.tonsil_kanan' => 'required|string',
        'dataFisik.kepala.tonsil_kiri' => 'required|string', 'dataFisik.kepala.serumen' => 'required|string',
        'dataFisik.kepala.membran_timpani' => 'required|string',
        'dataFisik.leher.jvp' => 'required|string', 'dataFisik.leher.tiroid' => 'required|string',
        'dataFisik.leher.kelenjar_getah_bening' => 'required|string',

        // KRITIS: Aturan validasi baru untuk data mata (input teks)
        'dataFisik.mata.visus_kanan' => 'required|string|max:100',
        'dataFisik.mata.visus_kiri' => 'required|string|max:100',
        'dataFisik.mata.konjungtiva' => 'nullable|string|max:100',
        'dataFisik.mata.sklera' => 'nullable|string|max:100',
        'dataFisik.mata.kesimpulan_mata' => 'nullable|string|max:255', // Input kesimpulan mata
        
        // Dada & Paru
        'dataFisik.dada.bunyi_jantung_1' => 'required|string', 'dataFisik.dada.bunyi_jantung_2' => 'required|string',
        'dataFisik.paru.bunyi_nafas' => 'required|string', 'dataFisik.paru.bunyi_nafas_tambahan' => 'required|string',

        // Abdomen & Ekstremitas
        'dataFisik.abdomen.peristaltik' => 'required|string', 'dataFisik.abdomen.nyeri_tekan' => 'required|string',
        'dataFisik.abdomen.massa' => 'required|string', 'dataFisik.abdomen.hati' => 'required|string',
        'dataFisik.abdomen.limpa' => 'required|string',
        'dataFisik.ekstremitas.ekstremitas' => 'required|string',
        'dataFisik.ekstremitas.refleks_fisiologis_kanan' => 'required|string', 'dataFisik.ekstremitas.refleks_fisiologis_kiri' => 'required|string',
        'dataFisik.ekstremitas.refleks_patologis_kanan' => 'required|string', 'dataFisik.ekstremitas.refleks_patologis_kiri' => 'required|string',

        // RIWAYAT PAJANAN PEKERJAAN
        'dataFisik.pajanan.fisik.kebisingan' => 'required|string', 'dataFisik.pajanan.fisik.suhu_panas' => 'required|string',
        'dataFisik.pajanan.fisik.suhu_dingin' => 'required|string', 'dataFisik.pajanan.fisik.radiasi_non_pengion' => 'required|string',
        'dataFisik.pajanan.fisik.radiasi_pengion' => 'required|string', 'dataFisik.pajanan.fisik.getaran_lokal' => 'required|string',
        'dataFisik.pajanan.fisik.getaran_seluruh_tubuh' => 'required|string', 'dataFisik.pajanan.fisik.ketinggian' => 'required|string',
        'dataFisik.pajanan.fisik.lain_fisik' => 'required|string',
        'dataFisik.pajanan.kimia.debu_anorganik' => 'required|string', 'dataFisik.pajanan.kimia.debu_organic' => 'required|string',
        'dataFisik.pajanan.kimia.asap' => 'required|string', 'dataFisik.pajanan.kimia.logam_berat' => 'required|string',
        'dataFisik.pajanan.kimia.iritan_asam' => 'required|string', 'dataFisik.pajanan.kimia.iritan_basa' => 'required|string',
        'dataFisik.pajanan.kimia.cairan_pembersih' => 'required|string', 'dataFisik.pajanan.kimia.pestisida' => 'required|string',
        'dataFisik.pajanan.kimia.uap_logam' => 'required|string', 'dataFisik.pajanan.kimia.lain_kimia' => 'required|string',
        'dataFisik.pajanan.biologi.bakteri' => 'required|string', 'dataFisik.pajanan.biologi.darah' => 'required|string',
        'dataFisik.pajanan.biologi.nyamuk' => 'required|string', 'dataFisik.pajanan.biologi.limbah' => 'required|string',
        'dataFisik.pajanan.biologi.lain_biologi' => 'required|string',
        'dataFisik.pajanan.psikologi.beban_kerja' => 'required|string', 'dataFisik.pajanan.psikologi.pekerjaan_tidak_sesuai' => 'required|string',
        'dataFisik.pajanan.psikologi.ketidakjelasan_tugas' => 'required|string', 'dataFisik.pajanan.psikologi.hambatan_jenjang_karir' => 'required|string',
        'dataFisik.pajanan.psikologi.bekerja_giliran' => 'required|string', 'dataFisik.pajanan.psikologi.konflik_teman_sekerja' => 'required|string',
        'dataFisik.pajanan.psikologi.konflik_keluarga' => 'required|string', 'dataFisik.pajanan.psikologi.lain_psikologi' => 'required|string',
        'dataFisik.pajanan.ergonomis.gerakan_berulang' => 'required|string', 'dataFisik.pajanan.ergonomis.angkat_berat' => 'required|string',
        'dataFisik.pajanan.ergonomis.duduk_lama' => 'required|string', 'dataFisik.pajanan.ergonomis.berdiri_lama' => 'required|string',
        'dataFisik.pajanan.ergonomis.posisi_tidak_ergonomis' => 'required|string', 'dataFisik.pajanan.ergonomis.pencahayaan_tidak_sesuai' => 'required|string',
        'dataFisik.pajanan.ergonomis.bekerja_layar_monitor' => 'required|string', 'dataFisik.pajanan.ergonomis.lain_ergonomis' => 'required|string',

        'kesimpulan' => 'nullable|string',
        'keterangan' => 'nullable|string',
    ];

    public function mount() // Hapus semua parameter di sini!
    {
        try { $this->listDokter = Dokter::all(); } catch (\Exception $e) { $this->listDokter = collect([]); }

        // KRITIS: Akses properti melalui $this->
        $jadwalMcu = $this->poliData->jadwalMcu ?? null;
        $karyawanId = $jadwalMcu->karyawan_id ?? null;
        $pesertaMcuId = $jadwalMcu->peserta_mcus_id ?? null;

        // --- Muat Pasien & Tentukan Tipe ---
        if ($karyawanId) {
            $this->patient = Karyawan::with('unitKerja')->find($karyawanId);
            $this->isKaryawan = true;
        } elseif ($pesertaMcuId) {
            $this->patient = PesertaMcu::find($pesertaMcuId);
            $this->isKaryawan = false;
        } else {
            // Jika pasien tidak ditemukan, set ke objek kosong
            $this->patient = (object)['nama_lengkap' => 'Pasien Tidak Ditemukan', 'tanggal_lahir' => Carbon::now()->toDateString(), 'jenis_kelamin' => 'N/A', 'tinggi_badan' => 165, 'berat_badan' => 60,];
            $this->isKaryawan = false;
        }

        // --- Standarisasi Data Pasien & Instansi ---
        if ($this->patient && isset($this->patient->tanggal_lahir)) {
            if ($this->isKaryawan) {
                $unitKerja = $this->patient->unitKerja->nama_unit_kerja ?? 'Unit Kerja Tidak Diketahui';
                $this->instansiPasien = "PT SEMEN TONASA (Unit: {$unitKerja})";
                $this->patient->nama_lengkap = $this->patient->nama_karyawan ?? $jadwalMcu->nama_pasien ?? 'N/A';
                $this->patient->nik_pasien = $this->patient->nik_karyawan ?? 'N/A';
                $this->patient->no_sap = $this->patient->id ?? 'N/A';
            } else {
                $perusahaanAsal = $this->patient->perusahaan_asal ?? $jadwalMcu->perusahaan_asal ?? null;
                $this->instansiPasien = $perusahaanAsal ?? 'NON-KARYAWAN/UMUM';
                $this->patient->nama_lengkap = $this->patient->nama_lengkap ?? $jadwalMcu->nama_pasien ?? 'N/A';
                $this->patient->nik_pasien = $this->patient->nik_pasien ?? 'N/A';
                $this->patient->no_sap = 'N/A';
            }
            // Data umum
            $this->patient->tanggal_lahir = $this->patient->tanggal_lahir ?? Carbon::now()->toDateString();
            $this->patient->jenis_kelamin = $this->patient->jenis_kelamin ?? 'PRIA';
            $this->patient->tinggi_badan = $this->patient->tinggi_badan ?? 165;
            $this->patient->berat_badan = $this->patient->berat_badan ?? 60;
        } else {
            $this->patient = (object)['nama_lengkap' => 'Pasien Tidak Ditemukan', 'tanggal_lahir' => Carbon::now()->toDateString(), 'jenis_kelamin' => 'N/A', 'tinggi_badan' => 165, 'berat_badan' => 60,];
            $this->instansiPasien = 'N/A';
        }

        // --- Muat Data Lama atau Inisialisasi Default ---
        // KRITIS: Pastikan $this->poliData adalah instance valid sebelum mengakses ID
        if (!($this->poliData instanceof JadwalPoli) || !isset($this->poliData->id)) {
            $this->fisikResult = new FisikResult();
            // Jika ID tidak valid, kita tidak bisa memuat data lama, tapi kita set objek baru.
        } else {
            $this->fisikResult = FisikResult::firstOrNew(['jadwal_poli_id' => $this->poliData->id]);
        }


        if ($this->fisikResult->exists) {
            $this->dataFisik = $this->fisikResult->data_fisik;
            $this->kesimpulan = $this->fisikResult->kesimpulan;
            $this->keterangan = $this->fisikResult->keterangan;
            $this->dokterId = $this->fisikResult->dokter_id;
        } else {
            $this->dataFisik = $this->getDefaultDataFisik();
            $this->dokterId = null;
        }
    }

    // Hitung BMI dan Kategorinya
    public function getBmiProperty()
    {
        $tb = ($this->dataFisik['tanda_vital']['tinggi_badan'] ?? 0) / 100;
        $bb = $this->dataFisik['tanda_vital']['berat_badan'] ?? 0;

        if ($tb <= 0 || $bb <= 0) return ['bmi' => 0, 'kategori' => 'N/A'];

        $bmi = $bb / ($tb * $tb);
        $kategori = 'Normal';

        // Logika sederhana kategori BMI Asia
        if ($bmi < 18.5) $kategori = 'Underweight';
        elseif ($bmi >= 23 && $bmi < 25) $kategori = 'Overweight';
        elseif ($bmi >= 25) $kategori = 'Obese';

        return ['bmi' => number_format($bmi, 2), 'kategori' => $kategori];
    }

    protected function getDefaultDataFisik()
    {
        $tb = $this->patient->tinggi_badan ?? 165;
        $bb = $this->patient->berat_badan ?? 60;

        // Inisialisasi Data Pemeriksaan Fisik
        $data = [
            'tanda_vital' => [
                'tinggi_badan' => $tb,
                'berat_badan' => $bb,
                'tekanan_darah_sistol' => 120,
                'tekanan_darah_diastol' => 80,
                'nadi' => 80,
                'pernafasan' => 18,
                'suhu' => 36.5,
                'spo2' => 97,
            ],

            'kepala' => [
                'anemi' => 'Tidak', 'ikterus' => 'Tidak', 'dyspnoe' => 'Tidak', 'cyanosis' => 'Tidak',
                'refleks_pupil' => $this->defaultOption, 'tonsil_kanan' => 'T1', 'tonsil_kiri' => 'T1',
                'serumen' => 'Tidak Ada', 'membran_timpani' => 'Normal',
            ],
            'leher' => [
                'jvp' => $this->defaultOption, 'tiroid' => $this->defaultOption, 'kelenjar_getah_bening' => $this->defaultOption,
            ],
            'mata' => [
                'visus_kanan' => '6/6', 
                'visus_kiri' => '6/6',
                'konjungtiva' => 'Normal', 
                'sklera' => 'Normal',
                'kesimpulan_mata' => '', // Input kesimpulan mata
            ],
            'dada' => [
                'bunyi_jantung_1' => 'Murni', 'bunyi_jantung_2' => 'Reguler',
            ],
            'paru' => [
                'bunyi_nafas' => 'Vesikular', 'bunyi_nafas_tambahan' => 'Tidak ada',
            ],
            'abdomen' => [
                'peristaltik' => $this->defaultOption, 'nyeri_tekan' => 'Tidak Ada', 'massa' => 'Tidak Ada',
                'hati' => $this->defaultOption, 'limpa' => $this->defaultOption,
            ],
            'ekstremitas' => [
                'ekstremitas' => 'Dalam Batas Normal',
                'refleks_fisiologis_kanan' => $this->defaultRefleks,
                'refleks_fisiologis_kiri' => $this->defaultRefleks,
                'refleks_patologis_kanan' => 'Tidak Ada',
                'refleks_patologis_kiri' => 'Tidak Ada',
            ],

            // --- Inisialisasi Data Pajanan Pekerjaan ---
            'pajanan' => [
                'fisik' => [
                    'kebisingan' => $this->defaultPajanan, 'suhu_panas' => $this->defaultPajanan, 'suhu_dingin' => $this->defaultPajanan,
                    'radiasi_non_pengion' => $this->defaultPajanan, 'radiasi_pengion' => $this->defaultPajanan,
                    'getaran_lokal' => $this->defaultPajanan, 'getaran_seluruh_tubuh' => $this->defaultPajanan,
                    'ketinggian' => $this->defaultPajanan, 'lain_fisik' => $this->defaultPajanan,
                ],
                'kimia' => [
                    'debu_anorganik' => $this->defaultPajanan, 'debu_organic' => $this->defaultPajanan, 'asap' => $this->defaultPajanan,
                    'logam_berat' => $this->defaultPajanan, 'iritan_asam' => $this->defaultPajanan, 'iritan_basa' => $this->defaultPajanan,
                    'cairan_pembersih' => $this->defaultPajanan, 'pestisida' => $this->defaultPajanan, 'uap_logam' => $this->defaultPajanan,
                    'lain_kimia' => $this->defaultPajanan,
                ],
                'biologi' => [
                    'bakteri' => $this->defaultPajanan, 'darah' => $this->defaultPajanan, 'nyamuk' => $this->defaultPajanan,
                    'limbah' => $this->defaultPajanan, 'lain_biologi' => $this->defaultPajanan,
                ],
                'psikologi' => [
                    'beban_kerja' => $this->defaultPajanan, 'pekerjaan_tidak_sesuai' => 'Tidak', 'ketidakjelasan_tugas' => $this->defaultPajanan,
                    'hambatan_jenjang_karir' => $this->defaultPajanan, 'bekerja_giliran' => $this->defaultPajanan,
                    'konflik_teman_sekerja' => $this->defaultPajanan, 'konflik_keluarga' => $this->defaultPajanan, 'lain_psikologi' => $this->defaultPajanan,
                ],
                'ergonomis' => [
                    'gerakan_berulang' => $this->defaultPajanan, 'angkat_berat' => $this->defaultPajanan, 'duduk_lama' => $this->defaultPajanan,
                    'berdiri_lama' => $this->defaultPajanan, 'posisi_tidak_ergonomis' => $this->defaultPajanan, 'pencahayaan_tidak_sesuai' => $this->defaultPajanan,
                    'bekerja_layar_monitor' => $this->defaultPajanan, 'lain_ergonomis' => $this->defaultPajanan,
                ],
            ],
        ];

        return $data;
    }

    public function simpanHasil()
    {
        $this->validate();

        $dokter = Dokter::find($this->dokterId);
        if (!$dokter) {
            session()->flash('error', 'Gagal: Data dokter yang dipilih tidak ditemukan.');
            return;
        }

        // Hitung BMI dan masukkan ke data Fisik sebelum disimpan
        $bmi = $this->getBmiProperty();
        $this->dataFisik['tanda_vital']['bmi'] = $bmi['bmi'];
        $this->dataFisik['tanda_vital']['kategori_bmi'] = $bmi['kategori'];

        // Data yang akan disimpan/diperbarui
        $dataToSave = [
            'data_fisik' => $this->dataFisik,
            'kesimpulan' => $this->kesimpulan,
            'keterangan' => $this->keterangan,
            'dokter_id' => $this->dokterId,
        ];

        try {
            // Cek apakah ada file PDF lama, dan hapus jika ada
            if ($this->fisikResult && $this->fisikResult->file_path && Storage::disk('public')->exists(str_replace('public/', '', $this->fisikResult->file_path))) {
                Storage::disk('public')->delete(str_replace('public/', '', $this->fisikResult->file_path));
            }

            // Gunakan updateOrCreate untuk mengelola penyimpanan dan pembaruan
            $this->fisikResult = FisikResult::updateOrCreate(
                ['jadwal_poli_id' => $this->poliData->id],
                $dataToSave
            );

            // --- Pembuatan PDF dengan Dompdf ---
            $patientIdentifier = $this->patient->nama_pasien ?? $this->patient->nama_karyawan ?? 'N/A';
            $safeIdentifier = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $patientIdentifier);
            $fileName = 'Hasil Pemeriksaan Poli Fisik ' . $safeIdentifier . ' Jadwal ' . $this->poliData->id . '.pdf';
            $storagePath = 'pdf_reports/' . $fileName;

            // KRITIS: Tambahkan data pajanan ke reportData
            $reportData = [
                'patient' => $this->patient,
                'fisikResult' => $this->fisikResult,
                'dokter' => $dokter,
                'instansiPasien' => $this->instansiPasien,
                'pajanan' => $this->dataFisik['pajanan'], // Tambahkan ini
            ];

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdfs.poli-fisik-report', $reportData);
            Storage::disk('public')->put($storagePath, $pdf->output());

            // Update file_path di kedua model setelah PDF berhasil dibuat
            $this->fisikResult->file_path = 'public/' . $storagePath;
            $this->fisikResult->save();

            $this->poliData->file_path = 'public/' . $storagePath;
            $this->poliData->status = 'Done';
            $this->poliData->save();

            session()->flash('success', 'Hasil pemeriksaan fisik dan laporan PDF berhasil disimpan!');

        } catch (\Exception $e) {
            Log::error('PDF Generation GAGAL (Poli Fisik): ' . $e->getMessage());
            session()->flash('error', 'Gagal membuat file PDF. Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // KRITIS: Hanya coba memuat ulang jika $this->poliData adalah model yang valid
        if ($this->poliData instanceof JadwalPoli && isset($this->poliData->id)) {
            $this->fisikResult = FisikResult::where('jadwal_poli_id', $this->poliData->id)->first() ?? $this->fisikResult;
        }

        return view('livewire.poli-fisik-form', [
            'bmiData' => $this->getBmiProperty()
        ]);
    }
}