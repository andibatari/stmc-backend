<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provinsis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_provinsi');
            $table->timestamps();
        });

        Schema::create('kabupatens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provinsi_id')->constrained()->onDelete('cascade');
            $table->string('nama_kabupaten');
            $table->timestamps();
        });

        Schema::create('kecamatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kabupaten_id')->constrained()->onDelete('cascade');
            $table->string('nama_kecamatan');
            $table->timestamps();
        });

        Schema::table('karyawans', function (Blueprint $table) {
            $table->foreignId('provinsi_id')->nullable()->after('unit_kerjas_id')->constrained()->onDelete('set null');
            $table->foreignId('kabupaten_id')->nullable()->after('provinsi_id')->constrained()->onDelete('set null');
            $table->foreignId('kecamatan_id')->nullable()->after('kabupaten_id')->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->dropForeign(['kecamatan_id']);
            $table->dropColumn('kecamatan_id');
        });

        Schema::dropIfExists('provinsis');
        Schema::dropIfExists('kabupatens');
        Schema::dropIfExists('kecamatans');
    }
};
