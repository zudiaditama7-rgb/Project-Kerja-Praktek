<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = 'storage/data siswa kelas 1-6.xlsx';

if (!file_exists($filePath)) {
    echo "File tidak ditemukan: $filePath\n";
    exit(1);
}

try {
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();
    
    echo "Total baris: " . count($rows) . "\n";
    echo "=================================\n";
    
    foreach (array_slice($rows, 0, 10) as $idx => $row) {
        echo "Baris " . ($idx + 1) . ": " . json_encode($row) . "\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
