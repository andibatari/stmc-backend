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
        Schema::create('peserta_mcu_logins', function (Blueprint $table) {
            $table->id();
            // Tambahkan kolom 'nik' untuk otentikasi
            $table->string('nik_pasien')->unique(); 
            // Gunakan `peserta_mcu_id` sebagai foreign key ke tabel `peserta_mcus`
            $table->foreignId('peserta_mcu_id')->constrained('peserta_mcus')->onDelete('cascade');
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta_mcu_logins');
    }
};
