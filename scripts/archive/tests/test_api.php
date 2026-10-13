<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = app(\App\Http\Controllers\PosController::class);
$request = \Illuminate\Http\Request::create('/api/pos/products', 'GET', ['all' => 1]);
$response = $controller->products($request);

$products = json_decode($response->getContent(), true);
foreach ($products as $p) {
    if (strpos($p['sku_code'], 'SKU-NA9DF4') !== false || strpos($p['name_th'], 'BB-B') !== false) {
        print_r($p);
    }
}
