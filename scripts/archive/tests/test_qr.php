<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$branchId = 6;
$qrPayment = \App\Models\QrPaymentConfig::query()
    ->where('is_active', true)
    ->with('bankAccount:id,branch_id,bank_name,account_name')
    ->where(function ($query) use ($branchId) {
        $query->whereNull('bank_account_id')
            ->orWhereHas('bankAccount', fn ($bank) => $bank
                ->whereNull('branch_id')
                ->when($branchId, fn ($branch) => $branch->orWhere('branch_id', $branchId)));
    })
    ->orderBy('id')
    ->first();
echo json_encode($qrPayment);
