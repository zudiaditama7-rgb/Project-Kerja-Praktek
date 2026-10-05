<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $activePeriod = TahunAjaran::getActiveSemesterPeriod();
        $periodCode = $activePeriod ? $activePeriod['period_code'] : date('Y') . '-Ganjil';

        // Support both 'nis' and 'nisn' column headers
        $nis = $row['nis'] ?? $row['nisn'] ?? null;
        if ($nis !== null) {
            $nis = trim((string) $nis);
            if ($nis === '' || $nis === '-' || $nis === '0') {
                $nis = null;
            }
        }

        // Normalize class value (e.g., remove 'KELAS' prefix if present)
        $kelas = trim((string) ($row['kelas'] ?? ''));
        if (stripos($kelas, 'kelas') === 0) {
            $kelas = trim(substr($kelas, 5));
        }

        // Auto-register the class if it doesn't exist yet
        if ($kelas !== '') {
            Kelas::firstOrCreate([
                'nama_kelas' => $kelas,
                'semester' => $periodCode
            ]);
        }

        // Check if student already exists to prevent duplicates (using NIS/NISN or Name if NIS is null)
        $existingSiswa = null;
        if ($nis !== null) {
            $existingSiswa = Siswa::where('nis', $nis)->first();
        } else {
            $existingSiswa = Siswa::where('nama_siswa', $row['nama_siswa'])->first();
        }

        if ($existingSiswa) {
            $oldKelas = $existingSiswa->kelas;
            $oldStatus = $existingSiswa->status;
            $targetTahun = $row['tahun_ajaran'] ?? ($activePeriod ? $activePeriod['tahun_ajaran'] : date('Y'));

            // Reactivate and update existing student
            $existingSiswa->update([
                'nama_siswa'   => $row['nama_siswa'],
                'kelas'        => $kelas,
                'tahun_ajaran' => $targetTahun,
                'status'       => $row['status'] ?? 'aktif',
            ]);

            // Create a history entry if class or status changes (e.g. from 'lulus' back to 'aktif')
            if ($oldKelas !== $existingSiswa->kelas || $oldStatus !== $existingSiswa->status) {
                \App\Models\RiwayatSiswa::create([
                    'siswa_id' => $existingSiswa->id,
                    'tahun_ajaran' => $targetTahun,
                    'kelas_asal' => $oldKelas,
                    'kelas_tujuan' => $existingSiswa->kelas,
                    'status' => $oldStatus === 'lulus' ? 'Aktif Kembali (Import)' : 'Penyesuaian Manual',
                ]);
            }

            return null; // Do not insert a new record
        }

        return new Siswa([
            'nama_siswa'   => $row['nama_siswa'],
            'nis'          => $nis,
            'kelas'        => $kelas,
            'tahun_ajaran' => $row['tahun_ajaran'] ?? ($activePeriod ? $activePeriod['tahun_ajaran'] : date('Y')),
            'status'       => $row['status'] ?? 'aktif',
        ]);
    }
}
