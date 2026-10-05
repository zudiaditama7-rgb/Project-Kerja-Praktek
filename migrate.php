<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;
use App\Models\User;

try {
    echo "Running migrations...\n";
    Artisan::call('migrate', ['--force' => true]);
    echo Artisan::output();
    
    echo "Updating existing users...\n";
    $users = User::all();
    foreach($users as $user) {
        if ($user->role && empty($user->roles)) {
            $user->roles = [$user->role];
            $user->save();
        }
    }
    echo "Done.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
