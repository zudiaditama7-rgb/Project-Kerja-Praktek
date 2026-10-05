<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

$kelas = DB::table('kelas')->where('semester', '2026/2027-Ganjil')->get();
echo "<pre>";
print_r($kelas->toArray());
echo "</pre>";
