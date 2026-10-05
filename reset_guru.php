<?php
// Quick script to reset guru pengganti password
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('role', 'guru_pengganti')->first();

if ($user) {
    echo "Found user:\n";
    echo "Name: " . $user->name . "\n";
    echo "Username: " . $user->username . "\n";
    echo "Role: " . $user->role . "\n\n";
    
    // Reset password to '123456'
    $user->password = Hash::make('123456');
    $user->save();
    
    echo "Password has been reset to: 123456\n";
} else {
    echo "Guru pengganti user not found.\n";
}
