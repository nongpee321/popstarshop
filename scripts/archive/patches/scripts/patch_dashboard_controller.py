import re

file_path = r"d:\pop-erp-food\ERPPOP-main\app\Http\Controllers\DashboardController.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Modify branchId logic
old_logic = """
        $branchId = auth()->user()?->branchScopeId();
        $scopeBranchName = $branchId ? Branch::whereKey($branchId)->value('name_th') : null;
"""
new_logic = """
        $userBranchId = auth()->user()?->branchScopeId();
        
        // Let user select branch from dropdown, if they are admin they can see all
        if ($request->filled('branch_id')) {
            $branchId = $request->input('branch_id') === 'all' ? null : (int) $request->input('branch_id');
        } else {
            $branchId = $userBranchId;
        }
        
        // If user is restricted to a specific branch, force it back (security check)
        if ($userBranchId !== null && $branchId !== $userBranchId) {
            $branchId = $userBranchId;
        }
        
        $scopeBranchName = $branchId ? Branch::whereKey($branchId)->value('name_th') : null;
        $allBranches = Branch::orderBy('code')->get();
"""
content = content.replace(old_logic.strip(), new_logic.strip())

# Add allBranches and currentBranchId to the view response
old_return = "'cashVariance' => $cashVariance,"
new_return = "'cashVariance' => $cashVariance,\n            'allBranches' => $allBranches,\n            'currentBranchId' => $branchId,"
content = content.replace(old_return, new_return)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated DashboardController.php")
