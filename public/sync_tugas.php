<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\TugasPengganti;
use App\Models\Presensi;
use Illuminate\Support\Facades\DB;

try {
    // 1. Alter the table first to accept 'selesai'
    DB::statement("ALTER TABLE tugas_penggantis MODIFY COLUMN status ENUM('pending', 'disetujui', 'ditolak', 'selesai') DEFAULT 'pending'");
    echo "Table schema altered successfully.\n<br>";

    // 2. Perform the data sync
    $tugasList = TugasPengganti::where('status', 'disetujui')->get();
    $updated = 0;

    foreach ($tugasList as $tugas) {
        $hasPresensi = Presensi::where('tanggal', $tugas->tanggal)
            ->where('nama_pengganti', '!=', null) 
            ->exists();

        if ($hasPresensi) {
            $tugas->status = 'selesai';
            $tugas->save();
            $updated++;
        }
    }
    echo "Updated $updated records to selesai.";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
