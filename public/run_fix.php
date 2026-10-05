<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\TugasPengganti;
use App\Models\Presensi;

try {
    $tugasList = TugasPengganti::where('status', 'disetujui')->get();
    $updated = 0;

    $activePeriod = \App\Models\TahunAjaran::getActiveSemesterPeriod();
    $periodCode = $activePeriod ? $activePeriod['period_code'] : '';

    foreach ($tugasList as $tugas) {
        $siswas = \App\Models\Siswa::whereHas('rombels', function($q) use ($tugas, $periodCode) {
            $q->where('semester', $periodCode)
              ->whereHas('kelas', function($qk) use ($tugas) {
                  $qk->where('nama_kelas', $tugas->kelas);
              });
        })->pluck('id');

        $hasPresensi = Presensi::where('tanggal', $tugas->tanggal)
            ->whereIn('siswa_id', $siswas) 
            ->exists();

        if ($hasPresensi) {
            $tugas->status = 'selesai';
            $tugas->save();
            $updated++;
        }
    }
    echo "Fresh run completed. Updated $updated records to selesai.";
} catch (\Exception $e) {
    echo "Error on fresh run: " . $e->getMessage();
}
