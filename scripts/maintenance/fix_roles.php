<?php
$root = dirname(__DIR__, 2);
require $root.'/vendor/autoload.php';
$app = require_once $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\Salesman;

$user = User::where('email', 'nongpee249@gmail.com')->first();

if ($user) {
    // Re-assign all roles
    $roles = Role::pluck('id');
    $user->roles()->sync($roles);
    echo "Assigned all roles.\n";
    
    // Re-create salesman profile
    $salesman = Salesman::firstOrCreate(
        ['user_id' => $user->id],
        [
            'code' => 'nongpee',
            'name' => 'NongPee',
            'is_active' => true,
        ]
    );
    echo "Re-created Salesman profile.\n";
}
