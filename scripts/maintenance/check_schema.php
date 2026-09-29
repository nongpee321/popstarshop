<?php
$root = dirname(__DIR__, 2);
require $root.'/vendor/autoload.php';
$app = require_once $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

echo 'ProductCategory: ' . json_encode(Schema::getColumnListing('product_categories')) . "\n";
echo 'Product: ' . json_encode(Schema::getColumnListing('products')) . "\n";
