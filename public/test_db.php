<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$activePeriod = \App\Models\TahunAjaran::getActiveSemesterPeriod();
$periodCode = $activePeriod ? $activePeriod['period_code'] : '';

echo "Active Period Code: " . $periodCode . "\n\n";

$distinctRombelSemesters = \App\Models\Rombel::select('semester')->distinct()->pluck('semester');
echo "Distinct Rombel Semesters:\n";
print_r($distinctRombelSemesters->toArray());

$distinctKelasSemesters = \App\Models\Kelas::select('semester')->distinct()->pluck('semester');
echo "\nDistinct Kelas Semesters:\n";
print_r($distinctKelasSemesters->toArray());
