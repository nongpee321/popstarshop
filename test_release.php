<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = new \App\Http\Controllers\SystemSettingController();
$method = new ReflectionMethod($controller, 'currentPosRelease');
$method->setAccessible(true);
$release = $method->invoke($controller);
echo json_encode($release);
