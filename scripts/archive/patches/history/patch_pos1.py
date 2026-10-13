import os

path = r'D:\pop-erp-food\ERPPOP-main\app\Http\Controllers\PosController.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

old_func = """    private function enforcedBranchId(?int $requested): int
    {
        if ($requested) {
            return $requested;
        }
        
        $device = request()->attributes->get('pos_device');
        if ($device) {
            $branchId = (int) ($device->branch_id ?: auth()->user()?->branch_id);
        } else {
            $branchId = (int) (auth()->user()?->branch_id);
        }
        
        if (! $branchId) {
            $branchId = (int) \App\Models\Branch::where('is_active', true)->value('id') ?: 1;
        }
        return $branchId;
    }"""

new_func = """    private function enforcedBranchId(?int $requested): int
    {
        $device = request()->attributes->get('pos_device');
        $authBranch = (int) ($device?->branch_id ?: auth()->user()?->branch_id);
        
        if ($requested && $requested !== $authBranch) {
            if (! auth()->user()?->isSuperAdmin()) {
                $requested = null;
            }
        }
        
        if ($requested) {
            return $requested;
        }

        if (! $authBranch) {
            $authBranch = (int) \App\Models\Branch::where('is_active', true)->value('id') ?: 1;
        }
        return $authBranch;
    }"""

content = content.replace(old_func, new_func)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Patched PosController.php enforcedBranchId")
