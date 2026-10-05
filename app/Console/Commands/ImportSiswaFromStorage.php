<?php

namespace App\Console\Commands;

use App\Models\Siswa;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportSiswaFromStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'siswa:import-from-storage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data siswa dari file Excel di storage/data siswa kelas 1-6.xlsx';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = storage_path('data siswa kelas 1-6.xlsx');

        if (!file_exists($filePath)) {
            $this->error("File tidak ditemukan: {$filePath}");
            return 1;
        }

        try {
            $this->info('Mulai import data siswa...');

            // Baca file Excel menggunakan PhpSpreadsheet
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (empty($rows)) {
                $this->error('File Excel kosong!');
                return 1;
            }

            $imported = 0;
            $skipped = 0;

            // Scan seluruh baris untuk menemukan blok Kelas dan header tabel
            $currentKelas = null;
            $headerMap = null; // kolom => nama header
            $inTable = false;

            foreach ($rows as $rowIndex => $row) {
                // Skip baris kosong
                if (empty(array_filter($row))) {
                    // If we were in a table, end the table on an empty row
                    $inTable = false;
                    $headerMap = null;
                    continue;
                }

                // Cari kata 'Kelas' di seluruh sel baris (case-insensitive)
                foreach ($row as $cell) {
                    if ($cell === null) continue;
                    if (preg_match('/kelas\s*[:\-]?\s*(\d+)/i', trim($cell), $m)) {
                        $currentKelas = trim($m[1]);
                        $this->line("📋 Ditemukan blok Kelas: {$currentKelas} (baris " . ($rowIndex+1) . ")");
                        // reset state
                        $inTable = false;
                        $headerMap = null;
                        break;
                    }
                }

                // Jika belum ada kelas sekarang, skip sampai kita menemukan kelas
                if (empty($currentKelas)) {
                    continue;
                }

                // Detect header row (mengandung kata 'No' dan 'Nama' atau 'Nama Lengkap')
                $joined = strtolower(implode(' ', array_map(function($c){ return $c === null ? '' : trim($c); }, $row)));
                if (preg_match('/\b(no|no\.)\b/i', $joined) && preg_match('/nama/i', $joined)) {
                    // Build header map: index => normalized header
                    $headerMap = [];
                    foreach ($row as $i => $cell) {
                        if ($cell === null) continue;
                        $h = strtolower(trim($cell));
                        $h = str_replace([' ', "\n", "\r", "\t"], '_', $h);
                        $headerMap[$i] = $h;
                    }
                    $inTable = true;
                    $this->line("   -> Header tabel terdeteksi (baris " . ($rowIndex+1) . ")");
                    continue;
                }

                // Jika sedang di dalam tabel, proses baris sebagai data siswa
                if ($inTable && $headerMap) {
                    // Cari kolom nama berdasarkan headerMap
                    $nama = null;
                    foreach ($headerMap as $i => $h) {
                        if (strpos($h, 'nama') !== false) {
                            $nama = isset($row[$i]) ? trim($row[$i]) : null;
                            break;
                        }
                    }

                    // Jika nama kosong, skip
                    if (empty($nama)) {
                        $skipped++;
                        continue;
                    }

                    try {
                        // Check jika siswa sudah ada
                        $exists = Siswa::where('nama_siswa', $nama)
                            ->where('kelas', $currentKelas)
                            ->first();

                        if ($exists) {
                            $this->line("ℹ️  {$nama} sudah ada di Kelas {$currentKelas}");
                            $skipped++;
                            continue;
                        }

                        Siswa::create([
                            'nama_siswa' => $nama,
                            'kelas' => $currentKelas,
                            'tahun_ajaran' => date('Y'),
                            'status' => 'aktif',
                        ]);

                        $this->line("✓ {$nama} (Kelas {$currentKelas})");
                        $imported++;

                    } catch (\\Exception $e) {
                        $this->error("✗ Error baris " . ($rowIndex + 1) . ": " . $e->getMessage());
                        $skipped++;
                    }
                }
            }

            $this->info("\n" . str_repeat("=", 60));
            $this->info("✓ Import selesai!");
            $this->line("  Berhasil import: {$imported} siswa");
            $this->line("  Dilewati: {$skipped}");
            $this->info(str_repeat("=", 60));

            return 0;

        } catch (\\Exception $e) {
            $this->error('Error saat import: ' . $e->getMessage());
            return 1;
        }
    }
}
