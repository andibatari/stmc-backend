<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\JadwalMcu;
use App\Models\UnitKerja;

class DashboardController extends Controller
{
     public function index()
    {
        // Ambil data ringkasan untuk dashboard
        $totalKaryawan = Karyawan::count();

        //Ambil data ringkasan unit kerja untuk dashboard
        $totalUnitKerja = UnitKerja::count();

        // Hitung jumlah jadwal MCU yang akan datang
        // Asumsikan JadwalMcu adalah model yang berisi jadwal MCU
        // dan memiliki atribut 'tanggal_mcu' untuk tanggal MCU
        // Ganti 'tanggal_mcu' dengan nama kolom yang sesuai jika berbeda
        $jadwalMcuMendatang = JadwalMcu::where('tanggal_mcu', '>', now())->count();
        
        return view('dashboard.index', compact('totalKaryawan', 'totalUnitKerja', 'jadwalMcuMendatang'));
    }
}
