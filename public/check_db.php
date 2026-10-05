<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\TugasPengganti;

$all = TugasPengganti::all();
foreach($all as $t) {
    echo "ID: {$t->id}, Kelas: {$t->kelas}, Status: '{$t->status}'<br>";
}
