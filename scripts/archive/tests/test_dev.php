<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$d = \App\Models\PosDevice::first();
echo json_encode(['branch_id' => $d->branch_id, 'token' => $d->token]);
