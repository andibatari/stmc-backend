<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AdminUser; 
use Illuminate\Support\Facades\Hash; // Gunakan facade Hash untuk mengenkripsi password


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
            'no_sap' => '001',
            'nama_lengkap' => 'Admin Utama',
            'email' => 'admin@mcuapp.com', // Opsional, sesuaikan jika diperlukan
            'password' => Hash::make('123456'), // Password default, enkripsi
        ]);
    }
}
