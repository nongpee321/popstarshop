import os
import re

path = r'D:\pop-erp-food\ERPPOP-main\app\Http\Controllers\Api\PosApiController.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Remove the two bypass lines
pattern = r'\s*\$branchId = null; // ALWAYS allow all branches for POS login\s*\$assignedUserId = null; // ALWAYS allow any user, ignore device owner restrictions'
content = re.sub(pattern, '', content)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Removed bypass from cashierCandidates")
