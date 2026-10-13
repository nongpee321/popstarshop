<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$user = App\Models\User::where('username', 'noname')->first();
$has = $user->roles()->whereHas('permissions', fn($p) => $p->where('code', 'pos.sell'))->exists();
echo "Has global pos.sell: " . ($has ? 'YES' : 'NO') . "\n";
