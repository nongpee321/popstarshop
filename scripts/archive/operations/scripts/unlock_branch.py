import os

path = r'D:\pop-erp-food\ERPPOP-main\app\Http\Controllers\PosController.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

old = "$lockedBranch = $authUser?->branch;"
new = "$lockedBranch = null; // $authUser?->branch; // Temporarily unlocked for testing Branch Switcher"
content = content.replace(old, new)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Unlocked branch switcher in Web POS")
