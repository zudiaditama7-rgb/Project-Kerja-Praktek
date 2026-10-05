<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

// Create column if it doesn't exist
if (!Schema::hasColumn('presensis', 'nama_mapel')) {
    Schema::table('presensis', function (Blueprint $table) {
        $table->string('nama_mapel')->nullable()->after('nama_pengganti');
    });
    echo "Column nama_mapel added successfully.\n";
} else {
    echo "Column nama_mapel already exists.\n";
}

