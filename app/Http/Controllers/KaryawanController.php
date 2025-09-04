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
        // // // Ambil data untuk dropdown dari database
        // $unitKerjas = \App\Models\UnitKerja::all();
        // $departemens = \App\Models\Departemen::all();
        
        // return view('karyawan.create', compact('unitKerjas', 'departemens'));

        // // Tidak perlu mengambil data dropdown di sini karena Livewire yang menanganinya
        return view('karyawan.create');
    }

    /**
     * Simpan karyawan yang baru dibuat ke database.
     */
    // public function store(Request $request)
    // {
    //     // Validasi data input dari admin
    //     $request->validate([
    //         'no_sap' => 'required|string|unique:karyawans,no_sap',
    //         'nik_karyawan' => 'required|string|unique:karyawans,nik_karyawan',
    //         'nama_karyawan' => 'required|string|max:255',
    //         'jenis_kelamin' => 'required|string',
    //         'unit_kerjas_id' => 'required|exists:unit_kerjas,id',
    //         'departemens_id' => 'required|exists:departemens,id',
    //         'tanggal_lahir' => 'required|date',
    //         'alamat' => 'required|string',
    //         'email' => 'required|email|unique:karyawans,email',
    //         'no_hp' => 'required|string',
    //         'password' => 'required|min:6',
    //     ]);
        
    //     // 1. Buat entri baru di tabel 'karyawans'
    //     $karyawan = Karyawan::create($request->except(['password']));
        
    //     // 2. Buat entri login di tabel 'employee_logins'
    //     EmployeeLogin::create([
    //         'karyawan_id' => $karyawan->id,
    //         'no_sap' => $request->no_sap,
    //         'password' => Hash::make($request->password),
    //     ]);

    //     return redirect()->route('karyawan.index')->with('status', 'Data karyawan berhasil ditambahkan.');
    // }

    /**
     * Tampilkan form untuk mengedit data karyawan.
     */
    public function edit(Karyawan $karyawan)
    {
        // Mendapatkan semua data untuk dropdown
        $departemens = Departemen::all();
        $unitKerjas = UnitKerja::all();
        $provinsis = Provinsi::all();
        
        // Memuat data kabupaten dan kecamatan berdasarkan data karyawan
        $kabupatens = collect();
        $kecamatans = collect();
        if ($karyawan->kecamatan_id) {
            $kecamatan = Kecamatan::find($karyawan->kecamatan_id);
            if ($kecamatan) {
                $kabupatens = Kabupaten::where('provinsi_id', $kecamatan->kabupaten->provinsi_id)->get();
                $kecamatans = Kecamatan::where('kabupaten_id', $kecamatan->kabupaten_id)->get();
            }
        }

        return view('karyawan.edit', compact(
            'karyawan', 
            'departemens', 
            'unitKerjas', 
            'provinsis', 
            'kabupatens', 
            'kecamatans'
        ));
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
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\KaryawanExport, 'karyawan.xlsx');
    }

    
}
