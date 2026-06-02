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
            // Menghapus kedua kolom sekaligus
            $table->dropColumn(['suami_istri']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            // Mengembalikan kolom jika suatu saat diperlukan (rollback)
            $table->string('suami_istri')->nullable();
        });
    }
};