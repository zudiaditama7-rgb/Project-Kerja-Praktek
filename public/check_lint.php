<?php
header('Content-Type: text/plain; charset=utf-8');

$files = [
    __DIR__ . '/../app/Http/Controllers/WaliKelasController.php',
    __DIR__ . '/../app/Http/Controllers/AdminController.php',
    __DIR__ . '/../app/Http/Controllers/GuruPenggantiController.php',
    __DIR__ . '/../app/Http/Controllers/KepalaSekolahController.php',
    __DIR__ . '/../app/Models/Siswa.php',
    __DIR__ . '/../app/Models/User.php',
    __DIR__ . '/../app/Models/Kelas.php',
    __DIR__ . '/../app/Models/Rombel.php',
    __DIR__ . '/../app/Models/TahunAjaran.php',
    __DIR__ . '/../app/Providers/AppServiceProvider.php',
    __DIR__ . '/../app/Imports/SiswaImport.php',
];

echo "=== PHP Syntax Check ===\n";
echo "PHP Version: " . PHP_VERSION . "\n\n";

$hasError = false;
foreach ($files as $file) {
    $basename = basename($file);
    $output = [];
    $return = 0;
    exec('php -l ' . escapeshellarg(realpath($file)) . ' 2>&1', $output, $return);
    $result = implode("\n", $output);
    if ($return !== 0) {
        echo "[FAIL] $basename\n  $result\n\n";
        $hasError = true;
    } else {
        echo "[OK]   $basename\n";
    }
}

if (!$hasError) {
    echo "\nAll files passed syntax check!\n";
}
