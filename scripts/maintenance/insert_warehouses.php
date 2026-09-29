<?php
$root = dirname(__DIR__, 2);
require $root.'/vendor/autoload.php';
$app = require_once $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Warehouse;
use App\Models\WarehouseLocation;

$data = [
    ['wh' => 'คลังสำนักงานใหญ่', 'loc' => 'พื้นที่หลัก - สำนักงานใหญ่ (คลังสำนักงานใหญ่)'],
    ['wh' => 'คลังหลัก - สาขา-หน้าร้าน', 'loc' => 'พื้นที่หลัก - สาขา-หน้าร้าน'],
    ['wh' => 'คลังหลัก - สาขา-ห้วยวังนอง', 'loc' => 'พื้นที่หลัก - สาขา-ห้วยวังนอง'],
    ['wh' => 'คลังหลัก - สาขา-บ้านปลาดุก', 'loc' => 'พื้นที่หลัก - สาขา-บ้านปลาดุก'],
    ['wh' => 'คลังหลัก - ตลาดดอนกลาง', 'loc' => 'พื้นที่หลัก - ตลาดดอนกลาง'],
    ['wh' => 'คลังหลัก - สาขาสุรินทร์', 'loc' => 'พื้นที่หลัก - สาขาสุรินทร์'],
    ['wh' => 'คลังหลัก - สาขาอำนาจเจริญ', 'loc' => 'พื้นที่หลัก - สาขาอำนาจเจริญ'],
    ['wh' => 'คลังหลัก - สาขาตลาดเจริญศรี', 'loc' => 'พื้นที่หลัก - สาขาตลาดเจริญศรี'],
];

foreach ($data as $item) {
    // Generate a code for the warehouse
    $whCode = 'WH-' . strtoupper(substr(md5($item['wh']), 0, 4));
    
    $warehouse = Warehouse::firstOrCreate(
        ['name' => $item['wh']],
        ['code' => $whCode]
    );

    WarehouseLocation::updateOrCreate(
        ['warehouse_id' => $warehouse->id, 'code' => 'MAIN'],
        ['name' => $item['loc']]
    );
}

echo "Warehouses and Locations inserted successfully.\n";
