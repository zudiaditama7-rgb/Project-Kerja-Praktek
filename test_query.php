<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$activePeriod = \App\Models\TahunAjaran::getActiveSemesterPeriod();
$periodCode = $activePeriod ? $activePeriod['period_code'] : '';

echo "Active Period Code: " . $periodCode . "\n";

$kelasCount = \App\Models\Kelas::where('semester', $periodCode)->count();
echo "Kelas count for period: " . $kelasCount . "\n";

$rombelCount = \App\Models\Rombel::where('semester', $periodCode)->count();
echo "Rombel count for period: " . $rombelCount . "\n";

$rombelsFirst = \App\Models\Rombel::first();
echo "Sample Rombel: " . json_encode($rombelsFirst) . "\n";

$kelasFirst = \App\Models\Kelas::first();
echo "Sample Kelas: " . json_encode($kelasFirst) . "\n";
