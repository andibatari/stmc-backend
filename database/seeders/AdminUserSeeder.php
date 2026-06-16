<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AdminUser; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Matikan Foreign Key Checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); 

        // 2. Hapus data lama
        AdminUser::truncate();

        // 3. Hidupkan kembali Foreign Key Checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;'); 

        // 4. Buat akun superadmin pertama (Andi Batari Saudah S)
        AdminUser::create([
            'no_sap' => '012025',
            'nama_lengkap' => 'Andi Batari Saudah S',
            'nik' => '7373030404112323',
            'email' => 'batariedu04@gmail.com',
            'password' => Hash::make('041123'),
            'role' => 'superadmin', 
            'foto_profil' => null, 
            'dokter_id' => null, 
        ]);

        // 5. Buat akun superadmin kedua (MCU STMC)
        AdminUser::create([
            'no_sap' => '88990011',
            'nama_lengkap' => 'Medical Check Up STMC',
            'nik' => '7371122334455667',
            'email' => 'mcustmc2025@gmail.com',
            'password' => Hash::make('AdminMcu2026'),
            'role' => 'superadmin', 
            'foto_profil' => null, 
            'dokter_id' => null, 
        ]);
    }
}