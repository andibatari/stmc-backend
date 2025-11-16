<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keluarga;
use App\Models\PesertaMcu;

class KeluargaController extends Controller
{
    public function show(PesertaMcu $pesertaMcu)
    {
        return view('keluarga.show', compact('pesertaMcu'));
    }
    public function edit(Keluarga $keluarga)
    {
        return view('keluarga.edit', compact('keluarga'));
    }

    public function destroy(Keluarga $keluarga)
    {
        $keluarga->delete();
        return redirect()->route('karyawan.index')->with('success', 'Data keluarga berhasil dihapus.');
    }
}