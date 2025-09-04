<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LokasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::disableQueryLog();

        // Mengosongkan tabel sebelum seeding
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('kecamatans')->truncate();
        DB::table('kabupatens')->truncate();
        DB::table('provinsis')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // --- Logika untuk Provinsi ---
        Log::info('Seeding provinsi...');
        try {
            $provinsiJson = File::get(database_path('data/provinsis.json'));
            $provinsis = json_decode($provinsiJson, true);
            
            if (is_array($provinsis) && !empty($provinsis)) {
                // Perbaikan: Memecah data menjadi potongan 100 baris
                foreach (array_chunk($provinsis, 100) as $chunk) {
                    DB::table('provinsis')->insert($chunk);
                }
                Log::info('Provinsi berhasil di-seed.');
            } else {
                Log::error('Gagal memproses data provinsi dari file JSON atau file kosong.');
            }
        } catch (\Exception $e) {
            Log::error('Terjadi kesalahan saat seeding provinsi: ' . $e->getMessage());
        }


        // --- Logika untuk Kabupaten ---
        Log::info('Seeding kabupaten...');
        try {
            $kabupatenJson = File::get(database_path('data/kabupatens.json'));
            $kabupatens = json_decode($kabupatenJson, true);
            
            if (is_array($kabupatens) && !empty($kabupatens)) {
                // Perbaikan: Memecah data menjadi potongan 100 baris
                foreach (array_chunk($kabupatens, 100) as $chunk) {
                    DB::table('kabupatens')->insert($chunk);
                }
                Log::info('Kabupaten berhasil di-seed.');
            } else {
                Log::error('Gagal memproses data kabupaten dari file JSON atau file kosong.');
            }
        } catch (\Exception $e) {
            Log::error('Terjadi kesalahan saat seeding kabupaten: ' . $e->getMessage());
        }


        // --- Logika untuk Kecamatan ---
        Log::info('Seeding kecamatan...');
        try {
            $kecamatanJson = File::get(database_path('data/kecamatans.json'));
            $kecamatans = json_decode($kecamatanJson, true);

            if (is_array($kecamatans) && !empty($kecamatans)) {
                // Perbaikan: Memecah data menjadi potongan 100 baris
                foreach (array_chunk($kecamatans, 100) as $chunk) {
                    DB::table('kecamatans')->insert($chunk);
                }
                Log::info('Kecamatan berhasil di-seed.');
            } else {
                Log::error('Gagal memproses data kecamatan dari file JSON atau file kosong.');
            }
        } catch (\Exception $e) {
            Log::error('Terjadi kesalahan saat seeding kecamatan: ' . $e->getMessage());
        }
    }
}
