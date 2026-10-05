<?php
// Quick script to reset guru pengganti password - access via browser
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('role', 'guru_pengganti')->first();

if ($user) {
    // Reset password to '123456'
    $user->password = Hash::make('123456');
    $user->save();
    
    echo "<h2>Guru Pengganti Account</h2>";
    echo "<p><b>Username:</b> " . $user->username . "</p>";
    echo "<p><b>Password telah direset ke:</b> 123456</p>";
    echo "<br><a href='/'>Kembali ke Login</a>";
} else {
    echo "Guru pengganti user not found.";
}
