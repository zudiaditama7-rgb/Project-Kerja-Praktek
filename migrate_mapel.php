<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;
echo "Running migrations...\n";
Artisan::call('migrate', ['--force' => true]);
echo Artisan::output();
echo "Done.\n";
