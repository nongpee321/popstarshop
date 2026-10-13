<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Branch;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\StockDocument;
use App\Models\StockDocumentItem;

$branch = Branch::where('name', 'like', '%สำนักงานใหญ่%')->first();
echo "Branch ID: " . ($branch ? $branch->id : 'Not Found') . "\n";

if ($branch) {
    $warehouse = Warehouse::where('branch_id', $branch->id)->first();
    echo "Warehouse ID: " . ($warehouse ? $warehouse->id : 'Not Found') . "\n";
}

$productCount = Product::count();
echo "Total Products: " . $productCount . "\n";
