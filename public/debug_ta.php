<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

$ta = DB::table('tahun_ajarans')->get();
$rombels = DB::table('rombels')->select('semester', DB::raw('count(*) as count'))->groupBy('semester')->get();
$kelas = DB::table('kelas')->select('semester', DB::raw('count(*) as count'))->groupBy('semester')->get();

echo "<pre>";
echo "TAHUN AJARAN:\n";
print_r($ta->toArray());
echo "\nROMBELS:\n";
print_r($rombels->toArray());
echo "\nKELAS:\n";
print_r($kelas->toArray());
echo "\nACTIVE PERIOD LOGIC:\n";
print_r(\App\Models\TahunAjaran::getActiveSemesterPeriod());
echo "</pre>";
