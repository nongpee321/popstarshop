import re

path = r'D:\pop-erp-food\ERPPOP-main\resources\views\bplus-ops\pos-workbench.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace('var(--erp-surface-2)', 'var(--erp-surface)')

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Replaced erp-surface-2")
