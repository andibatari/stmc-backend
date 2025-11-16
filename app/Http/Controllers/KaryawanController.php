<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLogin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\UnitKerja;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use App\Imports\KaryawanImport;
use App\Imports\KaryawanExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\pesertaMcuImport;
use App\Models\PesertaMcu;

class KaryawanController extends Controller
{
    /**
     * Tampilkan daftar semua karyawan.
     */
    public function index()
    {
        // dan nested relation untuk lokasi
        $karyawans = Karyawan::with(['departemen', 'unitKerja', 'kecamatan.kabupaten.provinsi'])->get();
        return view('karyawan.index', compact('karyawans'));
    }

    /**
     * Tampilkan formulir untuk membuat karyawan baru.
     */
    public function create()
    {
        return view('karyawan.create');
    }

    // public function edit(Karyawan $karyawan)
    // {
    //     // Mendapatkan semua data untuk dropdown
    //     $departemens = Departemen::all();
    //     $unitKerjas = UnitKerja::all();
    //     $provinsis = Provinsi::all();
        
    //     // Memuat data kabupaten dan kecamatan berdasarkan data karyawan
    //     $kabupatens = collect();
    //     $kecamatans = collect();
    //     if ($karyawan->kecamatan_id) {
    //         $kecamatan = Kecamatan::find($karyawan->kecamatan_id);
    //         if ($kecamatan) {
    //             $kabupatens = Kabupaten::where('provinsi_id', $kecamatan->kabupaten->provinsi_id)->get();
    //             $kecamatans = Kecamatan::where('kabupaten_id', $kecamatan->kabupaten_id)->get();
    //         }
    //     }

    //     return view('karyawan.edit', compact(
    //         'karyawan', 
    //         'departemens', 
    //         'unitKerjas', 
    //         'provinsis', 
    //         'kabupatens', 
    //         'kecamatans'
    //     ));
    // }

    public function edit(Karyawan $karyawan)
    {
        // Pastikan variabel 'karyawan' dikirim ke view
        return view('karyawan.edit', compact('karyawan'));
    }

    /**
     * Menampilkan detail satu karyawan
     */
    public function show(Karyawan $karyawan)
    {
        return view('karyawan.show', compact('karyawan'));
    }

    
    
    /**
     * Hapus data karyawan dari database.
     */
    public function destroy(Karyawan $karyawan)
    {
        $karyawan->delete();
        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil dihapus.');
    }

    // // Metode API untuk mendapatkan Unit Kerja berdasarkan Departemen ID
    // public function getUnitsByDepartemen(Departemen $departemen)
    // {
    //     // Pastikan relasi 'unitKerjas' sudah didefinisikan di model Departemen
    //     // Mengambil unit kerja berdasarkan departemen yang diberikan
    //     $unitKerjas = $departemen->unitKerjas()->get([
    //         'id',
    //         'nama_unit_kerja',
    //     ]);
        
    //     return response()->json($unitKerjas);
    // }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file_karyawan' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new KaryawanImport, $request->file('file_karyawan'));

        return redirect()->back()->with('success', 'Data karyawan berhasil diimport.');
    }

    public function downloadExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\KaryawanExport, 'Data Karyawan.xlsx');
    }

    public function pesertaMcuImport(Request $request)
    {
        $request->validate([
            'file_peserta_mcu' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new pesertaMcuImport, $request->file('file_peserta_mcu'));

        return redirect()->back()->with('success', 'Data pasien berhasil diimport.');
    }
    public function pesertaMcuExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PesertaMcuExport, 'Data Pasien Mcu.xlsx');
    }
    /**
     * Ambil data keluarga dalam format JSON.
     */
    // public function showKeluarga($karyawan_id, $tipe)
    // {
    //     $karyawan = Karyawan::findOrFail($karyawan_id);

    //     $data = [];

    //     // Siapkan data berdasarkan tipe
    //     if ($tipe === 'istri') {
    //         $data = [
    //             'nama' => $karyawan->suami_istri,
    //             'pekerjaan' => $karyawan->pekerjaan_suami_istri
    //         ];
    //     } elseif ($tipe === 'suami') {
    //         $data = [
    //             'nama' => $karyawan->suami_istri,
    //             'pekerjaan' => $karyawan->pekerjaan_suami_istri
    //         ];
    //     } elseif ($tipe === 'anak1') {
    //         $data = [
    //             'nama' => $karyawan->anak1_nama,
    //             'tanggal_lahir' => $karyawan->anak1_tanggal_lahir
    //         ];
    //     } elseif ($tipe === 'anak2') {
    //         $data = [
    //             'nama' => $karyawan->anak2_nama,
    //             'tanggal_lahir' => $karyawan->anak2_tanggal_lahir
    //         ];
    //     } elseif ($tipe === 'anak3') {
    //         $data = [
    //             'nama' => $karyawan->anak3_nama,
    //             'tanggal_lahir' => $karyawan->anak3_tanggal_lahir
    //         ];
    //     }

    //     return response()->json(['data' => $data, 'tipe' => $tipe]);
    // }

    /**
     * Tampilkan formulir untuk menambahkan anggota keluarga baru atau pasien non-karyawan.
     * Menggunakan parameter opsional $karyawan_id.
     */
    public function addKeluarga($karyawan_id = null)
    {
        $karyawan = null;
        if ($karyawan_id) {
            $karyawan = Karyawan::findOrFail($karyawan_id);
        }

        

        return view('karyawan.addKeluarga', compact('karyawan'));
    }

    /**
     * Simpan data anggota keluarga baru atau pasien non-karyawan ke database.
     * Menggunakan parameter opsional $karyawan_id.
     */
    public function storeKeluarga(Request $request, $karyawan_id = null)
    {
        // Logika validasi dan penyimpanan data di sini
        $request->validate([
            'nama' => 'required|string|max:255',
            'tipe' => 'required|in:istri,suami,anak1,anak2,anak3,non_karyawan',
            // Tambahkan validasi lain sesuai kebutuhan
        ]);

        if ($karyawan_id) {
            // Logika untuk menambahkan anggota keluarga karyawan
            $karyawan = Karyawan::findOrFail($karyawan_id);
            if ($request->tipe === 'istri' || $request->tipe === 'suami') {
                $karyawan->suami_istri = $request->nama;
                // Anda dapat menambahkan logika untuk menyimpan jenis kelamin suami/istri jika diperlukan
                // $karyawan->pekerjaan_suami_istri = $request->pekerjaan;
            } elseif ($request->tipe === 'anak1') {
                $karyawan->anak1_nama = $request->nama;
                // $karyawan->anak1_tanggal_lahir = $request->tanggal_lahir;
            }
            // Tambahkan logika untuk anak2 dan anak3
            $karyawan->save();
            return redirect()->route('karyawan.show', $karyawan->id)->with('success', 'Anggota keluarga berhasil ditambahkan!');
        } else {
            // Logika untuk menambahkan pasien non-karyawan
            // Misalnya, simpan ke tabel yang sama atau tabel lain jika diperlukan
            // Contoh:
            // $pasien = new Karyawan();
            // $pasien->nama_karyawan = $request->nama;
            // $pasien->hubungan = 'Non-Karyawan';
            // $pasien->save();
            return redirect()->route('bebas.route.pasien')->with('success', 'Pasien non-karyawan berhasil ditambahkan!');
        }
    }
    
}
