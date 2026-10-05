<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$u = App\Models\User::where('name', 'like', '%wahyu%')->first();
if ($u) {
    echo "Name: " . $u->name . "\n";
    echo "Roles: " . json_encode($u->roles) . "\n";
    echo "Active Role: " . $u->role . "\n";
    echo "Kelas: " . $u->kelas . "\n";
    echo "Kelas Mapel: " . json_encode($u->kelas_mapel) . "\n";
    echo "Kelas Mapel Raw: " . $u->getRawOriginal('kelas_mapel') . "\n";
} else {
    echo "User not found\n";
}
