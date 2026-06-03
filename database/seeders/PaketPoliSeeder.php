<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Seeder;
use App\Models\PaketMcu;
use App\Models\Poli;

class PaketPoliSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menonaktifkan pemeriksaan foreign key untuk sementara
        Schema::disableForeignKeyConstraints();

        // Hapus data dari tabel pivot terlebih dahulu
        DB::table('paket_poli')->truncate();
        // Hapus data lama untuk menghindari duplikasi
        PaketMcu::truncate();
        Poli::truncate();

        /// HANYA MEMBUAT DATA MASTER POLI (Tanpa Poli Mata)
        Poli::create(['nama_poli' => 'LABORATORIUM']);
        Poli::create(['nama_poli' => 'GIGI']);
        Poli::create(['nama_poli' => 'FISIK']);
        Poli::create(['nama_poli' => 'EKG']);
        Poli::create(['nama_poli' => 'AUDIOMETRI']);
        Poli::create(['nama_poli' => 'SPIROMETRI']);
        Poli::create(['nama_poli' => 'KEBUGARAN']);
        Poli::create(['nama_poli' => 'THORAX PHOTO']);
        Poli::create(['nama_poli' => 'TREADMILL']);
        Poli::create(['nama_poli' => 'USG']);

        // Mengaktifkan kembali pemeriksaan foreign key
        Schema::enableForeignKeyConstraints();
    }
}