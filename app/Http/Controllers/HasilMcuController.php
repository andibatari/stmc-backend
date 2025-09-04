<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HasilMcu;
use App\Models\JadwalMcu;
use App\Models\Karyawan; //diperlukan untuk mengirim notifikasi

class HasilMcuController extends Controller
{
    // Menampilkan formulir input hasil MCU
    public function showInputForm(JadwalMcu $jadwalMcu)
    {
        return view('hasil_mcu.input', compact('jadwalMcu'));
    }

    // Menyimpan data dari formulir "Input Hasil MCU"
    public function simpanHasil(Request $request, JadwalMcu $jadwalMcu)
    {
        $request->validate([
            // Kolom Duplikasi Data Karyawan
            'no_sap' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'nama_lengkap' => 'nullable|string|max:255',
            'unit_kerja' => 'nullable|string|max:10',
            'dokter' => 'nullable|string|max:255',
            'tanggal_mcu_dilaksanakan' => 'nullable|date',

            // BAGIAN PEMERIKSAAN FISIK
            'tekanan_darah' => 'nullable|string|max:20',
            'nadi' => 'nullable|string|max:20',
            'suhu_tubuh' => 'nullable|string|max:20',
            'pernapasan' => 'nullable|string|max:20',
            'sp02' => 'nullable|string|max:20',
            'berat_badan' => 'nullable|numeric|max:999.99',
            'tinggi_badan' => 'nullable|numeric|max:999.99',
            'lingkar_perut' => 'nullable|string|max:20',
            'bmi' => 'nullable|numeric|max:99.99',
            'status_gizi' => 'nullable|string',
            'anemi_konjungtiva' => 'nullable|string|max:50',
            'cyanosis' => 'nullable|string|max:50',
            'ikterus' => 'nullable|string|max:50',
            'tonsil_kanan' => 'nullable|string|max:50',
            'tonsil_kiri' => 'nullable|string|max:50',
            'refleks_pupil' => 'nullable|string|max:50',
            'hidung' => 'nullable|string|max:50',
            'dyspnoe' => 'nullable|string|max:50',
            'serumen_telinga' => 'nullable|string|max:50',
            'membran_timpani' => 'nullable|string|max:50',
            'visus_kanan' => 'nullable|string|max:20',
            'visus_kiri' => 'nullable|string|max:20',
            'jvp' => 'nullable|string|max:50',
            'tiroid' => 'nullable|string|max:50',
            'kelenjar_getah_bening' => 'nullable|string|max:50',
            'jantung1' => 'nullable|string|max:50',
            'jantung2' => 'nullable|string|max:50',
            'bunyi_nafas' => 'nullable|string|max:50',
            'bunyi_nafas_tambahan' => 'nullable|string|max:50',
            'peristaltik' => 'nullable|string|max:50',
            'nyeri_tekan_abdomen' => 'nullable|string|max:50',
            'massa_abdomen' => 'nullable|string|max:50',
            'hati_abdomen' => 'nullable|string|max:50',
            'limpa_abdomen' => 'nullable|string|max:50',
            'ekskrimitas_kanan' => 'nullable|string|max:50',
            'ekskrimitas_kiri' => 'nullable|string|max:50',
            'refleks_fisiologis_kanan' => 'nullable|string|max:50',
            'refleks_fisiologis_kiri' => 'nullable|string|max:50',
            'refleks_patologis_kanan' => 'nullable|string|max:50',
            'refleks_patologis_kiri' => 'nullable|string|max:50',

            // BAGIAN ANAMNESA
            'keluhan_utama' => 'nullable|string',
            'riwayat_kesehatan' => 'nullable|string',
            'riwayat_penyakit_keluarga' => 'nullable|string',
            'riwayat_sosial' => 'nullable|string',

            // BAGIAN FISIK
            'fisik_kebisingan' => 'nullable|string',
            'fisik_suhu_panas' => 'nullable|string',
            'fisik_suhu_dingin' => 'nullable|string',
            'fisik_getaran_lokal' => 'nullable|string',
            'fisik_getaran_seluruh_tubuh' => 'nullable|string',
            'fisik_ketinggian' => 'nullable|string',
            'fisik_radiasi_pengion' => 'nullable|string',
            'fisik_radiasi_bukan_pengion' => 'nullable|string',
            
            // BAGIAN KIMIA
            'kimia_debu_anorganik' => 'nullable|string',
            'kimia_debu_organik' => 'nullable|string',
            'kimia_asap' => 'nullable|string',
            'kimia_bahan_kimia_berbahaya' => 'nullable|string',
            'kimia_logam_berat' => 'nullable|string',
            'kimia_iritan_asam' => 'nullable|string',
            'kimia_iritan_basa' => 'nullable|string',
            'kimia_cairan_pembersih' => 'nullable|string',
            'kimia_pestisida' => 'nullable|string',
            'kimia_uap_logam' => 'nullable|string',
            'kimia_lain_lain' => 'nullable|string',
            
            // BAGIAN BIOLOGI
            'biologi_bakteri_virus_jamur_parasit' => 'nullable|string',
            'biologi_darah_cairan_tubuh' => 'nullable|string',
            'biologi_nyamuk_serangga' => 'nullable|string',
            'biologi_limbah_kotoran_manusia' => 'nullable|string',
            'biologi_lain_lain' => 'nullable|string',
            
            // BAGIAN PSIKOLOGI
            'psikologi_bekerja_tidak_sesuai_waktu' => 'nullable|string',
            'psikologi_pekerjaan_tidak_sesuai_pengalaman' => 'nullable|string',
            'psikologi_ketidakjelasan_tugas' => 'nullable|string',
            'psikologi_hambatan_jenjang_karir' => 'nullable|string',
            'psikologi_bekerja_giliran_shift' => 'nullable|string',
            'psikologi_konflik_dengan_rekan_kerja' => 'nullable|string',
            'psikologi_konflik_dalam_keluarga' => 'nullable|string',
            
            // BAGIAN ERGONOMIS
            'ergonomis_gerakan_berulang_dengan_tangan' => 'nullable|string',
            'ergonomis_angkat_angkut_berat' => 'nullable|string',
            'ergonomis_duduk_lama' => 'nullable|string',
            'ergonomis_berdiri_lama' => 'nullable|string',
            'ergonomis_posisi_tubuh_tidak_ergonomis' => 'nullable|string',
            'ergonomis_pencahayaan_tidak_sesuai' => 'nullable|string',
            'ergonomis_bekerja_dengan_layar' => 'nullable|string',

            // BAGIAN KESEGARAN JASMANI (ERGOCYCLE)
            'lama_pemeriksaan_menit' => 'nullable|integer',
            'beban_latihan_level' => 'nullable|integer',
            'jumlah_denyut_nadi_akhir' => 'nullable|integer',
            'kebutuhan_vo2_maksimal' => 'nullable|numeric|max:999.99',
            'kategori_kesegaran_jasmani_hasil' => 'nullable|string',

            // BAGIAN LAINNYA
            'file_pemeriksaan_audiometri' => 'nullable|string|max:255',
            'file_pemeriksaan_spirometri' => 'nullable|string|max:255',
            'file_ekg' => 'nullable|string|max:255',
            'file_gigi' => 'nullable|string|max:255',

            // KOLOM UNTUK REKAP TEKS DARI HASIL UPLOAD
            'hasil_bmi_rekap' => 'nullable|string',
            'hasil_lab_rekap' => 'nullable|string',
            'hasil_rekam_jantung_rekap' => 'nullable|string',
            'hasil_gigi_rekap' => 'nullable|string',
            'hasil_visus_rekap' => 'nullable|string',
            'hasil_audiometri_rekap' => 'nullable|string',
            'hasil_spirometri_rekap' => 'nullable|string',
            'hasil_kesegaran_jasmani_rekap' => 'nullable|string',
            'riwayat_penyakit_sebelumnya_rekap' => 'nullable|string',

            // KESIMPULAN DAN SARAN DOKTER
            'kesimpulan_mcu' => 'required|string',
            'saran_dokter' => 'nullable|string',
            'file_hasil_mcu_pdf' => 'nullable|string|max:255', // Diisi otomatis
        ]);

       // Gabungkan data dari request dengan jadwal_mcu_id
        $data = $request->all();
        $data['jadwal_mcu_id'] = $jadwalMcu->id;

        // Buat entri baru di tabel hasil_mcus
        $hasil = HasilMcu::create($data);
        
        // Update status jadwal menjadi 'Selesai'
        $jadwalMcu->update(['status' => 'Selesai']);

        // Kirim notifikasi ke karyawan bahwa hasil sudah tersedia
        $jadwalMcu->karyawan->notify(new HasilMcuNotification($hasil));
        
        return redirect()->route('jadwal.index')->with('success', 'Hasil MCU berhasil disimpan.');
    }

    // Menampilkan hasil MCU untuk pengguna yang sedang login
    public function lihatHasil(Request $request)
    {
        $karyawan = $request->user()->karyawan; // Asumsi relasi user->karyawan ada

        if ($karyawan) {
            $hasilMcu = $karyawan->hasilMcus; // Asumsi relasi karyawan->hasilMcus ada
            return response()->json(['hasil' => $hasilMcu]);
        }

        return response()->json(['message' => 'Data tidak ditemukan.'], 404);
    }
}


