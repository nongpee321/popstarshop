<?php
$root = dirname(__DIR__, 2);
require $root.'/vendor/autoload.php';
$app = require_once $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\Schema;

echo "Media library classes: \n";
echo "HasMedia: " . (interface_exists('Spatie\MediaLibrary\HasMedia') ? 'Yes' : 'No') . "\n";

echo "Has table media? " . (Schema::hasTable('media') ? 'Yes' : 'No') . "\n";
echo "Has table product_images? " . (Schema::hasTable('product_images') ? 'Yes' : 'No') . "\n";

$rc = new ReflectionClass(Product::class);
echo "Product Traits: \n" . implode(", ", array_keys($rc->getTraits())) . "\n";
