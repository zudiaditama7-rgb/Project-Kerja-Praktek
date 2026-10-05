<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$hasTable = \Illuminate\Support\Facades\Schema::hasTable('tugas_penggantis');
echo "Has Table: " . ($hasTable ? 'YES' : 'NO');
