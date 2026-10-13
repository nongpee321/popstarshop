<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Warehouse;
use App\Models\WarehouseLocation;

$warehouses = Warehouse::where('branch_id', 6)->get();
echo "Warehouses: " . json_encode($warehouses) . "\n";

$locations = WarehouseLocation::whereIn('warehouse_id', $warehouses->pluck('id'))->get();
echo "Locations: " . json_encode($locations) . "\n";
