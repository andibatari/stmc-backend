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

        // Buat data untuk tabel 'paket_mcus'
        $paket1 = PaketMcu::create(['nama_paket' => 'Paket 1']);
        $paket2 = PaketMcu::create(['nama_paket' => 'Paket 2']);
        $paket3 = PaketMcu::create(['nama_paket' => 'Paket 3']);
        $paketLengkap = PaketMcu::create(['nama_paket' => 'Paket Lengkap']);

        // Buat data untuk tabel 'polis'
        $poli1 = Poli::create(['nama_poli' => 'LABORATORIUM']);
        $poli2 = Poli::create(['nama_poli' => 'GIGI']);
        $poli3 = Poli::create(['nama_poli' => 'MATA']);
        $poli4 = Poli::create(['nama_poli' => 'FISIK']);
        $poli5 = Poli::create(['nama_poli' => 'EKG']);
        $poli6 = Poli::create(['nama_poli' => 'AUDIOMETRI']);
        $poli7 = Poli::create(['nama_poli' => 'SPIROMETRI']);
        $poli8 = Poli::create(['nama_poli' => 'KEBUGARAN']);
        $poli9 = Poli::create(['nama_poli' => 'THORAX PHOTO']);
        $poli10 = Poli::create(['nama_poli' => 'TREADMILL']);
        $poli11 = Poli::create(['nama_poli' => 'USG']);

        // Menghubungkan paket dengan poli menggunakan tabel pivot
    
        $paket1->poli()->attach([$poli1->id, $poli3->id, $poli4->id, $poli5->id, $poli6->id]);
        $paket2->poli()->attach([$poli1->id, $poli3->id, $poli4->id, $poli6->id]);
        $paket3->poli()->attach([$poli1->id, $poli3->id, $poli4->id, $poli5->id]);
        $paketLengkap->poli()->attach([$poli1->id, $poli2->id, $poli3->id, $poli4->id, $poli5->id, $poli6->id, $poli7->id, $poli8->id, $poli9->id, $poli10->id, $poli11->id]);
    }
}
