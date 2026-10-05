<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$requestKelas = "1";
$activePeriod = \App\Models\TahunAjaran::getActiveSemesterPeriod();
$periodCode = $activePeriod ? $activePeriod['period_code'] : '';

echo "Period Code: $periodCode\n";

$query = \App\Models\Siswa::query();
$query->whereHas('rombels', function($q) use ($requestKelas, $periodCode) {
    $q->where('semester', $periodCode)
      ->whereHas('kelas', function($qk) use ($requestKelas) {
          $qk->where('nama_kelas', $requestKelas);
      });
});

$sql = $query->toSql();
$bindings = $query->getBindings();

echo "SQL: $sql\n";
echo "Bindings: " . json_encode($bindings) . "\n";

$count = $query->count();
echo "Count: $count\n";

$siswas = $query->get();
if ($count > 0) {
    echo "First Student: " . $siswas->first()->nama_siswa . "\n";
}
