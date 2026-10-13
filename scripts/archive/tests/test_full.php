<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$c = app(App\Http\Controllers\Api\PosApiController::class);

$m_candidates = new ReflectionMethod($c, 'cashierCandidates');
$m_candidates->setAccessible(true);
$matches = $m_candidates->invoke($c, 1, 'noname', null)->get();

$m_pin = new ReflectionMethod($c, 'pinMatches');
$m_pin->setAccessible(true);
$matches = $matches->filter(fn ($candidate) => $m_pin->invoke($c, $candidate, '123456'))->values();

echo "Matches count: " . $matches->count() . "\n";
if ($matches->count() > 0) {
    echo "Match user: " . $matches->first()->user->username . "\n";
}
