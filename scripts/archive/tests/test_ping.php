<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$req = Illuminate\Http\Request::create('/api/pos/ping', 'GET');
$device = \App\Models\PosDevice::first();
if ($device) {
    $req->headers->set('Authorization', 'Bearer '.$device->token);
    $req->attributes->set('pos_device', $device);
    $response = $kernel->handle($req);
    echo $response->getContent();
} else {
    echo "NO DEVICE";
}
