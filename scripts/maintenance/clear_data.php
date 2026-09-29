<?php
$root = dirname(__DIR__, 2);
require $root.'/vendor/autoload.php';
$app = require_once $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductUnit;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

echo "Deleting dummy data...\n";

// Delete product
$product = Product::where('sku_code', 'OLD-0001')->first();
if ($product) {
    DB::table('product_barcodes')->where('product_id', $product->id)->delete();
    $product->forceDelete();
    echo "Deleted product OLD-0001\n";
}

// Delete category
$category = ProductCategory::where('code', '101')->first();
if ($category) {
    $category->forceDelete();
    echo "Deleted category 101\n";
}

// Delete employee
$employee = Employee::where('employee_code', 'OLD-001')->first();
if ($employee) {
    $employee->forceDelete();
    echo "Deleted employee OLD-001\n";
}

echo "Data cleared!\n";
