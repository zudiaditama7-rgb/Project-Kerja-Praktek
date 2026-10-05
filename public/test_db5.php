<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = new \Illuminate\Http\Request(['kelas' => '1']);
$controller = app(\App\Http\Controllers\AdminController::class);
$response = $controller->siswaIndex($request);

echo "View Name: " . $response->name() . "\n";
$siswas = $response->getData()['siswas'];
echo "Siswas Count in View: " . count($siswas) . "\n";
echo "Total Paginated: " . $siswas->total() . "\n";
