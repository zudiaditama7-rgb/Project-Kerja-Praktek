<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rombels = \App\Models\Rombel::all();
echo "Total Rombels: " . count($rombels) . "\n";

foreach($rombels as $r) {
    echo $r->id . " - " . $r->semester . " - Kelas ID: " . $r->kelas_id . "\n";
}
