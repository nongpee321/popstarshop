<?php
$root = dirname(__DIR__, 2);
require $root.'/vendor/autoload.php';
$app = require_once $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('email', 'nongpee249@gmail.com')->orWhere('username', 'nongpee249@gmail.com')->first();
if (!$user) {
    // try finding by name
    $user = User::where('name', 'like', '%NongPee%')->first();
}

if ($user) {
    echo "Found user: " . $user->email . " / " . $user->username . "\n";
    $user->password = Hash::make('0887039382Pee');
    $user->save();
    echo "Password updated to: 0887039382Pee\n";
} else {
    echo "User not found! Recreating user...\n";
    $user = User::create([
        'name' => 'NongPee',
        'email' => 'nongpee249@gmail.com',
        'username' => 'nongpee',
        'password' => Hash::make('0887039382Pee'),
        'is_active' => true,
    ]);
    echo "User created!\n";
}
