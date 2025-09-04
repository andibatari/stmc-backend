<?php

namespace App\Imports;

use App\Models\Karyawan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;
use App\Models\EmployeeLogin; // Import model EmployeeLogin

class KaryawanImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Langsung return null jika tidak ada data yang valid
        if (!isset($row['no_sap']) || !isset($row['nik_karyawan'])) {
            return null;
        }

        // Gunakan transaksi untuk memastikan kedua entri tersimpan atau tidak sama sekali
        DB::beginTransaction();
        try{
            $karyawan = new Karyawan([
            'id'                    => $row['id'] ?? null,
            'no_sap'                => $row['no_sap'] ?? null,
            'nik_karyawan'          => $row['nik_karyawan'] ?? null,
            'nama_karyawan'         => $row['nama_karyawan'] ?? null,
            'pekerjaan'             => $row['pekerjaan'] ?? null,
            'pendidikan'            => $row['pendidikan'] ?? null,
            'kebangsaan'            => $row['kebangsaan'] ?? null,
            'tempat_lahir'          => $row['tempat_lahir'] ?? null,
            'tanggal_lahir'         => $row['tanggal_lahir'] ?? null,
            'umur'                  => $row['umur'] ?? null,
            'jenis_kelamin'         => $row['jenis_kelamin'] ?? null,
            'golongan_darah'        => $row['golongan_darah'] ?? null,
            'agama'                 => $row['agama'] ?? null,
            'status_pernikahan'     => $row['status_pernikahan'] ?? null,
            'hubungan'              => $row['hubungan'] ?? null,
            'jabatan'               => $row['jabatan'] ?? null,
            'eselon'                => $row['eselon'] ?? null,
            'suami_istri'           => $row['suami_istri'] ?? null,
            'pekerjaan_suami_istri' => $row['pekerjaan_suami_istri'] ?? null,
            'alamat'                => $row['alamat'] ?? null,
            'no_hp'                 => $row['no_hp'] ?? null,
            'email'                 => $row['email'] ?? null,
            'departemens_id'        => $row['departemens_id'] ?? null,
            'unit_kerjas_id'        => $row['unit_kerjas_id'] ?? null,
            'provinsi_id'           => $row['provinsi_id'] ?? null,
            'kabupaten_id'          => $row['kabupaten_id'] ?? null,
            'kecamatan_id'          => $row['kecamatan_id'] ?? null,
            'password'              => bcrypt($row['password'] ?? 'password'),
        ]);

        $karyawan->save(); // Simpan karyawan untuk mendapatkan ID

        // 2. Buat entri EmployeeLogin baru
        if (isset($row['email']) && isset($row['password'])) {
            $login = new EmployeeLogin([
                'karyawan_id' => $karyawan->id, // Gunakan ID karyawan yang baru dibuat
                'no_sap'       => $row['no_sap'],
                'password'    => bcrypt($row['password']),
            ]);
            $login->save();
        }

        DB::commit();
        return $karyawan;
        } catch (\Exception $e) {
            DB::rollBack();
            // Anda bisa log error di sini
            return null;
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }
    
    public function headingRow(): int
    {
        return 1;
    }
}
