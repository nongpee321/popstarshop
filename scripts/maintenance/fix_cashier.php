<?php
$root = dirname(__DIR__, 2);
require $root.'/vendor/autoload.php';
$app = require_once $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Salesman;

echo "Fixing cashier profile for NongPee...\n";

// Find user NongPee
$user = User::where('email', 'nongpee249@gmail.com')->orWhere('name', 'like', '%NongPee%')->first();

if ($user) {
    // Create or update salesman profile
    $salesman = Salesman::firstOrCreate(
        ['user_id' => $user->id],
        [
            'code' => $user->username ?? 'CASHIER-01',
            'name' => $user->name,
            'is_active' => true,
        ]
    );
    echo "Linked User ID {$user->id} to Salesman ID {$salesman->id}\n";
} else {
    echo "User not found\n";
}
