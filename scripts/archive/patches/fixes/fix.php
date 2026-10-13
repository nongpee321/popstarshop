<?php
$f = '/app/app/Http/Controllers/PosController.php';
$c = file_get_contents($f);
$old = <<<'EOF'
    private function enforcedBranchId(?int $requested): int
    {
        $device = request()->attributes->get('pos_device');
        $authBranch = (int) ($device?->branch_id ?: auth()->user()?->branch_id);
EOF;
$new = <<<'EOF'
    private function enforcedBranchId(?int $requested): int
    {
        $device = request()->attributes->get('pos_device');
        
        $activeCashierBranch = null;
        if ($device && $device->verifiedCashierId()) {
            $activeCashierBranch = $device->activeCashier?->branch_id;
        }
        
        $authBranch = (int) ($activeCashierBranch ?: $device?->branch_id ?: auth()->user()?->branch_id);
EOF;
$c = str_replace($old, $new, $c);
file_put_contents($f, $c);
echo "Replaced in PosController.php!\n";
