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
        Schema::create('dokters', function (Blueprint $table) {
            $table->id();
            $table->string('nik',20)->nullable(); // Ditambahkan untuk menyimpan NIK dokter
            $table->string('nama_lengkap');
            $table->string('spesialisasi')->nullable();
            $table->date('tanggal_lahir')->nullable(); // Ditambahkan untuk tanggal lahir
            $table->string('golongan_darah')->nullable(); // Ditambahkan untuk golongan darah
            $table->string('no_hp')->nullable();
            $table->string('email')->unique();
            $table->string('role')->default('dokter'); // Ditambahkan untuk peran pengguna
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokters');
    }
};