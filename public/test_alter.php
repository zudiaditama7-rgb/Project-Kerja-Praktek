<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

try {
    $res = DB::statement("ALTER TABLE tugas_penggantis MODIFY COLUMN status ENUM('pending', 'disetujui', 'ditolak', 'selesai') DEFAULT 'pending'");
    echo "Alter result: " . ($res ? 'true' : 'false') . "<br>";
    
    $cols = DB::select("SHOW COLUMNS FROM tugas_penggantis LIKE 'status'");
    print_r($cols);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
