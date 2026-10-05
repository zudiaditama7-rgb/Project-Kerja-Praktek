<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\TugasPengganti;
use App\Models\User;

$tugas = TugasPengganti::where('status', 'disetujui')->get();
$gurus = User::where('role', 'guru_pengganti')->get();

echo "Assigned Tugas:\n";
foreach ($tugas as $t) {
    echo "ID: {$t->id}, GuruPenggantiID: {$t->guru_pengganti_id}, Kelas: {$t->kelas}, Status: {$t->status}\n";
}

echo "\nGuru Pengganti Users:\n";
foreach ($gurus as $g) {
    echo "ID: {$g->id}, Name: {$g->name}\n";
}
