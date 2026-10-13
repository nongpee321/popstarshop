import re
file_path = r"d:\pop-erp-food\ERPPOP-main\app\Http\Controllers\DashboardController.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

old_code = """        // If user is restricted to a specific branch, force it back (security check)
        if ($userBranchId !== null && $branchId !== $userBranchId) {
            $branchId = $userBranchId;
        }"""
        
new_code = """        // อนุญาตให้เลือกดูสาขาอื่น หรือรวมยอดทุกสาขาได้ตามที่ผู้ใช้ร้องขอ
        // ยกเลิกการบังคับล็อคสาขาสำหรับหน้า Dashboard
        // if ($userBranchId !== null && $branchId !== $userBranchId) {
        //     $branchId = $userBranchId;
        // }"""

content = content.replace(old_code, new_code)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Disabled branch locking logic.")
