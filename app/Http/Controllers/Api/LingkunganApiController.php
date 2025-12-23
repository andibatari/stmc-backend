<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PemantauanLingkungan; // Sesuaikan dengan nama model Anda
use Carbon\Carbon;

class LingkunganApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Gunakan eager loading agar data Departemen & Unit Kerja ikut terkirim ke API
            $query = PemantauanLingkungan::with(['departemen', 'unitKerja']);

            // 1. Filter Berdasarkan Area (Jika 'location' di Flutter merujuk pada kolom 'area')
            if ($request->has('location') && $request->location != 'Semua') {
                $query->where('area', $request->location);
            }

            // 2. Filter Berdasarkan Bulan (Format: "Juli 2025")
            if ($request->has('month') && $request->month != '') {
                try {
                    // Tambahkan pengecekan jika bulan dalam format Indonesia
                    $monthName = $request->month;
                    
                    // Pemetaan sederhana jika locale server tidak mendukung bahasa Indonesia
                    $monthsId = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    $monthsEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                    $monthConverted = str_replace($monthsId, $monthsEn, $monthName);

                    $date = Carbon::createFromFormat('F Y', $monthConverted);
                    
                    $query->whereMonth('tanggal_pemantauan', $date->month)
                        ->whereYear('tanggal_pemantauan', $date->year);
                } catch (\Exception $e) {
                    // Tetap lanjutkan query tanpa filter bulan jika parsing gagal
                }
            }

            // Ambil data terbaru
            $data = $query->orderBy('tanggal_pemantauan', 'desc')->get();

            // Transformasi data agar Flutter lebih mudah membaca (Opsional tapi direkomendasikan)
            $transformedData = $data->map(function ($item) {
                return [
                    'id' => $item->id,
                    'location' => $item->area, // Map 'area' ke 'location' di Flutter
                    'sub_area' => $item->lokasi, // Map 'lokasi' ke 'sub_area'
                    'department' => $item->departemen->nama_departemen ?? 'N/A',
                    'unit_kerja' => $item->unitKerja->nama_unit_kerja ?? 'N/A',
                    'tanggal' => Carbon::parse($item->tanggal_pemantauan)->format('d M Y'),
                    
                    // Data Pemantauan (Asumsi disimpan sebagai JSON di database)
                    'cahaya_lux' => $item->data_pemantauan['cahaya'] ?? 0,
                    'bising_db' => $item->data_pemantauan['bising'] ?? 0,
                    'debu_mg_nm3' => $item->data_pemantauan['debu'] ?? 0,
                    'suhu_basah' => $item->data_pemantauan['suhu_basah'] ?? 'N/A',
                    'suhu_kering' => $item->data_pemantauan['suhu_kering'] ?? 'N/A',
                    'rh' => $item->data_pemantauan['rh'] ?? 'N/A',
                    
                    // Metadata NAB untuk logika warna di Flutter
                    'nab_cahaya' => $item->nab_cahaya,
                    'nab_bising' => $item->nab_bising,
                    'nab_debu' => $item->nab_debu,
                ];
            });

            return response()->json([
                'status' => 'success',
                'count' => $transformedData->count(),
                'data' => $transformedData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getFilters()
    {
        try {
            // 1. Ambil Area dan tambahkan 'Semua' secara manual
            $areas = \App\Models\PemantauanLingkungan::distinct()->pluck('area')->toArray();
            array_unshift($areas, 'Semua');

            // 2. Ambil Departemen dan tambahkan 'Semua'
            $departments = \App\Models\Departemen::pluck('nama_departemen')->toArray();
            array_unshift($departments, 'Semua');

            // 3. Ambil Unit Kerja dan tambahkan 'Semua'
            $units = \App\Models\UnitKerja::pluck('nama_unit_kerja')->toArray();
            array_unshift($units, 'Semua');

            // 4. Ambil Bulan unik dari tanggal_pemantauan
            $months = \App\Models\PemantauanLingkungan::selectRaw("DATE_FORMAT(tanggal_pemantauan, '%M %Y') as month_year")
                ->distinct()
                ->orderBy('tanggal_pemantauan', 'desc')
                ->pluck('month_year')
                ->toArray();

            return response()->json([
                'status' => 'success',
                'areas' => $areas,
                'departments' => $departments,
                'units' => $units,
                'months' => $months
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}