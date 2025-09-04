<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Departemen;
use App\Models\UnitKerja;

class UnitKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan pengecekan foreign key untuk sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Hapus semua data dari tabel unit_kerjas
        UnitKerja::truncate();

        // Ambil ID dari departemen yang sudah ada atau buat jika belum ada
        $departemensId1 = Departemen::firstOrCreate(['nama_departemen' => 'Dept. of Project Mngmnt & Maint. Support'])->id;
        $departemensId2 = Departemen::firstOrCreate(['nama_departemen' => 'Department of Finance & Accounting'])->id;
        $departemensId3 = Departemen::firstOrCreate(['nama_departemen' => 'Department of Human Capital & GRC'])->id;
        $departemensId4 = Departemen::firstOrCreate(['nama_departemen' => 'Department of Internal Audit'])->id;
        $departemensId5 = Departemen::firstOrCreate(['nama_departemen' => 'Dept. of Clinker & Cement Production'])->id;
        $departemensId6 = Departemen::firstOrCreate(['nama_departemen' => 'Dept. of Communication, Legal & GA'])->id;
        $departemensId7 = Departemen::firstOrCreate(['nama_departemen' => 'Dept. of Maintenance'])->id;
        $departemensId8 = Departemen::firstOrCreate(['nama_departemen' => 'Dept. of Market Planning & Development'])->id;
        $departemensId9 = Departemen::firstOrCreate(['nama_departemen' => 'Dept. of Mining & Power Plant'])->id;
        $departemensId10 = Departemen::firstOrCreate(['nama_departemen' => 'Dept. of Production Planning & Control'])->id;
        $departemensId11 = Departemen::firstOrCreate(['nama_departemen' => 'Dept. of Sales'])->id;
        $departemensId12 = Departemen::firstOrCreate(['nama_departemen' => 'Staff of Pres Dir - BU Non Cement'])->id;

        // Masukkan data unit kerja
        DB::table('unit_kerjas')->insert([
            ['nama_unit_kerja' => 'Staff of TPM', 'departemens_id' => $departemensId1],
            ['nama_unit_kerja' => 'Unit of CAPEX Management', 'departemens_id' =>$departemensId1],
            ['nama_unit_kerja' => 'Unit of Engineering', 'departemens_id' =>$departemensId1],
            ['nama_unit_kerja' => 'Unit of Maintenance Planning & Eval.', 'departemens_id' =>$departemensId1],
            ['nama_unit_kerja' => 'Unit of Project Management', 'departemens_id' =>$departemensId1],
            ['nama_unit_kerja' => 'Unit of Workshop & Design', 'departemens_id' =>$departemensId1],
            ['nama_unit_kerja' => 'Unit of Accounting', 'departemens_id' =>$departemensId2],
            ['nama_unit_kerja' => 'Unit of Budgeting & Performance', 'departemens_id' =>$departemensId2],
            ['nama_unit_kerja' => 'Unit of Finance', 'departemens_id' =>$departemensId2],
            ['nama_unit_kerja' => 'Unit of GRC & IC', 'departemens_id' =>$departemensId3],
            ['nama_unit_kerja' => 'Unit of CLD & Innovation', 'departemens_id' =>$departemensId3],
            ['nama_unit_kerja' => 'Unit of HC Operational', 'departemens_id' =>$departemensId3],
            ['nama_unit_kerja' => 'Unit of STMC Management', 'departemens_id' =>$departemensId3],
            ['nama_unit_kerja' => 'Staff of Internal Audit', 'departemens_id' =>$departemensId4],
            ['nama_unit_kerja' => 'Staff of Quality Assurance Int. Audit', 'departemens_id' =>$departemensId4],
            ['nama_unit_kerja' => 'Unit of Cement Production', 'departemens_id' =>$departemensId5],
            ['nama_unit_kerja' => 'Unit of Clinker Production 1', 'departemens_id' =>$departemensId5],
            ['nama_unit_kerja' => 'Unit of Clinker Production 2', 'departemens_id' =>$departemensId5],
            ['nama_unit_kerja' => 'Unit of Interplant Logistic', 'departemens_id' =>$departemensId5],
            ['nama_unit_kerja' => 'Unit of Packing Plant 1', 'departemens_id' =>$departemensId5],
            ['nama_unit_kerja' => 'Unit of Packing Plant 2', 'departemens_id' =>$departemensId5],
            ['nama_unit_kerja' => 'Unit of Port Operation & Maintenance', 'departemens_id' =>$departemensId5],
            ['nama_unit_kerja' => 'Unit of Quality Assurance', 'departemens_id' =>$departemensId5],
            ['nama_unit_kerja' => 'Unit of SCM Infrastructure Port Mngmnt', 'departemens_id' =>$departemensId5],
            ['nama_unit_kerja' => 'Unit of Communication & Secretariate', 'departemens_id' =>$departemensId6],
            ['nama_unit_kerja' => 'Unit of General Facility & Asset', 'departemens_id' =>$departemensId6],
            ['nama_unit_kerja' => 'Unit of Legal', 'departemens_id' =>$departemensId6],
            ['nama_unit_kerja' => 'Unit of Security', 'departemens_id' =>$departemensId6],
            ['nama_unit_kerja' => 'Unit of TJSL', 'departemens_id' =>$departemensId6],
            ['nama_unit_kerja' => 'Unit of Elins Maintenance 1', 'departemens_id' =>$departemensId7],
            ['nama_unit_kerja' => 'Unit of Elins Maintenance 2', 'departemens_id' =>$departemensId7],
            ['nama_unit_kerja' => 'Unit of Machine Maintenance 1', 'departemens_id' =>$departemensId7],
            ['nama_unit_kerja' => 'Unit of Machine Maintenance 2', 'departemens_id' =>$departemensId7],
            ['nama_unit_kerja' => 'Unit of Reliability Maintenance', 'departemens_id' =>$departemensId7],
            ['nama_unit_kerja' => 'Unit of Pricing & Promotion', 'departemens_id' =>$departemensId8],
            ['nama_unit_kerja' => 'Unit of Sales Planning & Evaluation', 'departemens_id' =>$departemensId8],
            ['nama_unit_kerja' => 'Unit of Mining', 'departemens_id' =>$departemensId9],
            ['nama_unit_kerja' => 'Unit of Power Distribution', 'departemens_id' =>$departemensId9],
            ['nama_unit_kerja' => 'Unit of Power Plant Elins Maintenance', 'departemens_id' =>$departemensId9],
            ['nama_unit_kerja' => 'Unit of Power Plant Machine Maint', 'departemens_id' =>$departemensId9],
            ['nama_unit_kerja' => 'Unit of Power Plant Operation', 'departemens_id' =>$departemensId9],
            ['nama_unit_kerja' => 'Unit of Raw Material Management', 'departemens_id' =>$departemensId9],
            ['nama_unit_kerja' => 'Unit of AFR & Energy', 'departemens_id' =>$departemensId10],
            ['nama_unit_kerja' => 'Unit of OHS', 'departemens_id' =>$departemensId10],
            ['nama_unit_kerja' => 'Unit of Prod. Plan Eval. & Environmental', 'departemens_id' =>$departemensId10],
            ['nama_unit_kerja' => 'Unit of Production Support', 'departemens_id' =>$departemensId10],
            ['nama_unit_kerja' => 'Unit of Quality Control', 'departemens_id' =>$departemensId10],
            ['nama_unit_kerja' => 'Unit of Outbound Logistic', 'departemens_id' =>$departemensId11],
            ['nama_unit_kerja' => 'Unit of Sales East Indonesia', 'departemens_id' =>$departemensId11],
            ['nama_unit_kerja' => 'Unit of Sales Kalimantan 2', 'departemens_id' =>$departemensId11],
            ['nama_unit_kerja' => 'Unit of Sales Senior Office', 'departemens_id' =>$departemensId11],
            ['nama_unit_kerja' => 'Unit of Sales Sulawesi 1', 'departemens_id' =>$departemensId11],
            ['nama_unit_kerja' => 'Unit of Sales Sulawesi 2', 'departemens_id' =>$departemensId11]
        ]);

        // Aktifkan kembali pengecekan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
