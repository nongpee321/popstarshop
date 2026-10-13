<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$input = file_get_contents('php://stdin');
if (empty($input)) {
    echo json_encode(['error' => 'No query provided']);
    exit;
}

try {
    $result = eval($input);
    echo json_encode(['success' => true, 'data' => $result], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (\Throwable $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
