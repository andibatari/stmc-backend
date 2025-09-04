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
        Schema::create('jadwal_mcus', function (Blueprint $table) {
            $table->id();
            
            // Kolom wajib untuk semua tipe pasien
            $table->enum('tipe_pasien', ['ptst', 'non-ptst'])->default('ptst');
            $table->date('tanggal_mcu');
            $table->date('tanggal_pendaftaran'); // Tambahkan kolom ini
            $table->string('no_antrean', 20)->unique()->nullable();
            $table->enum('status', ['Waited', 'Scheduled', 'Finished', 'Canceled'])->default('Waited');
            $table->string('dokter')->nullable();
            $table->timestamps();

            // Kolom untuk Karyawan PTST (nullable untuk pasien non-PTST)
            $table->unsignedBigInteger('karyawan_id')->nullable(); 
            $table->string('no_sap', 50)->nullable(); 
            
            // Kolom untuk Pasien Non-PTST (nullable untuk karyawan PTST)
            $table->string('nama_pasien')->nullable(); // Digunakan untuk non-PTST
            $table->string('no_identitas')->nullable(); // Digunakan untuk non-PTST
            $table->string('perusahaan_afiliasi')->nullable(); // Digunakan untuk non-PTST
            $table->date('tanggal_lahir')->nullable(); // Digunakan untuk non-PTST
            
            // Definisi Foreign Key
            $table->foreign('karyawan_id')->references('id')->on('karyawans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_mcus');
    }
};
