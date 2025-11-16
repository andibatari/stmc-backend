<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AdminUser; 
use Illuminate\Support\Facades\Hash; 


class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama jika ada untuk menghindari duplikasi saat seeding ulang
        AdminUser::truncate();

        // Buat satu pengguna admin default
        AdminUser::create([
            'no_sap' => '012025',
            'nama_lengkap' => 'Andi Batari Saudah S',
            'nik' =>'7373030404112323',
            'email' => 'batariedu04@gmail.com',
            'password' => Hash::make('041123'),
            'role' => 'superadmin', // Atur peran untuk akun utama
            'foto_profil' => null, // Biarkan null atau berikan URL gambar default
            'dokter_id' => null, // Karena ini admin, bukan dokter
        ]);
    }
}