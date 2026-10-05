<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\Artisan;

try {
    Artisan::call('migrate', ['--force' => true]);
    echo nl2br(Artisan::output());
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
