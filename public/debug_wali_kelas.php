<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

$kelas = DB::table('kelas')
    ->leftJoin('users', 'kelas.wali_kelas_id', '=', 'users.id')
    ->where('kelas.semester', '2026/2027-Ganjil')
    ->orderBy('kelas.nama_kelas')
    ->select('kelas.nama_kelas', 'users.name', 'users.id')
    ->get();

echo "<pre>";
foreach ($kelas as $k) {
    echo "Kelas: " . $k->nama_kelas . " | Wali Kelas: " . ($k->name ?? 'Belum Ada') . " (ID: " . $k->id . ")\n";
}
echo "</pre>";
