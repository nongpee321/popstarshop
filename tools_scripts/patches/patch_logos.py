import os

path = r'D:\pop-erp-food\ERPPOP-main'
files_to_fix = [
    r'resources\views\dashboard-odoo.blade.php',
    r'resources\views\dashboard.blade.php',
    r'resources\views\auth\change-password.blade.php',
    r'resources\views\auth\login.blade.php',
    r'resources\views\pos\index.blade.php',
    r'resources\views\wh\index.blade.php'
]

target = 'logo-jet-erp-mark.svg'
new_target = 'logo-jet-j-red.png'

for fpath in files_to_fix:
    full = os.path.join(path, fpath)
    if os.path.exists(full):
        with open(full, 'r', encoding='utf-8') as f:
            content = f.read()
        
        if target in content:
            content = content.replace(target, new_target)
            with open(full, 'w', encoding='utf-8') as f:
                f.write(content)
            print(f"Fixed {fpath}")
