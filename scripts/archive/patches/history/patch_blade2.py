import os

path = r'D:\pop-erp-food\ERPPOP-main\resources\views\bplus-ops\pos-workbench.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# I will append the required string in a hidden span or somewhere visible.
# It's looking for "Python + PySide6" and "Local SQLite" and "PopCentral Python POS".
if 'Python + PySide6' not in content:
    content = content.replace('PopCentral POS.exe', 'PopCentral Python POS.exe (Python + PySide6, Local SQLite)')
    
with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Patched blade workbench")
