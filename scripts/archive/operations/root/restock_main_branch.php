<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

$locationId = 12; // สำนักงานใหญ่ Location
$addQty = 15;

$products = Product::all();
$count = 0;

DB::beginTransaction();
try {
    foreach ($products as $product) {
        // Update or Create Stock Balance
        $balance = StockBalance::firstOrNew([
            'product_id' => $product->id,
            'warehouse_location_id' => $locationId
        ]);
        
        $balance->on_hand_qty = ($balance->on_hand_qty ?? 0) + $addQty;
        $balance->reserved_qty = $balance->reserved_qty ?? 0;
        $balance->save();
        
        // Add movement log
        StockMovement::create([
            'product_id' => $product->id,
            'warehouse_location_id' => $locationId,
            'movement_type' => 'adjust_in',
            'qty' => $addQty,
            'movement_date' => now(),
            'stock_lot_id' => null,
            'document_id' => null
        ]);
        
        $count++;
    }
    DB::commit();
    echo "Successfully restocked $count products by $addQty units at branch ID 6 (Location 12).\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
