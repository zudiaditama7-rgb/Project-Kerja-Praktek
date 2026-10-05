<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tas = \App\Models\TahunAjaran::all();
foreach($tas as $ta) {
    echo "ID: $ta->id - Nama: $ta->nama_tahun - Active: $ta->is_active\n";
}
