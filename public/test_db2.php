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

// Fix the semester bug for rombels that have 'Genap-Ganjil' appended
$fixedCount = 0;
foreach (\App\Models\Rombel::all() as $rombel) {
    if (strpos($rombel->semester, 'Genap-Ganjil') !== false) {
        // Find the matching kelas
        $kelas = \App\Models\Kelas::find($rombel->kelas_id);
        if ($kelas) {
            // Find active period class with same name
            $activeKelas = \App\Models\Kelas::where('nama_kelas', $kelas->nama_kelas)
                ->where('semester', $periodCode)
                ->first();
            
            if ($activeKelas) {
                $rombel->kelas_id = $activeKelas->id;
                $rombel->semester = $periodCode;
                $rombel->save();
                $fixedCount++;
            }
        }
    }
}
echo "\nFixed $fixedCount rombels\n";
