<?php
$root = dirname(__DIR__, 2);
require $root.'/vendor/autoload.php';
$app = require_once $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Branch;
use App\Models\WarehouseLocation;
use Illuminate\Support\Facades\DB;

$branchesData = [
    ['code' => '0001', 'name' => 'สำนักงานใหญ่', 'loc' => 'พื้นที่หลัก - สำนักงานใหญ่ (คลังสำนักงานใหญ่)'],
    ['code' => '0002', 'name' => 'สาขาหน้าร้าน', 'loc' => 'พื้นที่หลัก - สาขา-หน้าร้าน'],
    ['code' => '0003', 'name' => 'สาขาห้วยวังนอง', 'loc' => 'พื้นที่หลัก - สาขา-ห้วยวังนอง'],
    ['code' => '0004', 'name' => 'สาขาบ้านปลาดุก', 'loc' => 'พื้นที่หลัก - สาขา-บ้านปลาดุก'],
    ['code' => '0005', 'name' => 'ตลาดดอนกลาง', 'loc' => 'พื้นที่หลัก - ตลาดดอนกลาง'],
    ['code' => '0006', 'name' => 'สาขาสุรินทร์', 'loc' => 'พื้นที่หลัก - สาขาสุรินทร์'],
    ['code' => '0007', 'name' => 'สาขาอำนาจเจริญ', 'loc' => 'พื้นที่หลัก - สาขาอำนาจเจริญ'],
    ['code' => '0008', 'name' => 'สาขาตลาดเจริญศรี', 'loc' => 'พื้นที่หลัก - สาขาตลาดเจริญศรี'],
];

// Clean existing branches
DB::statement('TRUNCATE TABLE branches CASCADE');

foreach ($branchesData as $item) {
    // Find matching warehouse location
    $loc = WarehouseLocation::where('name', $item['loc'])->first();

    Branch::create([
        'code' => $item['code'],
        'name_th' => $item['name'],
        'is_active' => true,
        'default_warehouse_location_id' => $loc ? $loc->id : null,
    ]);
}

echo "Branches updated successfully.\n";
