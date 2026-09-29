<?php

use App\Models\Product;

$root = dirname(__DIR__, 2);
require $root . '/vendor/autoload.php';
$app = require_once $root . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$csvFile = $root . '/storage/reference-data/scraped_images.csv';

if (!file_exists($csvFile)) {
    die("ไม่พบไฟล์ CSV: $csvFile\n");
}

$file = fopen($csvFile, 'r');

// Detect BOM and skip it
$bom = fread($file, 3);
if ($bom !== "\xEF\xBB\xBF") {
    rewind($file); // Not a BOM, rewind to beginning
}

$headers = fgetcsv($file); // ['name', 'image_url']

$updatedCount = 0;
$notFoundCount = 0;

while (($row = fgetcsv($file)) !== false) {
    if (count($row) < 2) continue;
    
    $name = trim($row[0]);
    $imageUrl = trim($row[1]);

    if (!$name || !$imageUrl) continue;

    $updated = Product::where('name_th', $name)->update([
        'image_url' => $imageUrl
    ]);

    if ($updated) {
        $updatedCount++;
    } else {
        $notFoundCount++;
    }
}

fclose($file);

echo "อัปเดตรูปภาพสำเร็จ: $updatedCount รายการ\n";
echo "ไม่พบสินค้าในระบบ: $notFoundCount รายการ\n";
