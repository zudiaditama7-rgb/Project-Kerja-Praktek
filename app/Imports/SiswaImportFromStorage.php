<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImportFromStorage implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $kelas = $row['kelas'] ?? null;

        // Auto-register the class if it doesn't exist yet
        if ($kelas !== null && trim((string)$kelas) !== '') {
            Kelas::firstOrCreate(['nama_kelas' => trim((string)$kelas)]);
        }

        return new Siswa([
            'nama_siswa' => $row['nama'] ?? $row['nama_siswa'] ?? null,
            'kelas' => $kelas,
            'tahun_ajaran' => $row['tahun_ajaran'] ?? date('Y'),
            'status' => $row['status'] ?? 'aktif',
        ]);
    }
}
