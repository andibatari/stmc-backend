<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PemantauanLingkungan;
use App\Models\Lokasi;
use App\Models\Area;

class PemantauanLingkunganController extends Controller
{
    /**
     * Menampilkan daftar semua data pemantauan lingkungan.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil semua data pemantauan lingkungan
        $dataPemantauan = PemantauanLingkungan::all();
        
        // Menampilkan view dengan data pemantauan
        return view('pemantauan.index', compact('dataPemantauan'));
    }

    /**
     * Menyimpan data pemantauan lingkungan baru.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function simpan(Request $request)
    {
        // Validasi semua data input dari formulir
        $request->validate([
            'lokasi_id' => 'required|exists:lokasis,id',
            'area_id' => 'required|exists:areas,id',
            'tanggal' => 'required|date',
            'cahaya' => 'nullable|numeric',
            'bising' => 'nullable|numeric',
            'debu' => 'nullable|string',
            'suhu_basah' => 'nullable|numeric',
            'suhu_kering' => 'nullable|numeric',
            'suhu_radiasi' => 'nullable|numeric',
            'isbb_indoor' => 'nullable|numeric',
            'isbb_outdoor' => 'nullable|numeric',
            'rh' => 'nullable|numeric',
        ]);
        
        // Membuat entri baru di tabel `pemantauan_lingkungan`
        PemantauanLingkungan::create($request->all());
        
        // Kembali ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', 'Data pemantauan berhasil disimpan.');
    }
}
