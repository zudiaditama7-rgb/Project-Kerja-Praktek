<?php
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$response = $kernel->handle($request = Request::capture());

if (Schema::hasColumn('tugas_penggantis', 'keterangan_ditolak')) {
    echo "Column exists!";
} else {
    echo "Column does NOT exist.";
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    echo "\nRan migration. Exists now? " . (Schema::hasColumn('tugas_penggantis', 'keterangan_ditolak') ? "YES" : "NO");
}
