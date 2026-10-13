with open(r'd:\pop-erp-food\ERPPOP-main\test.php', 'w', encoding='utf-8') as f:
    f.write("""<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\Salesman;
use Illuminate\Support\Facades\Hash;
$cashier = Salesman::with('user')->where('code', 'noname')->first();
if (!$cashier) die("No cashier found\\n");
$pin = '123456';
$matches = false;
if ($cashier->user && Hash::check($pin, $cashier->user->password)) {
    $matches = true;
}
echo 'Matches 123456: ' . ($matches ? 'YES' : 'NO') . "\\n";
$pin2 = '111111';
if ($cashier->user && Hash::check($pin2, $cashier->user->password)) {
    echo "Matches 111111: YES\\n";
}
""")
