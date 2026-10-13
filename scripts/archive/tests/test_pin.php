<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$c = app(App\Http\Controllers\Api\PosApiController::class);
$m = new ReflectionMethod($c, 'cashierCandidates');
$m->setAccessible(true);
$salesman = $m->invoke($c, 1, 'noname', null)->first();

$pm = new ReflectionMethod($c, 'pinMatches');
$pm->setAccessible(true);
$result = $pm->invoke($c, $salesman, '123456');
echo "pinMatches: " . ($result ? 'TRUE' : 'FALSE') . "\n";
