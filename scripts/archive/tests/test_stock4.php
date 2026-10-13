<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\StockBalance;

$stock = StockBalance::first();
echo "StockBalance schema: " . json_encode($stock) . "\n";
