import os
import re

path = r'D:\pop-erp-food\ERPPOP-main\app\Http\Controllers\PosController.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# I will fix the defaultBranchId logic
old_logic = """        $lockedBranch = null; // $authUser?->branch; // Temporarily unlocked for testing Branch Switcher
        $lockedCashier = $authUser?->posCashierProfile;
        
        $defaultBranchId = null;
        if ($lockedBranch) {
            $defaultBranchId = $lockedBranch->id;
        }"""

new_logic = """        $lockedBranch = null; // Unlocked for testing Branch Switcher
        $lockedCashier = $authUser?->posCashierProfile;
        
        $defaultBranchId = $authUser?->branch_id;"""

content = content.replace(old_logic, new_logic)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Set default branch ID for unlocked dropdown")
