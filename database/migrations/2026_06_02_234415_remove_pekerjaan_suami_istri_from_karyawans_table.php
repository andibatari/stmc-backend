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
        Schema::table('karyawans', function (Blueprint $table) {
            // Perintah untuk menghapus kolom yang tidak diinginkan
            $table->dropColumn('pekerjaan_suami_istri');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            // Perintah untuk mengembalikan kolom (jika suatu saat kamu butuh rollback)
            $table->string('pekerjaan_suami_istri')->nullable();
        });
    }
};