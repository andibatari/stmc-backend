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
        Schema::create('pemantauan_lingkungan', function (Blueprint $table) {
            $table->id();
           // Kunci asing untuk relasi ke tabel master `lokasis` dan `areas`
            $table->foreignId('lokasi_id')->nullable()->constrained('lokasi')->onDelete('set null');
            $table->foreignId('area_id')->nullable()->constrained('area')->onDelete('set null');
            $table->date('tanggal')->nullable(); // Perubahan ada di sini
            
            // --- PENGUKURAN LINGKUNGAN ---
            $table->decimal('cahaya', 8, 2)->nullable();
            $table->decimal('bising', 8, 2)->nullable();
            $table->decimal('debu', 8, 2)->nullable();

            // --- PENGUKURAN IKLIM KERJA ---
            $table->decimal('suhu_basah', 8, 2)->nullable();
            $table->decimal('suhu_kering', 8, 2)->nullable();
            $table->decimal('suhu_radiasi', 8, 2)->nullable();
            $table->decimal('isbb_indoor', 8, 2)->nullable();
            $table->decimal('isbb_outdoor', 8, 2)->nullable();
            $table->decimal('rh', 8, 2)->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemantauan_lingkungan');
    }
};
