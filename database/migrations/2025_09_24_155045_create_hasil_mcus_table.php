<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hasil_mcus', function (Blueprint $table) {
            $table->id();     
            $table->unsignedBigInteger('jadwal_mcu_id')->unique(); // Foreign Key ke `jadwal_mcus`, unik karena 1 jadwal hanya punya 1 hasil

            // --- Kolom Duplikasi Data Karyawan (untuk laporan yang berdiri sendiri) ---
            $table->string('no_sap', 50)->nullable();
            $table->text('alamat')->nullable();
            $table->string('nama_lengkap', 255)->nullable();
            $table->string('unit_kerja', 10)->nullable();
            $table->string('dokter', 255)->nullable();
            $table->date('tanggal_mcu_dilaksanakan')->nullable(); // Tanggal MCU di form hasil

            // --- BAGIAN PEMERIKSAAN FISIK ---
            // Tanda Vital & Pengukuran
            $table->string('tekanan_darah', 20)->nullable(); // 120/80 mmHg
            $table->string('nadi', 20)->nullable(); // /menit
            $table->string('suhu_tubuh', 20)->nullable(); // °C
            $table->string('pernapasan', 20)->nullable(); // /menit
            $table->string('sp02', 20)->nullable(); // %
            $table->decimal('berat_badan', 5, 2)->nullable(); // kg
            $table->decimal('tinggi_badan', 5, 2)->nullable(); // cm
            $table->string('lingkar_perut', 20)->nullable(); // cm
            $table->decimal('bmi', 4, 2)->nullable(); // BMI
            $table->text('status_gizi')->nullable(); // Obesitas 1

            // Kepala, Mata, Telinga, Leher
            $table->string('anemi_konjungtiva', 50)->nullable(); // Anemi
            $table->string('cyanosis', 50)->nullable(); // Cyanosis
            $table->string('ikterus', 50)->nullable(); // Ikterus
            $table->string('tonsil_kanan', 50)->nullable(); // Tonsil Kanan
            $table->string('tonsil_kiri', 50)->nullable(); // Tonsil Kiri
            $table->string('refleks_pupil', 50)->nullable(); // Refleks Pupil
            $table->string('Hidung', 50)->nullable(); 
            $table->string('dyspnoe', 50)->nullable(); // Dyspnoe
            $table->string('serumen_telinga', 50)->nullable(); // Serumen
            $table->string('membran_timpani', 50)->nullable(); // Membran Timpani
            $table->string('visus_kanan', 20)->nullable(); // Visus Mata Kanan
            $table->string('visus_kiri', 20)->nullable(); // Visus Mata Kiri
            $table->string('jvp', 50)->nullable(); // JVP
            $table->string('tiroid', 50)->nullable(); // Tiroid
            $table->string('kelenjar_getah_bening', 50)->nullable(); // Kelenjar Getah Bening

            // Dada, Paru, Abdomen, Ekstremitas
            $table->string('jantung1', 50)->nullable(); // Jantung
            $table->string('jantung2', 50)->nullable(); // Jantung2
            $table->string('bunyi_nafas', 50)->nullable(); // Bunyi Nafas
            $table->string('bunyi_nafas_tambahan', 50)->nullable(); // Tambahan
            $table->string('Peristaltik', 50)->nullable(); 
            $table->string('nyeri_tekan_abdomen', 50)->nullable(); // Nyeri Tekan
            $table->string('massa_abdomen', 50)->nullable(); // Massa
            $table->string('hati_abdomen', 50)->nullable(); // Hati
            $table->string('limpa_abdomen', 50)->nullable(); // Limpa
            $table->string('ekskrimitas_kanan', 50)->nullable(); // Ekskrimitas Kanan
            $table->string('ekskrimitas_kiri', 50)->nullable(); // Ekskrimitas Kiri
            $table->string('refleks_fisiologis_kanan', 50)->nullable(); // Refleks Fisiologis Kanan
            $table->string('refleks_fisiologis_kiri', 50)->nullable(); // Refleks Fisiologis Kiri
            $table->string('refleks_patologis_kanan', 50)->nullable(); // Refleks Patologis Kanan
            $table->string('refleks_patologis_kiri', 50)->nullable(); // Refleks Patologis Kiri

            // --- BAGIAN ANAMNESA ---
            $table->text('keluhan_utama')->nullable();
            $table->text('riwayat_kesehatan')->nullable();
            $table->text('riwayat_penyakit_keluarga')->nullable();
            $table->text('riwayat_sosial')->nullable();

            // --- BAGIAN FISIK ---
            $table->text('fisik_kebisingan')->nullable();
            $table->text('fisik_suhu_panas')->nullable();
            $table->text('fisik_suhu_dingin')->nullable();
            $table->text('fisik_getaran_lokal')->nullable();
            $table->text('fisik_getaran_seluruh_tubuh')->nullable();
            $table->text('fisik_ketinggian')->nullable();
            $table->text('fisik_radiasi_pengion')->nullable();
            $table->text('fisik_radiasi_bukan_pengion')->nullable();
            
            // --- BAGIAN KIMIA ---
            $table->text('kimia_debu_anorganik')->nullable();
            $table->text('kimia_debu_organik')->nullable();
            $table->text('kimia_asap')->nullable();
            $table->text('kimia_bahan_kimia_berbahaya')->nullable();
            $table->text('kimia_logam_berat')->nullable();
            $table->text('kimia_iritan_asam')->nullable();
            $table->text('kimia_iritan_basa')->nullable();
            $table->text('kimia_cairan_pembersih')->nullable();
            $table->text('kimia_pestisida')->nullable();
            $table->text('kimia_uap_logam')->nullable();
            $table->text('kimia_lain_lain')->nullable();
            
            // --- BAGIAN BIOLOGI ---
            $table->text('biologi_bakteri_virus_jamur_parasit')->nullable();
            $table->text('biologi_darah_cairan_tubuh')->nullable();
            $table->text('biologi_nyamuk_serangga')->nullable();
            $table->text('biologi_limbah_kotoran_manusia')->nullable();
            $table->text('biologi_lain_lain')->nullable();
            
            // --- BAGIAN PSIKOLOGI ---
            $table->text('psikologi_bekerja_tidak_sesuai_waktu')->nullable();
            $table->text('psikologi_pekerjaan_tidak_sesuai_pengalaman')->nullable();
            $table->text('psikologi_ketidakjelasan_tugas')->nullable();
            $table->text('psikologi_hambatan_jenjang_karir')->nullable();
            $table->text('psikologi_bekerja_giliran_shift')->nullable();
            $table->text('psikologi_konflik_dengan_rekan_kerja')->nullable();
            $table->text('psikologi_konflik_dalam_keluarga')->nullable();
            
            // --- BAGIAN ERGONOMIS ---
            $table->text('ergonomis_gerakan_berulang_dengan_tangan')->nullable();
            $table->text('ergonomis_angkat_angkut_berat')->nullable();
            $table->text('ergonomis_duduk_lama')->nullable();
            $table->text('ergonomis_berdiri_lama')->nullable();
            $table->text('ergonomis_posisi_tubuh_tidak_ergonomis')->nullable();
            $table->text('ergonomis_pencahayaan_tidak_sesuai')->nullable();
            $table->text('ergonomis_bekerja_dengan_layar')->nullable();

            // --- BAGIAN KESEGARAN JASMANI (ERGOCYCLE) ---
            $table->integer('lama_pemeriksaan_menit')->nullable();
            $table->integer('beban_latihan_level')->nullable();
            $table->integer('jumlah_denyut_nadi_akhir')->nullable();
            $table->decimal('kebutuhan_vo2_maksimal', 5, 2)->nullable();
            $table->string('kategori_kesegaran_jasmani_hasil')->nullable();

            // --- BAGIAN LAINNYA ---
            $table->string('file_pemeriksaan_audiometri', 255)->nullable();
            $table->string('file_pemeriksaan_spirometri', 255)->nullable();
            $table->string('file_ekg', 255)->nullable();
            $table->string('file_gigi', 255)->nullable();

            // --- KOLOM UNTUK REKAP TEKS DARI HASIL UPLOAD ---
            $table->text('hasil_bmi_rekap')->nullable();
            $table->text('hasil_lab_rekap')->nullable();
            $table->text('hasil_rekam_jantung_rekap')->nullable();
            $table->text('hasil_gigi_rekap')->nullable();
            $table->text('hasil_visus_rekap')->nullable();
            $table->text('hasil_audiometri_rekap')->nullable();
            $table->text('hasil_spirometri_rekap')->nullable();
            $table->text('hasil_kesegaran_jasmani_rekap')->nullable();
            $table->text('riwayat_penyakit_sebelumnya_rekap')->nullable();

            // --- KESIMPULAN DAN SARAN DOKTER ---
            $table->text('kesimpulan_mcu')->nullable();
            $table->text('saran_dokter')->nullable();
            $table->string('file_hasil_mcu_pdf')->nullable(); // Path/URL file PDF hasil (diisi otomatis)

            

            $table->timestamps(); // `created_at` dan `updated_at` otomatis

            // Definisi Foreign Key
            $table->foreign('jadwal_mcu_id')->references('id')->on('jadwal_mcus')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_mcus');
    }
};
