import re

path = r'D:\pop-erp-food\ERPPOP-main\resources\views\layout.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = 'html[data-color-mode="night"] .card, html[data-color-mode="night"] .dropdown-menu'
if target in content:
    content = content.replace(target, 'html[data-color-mode="night"] .replenish-summary > div, ' + target)
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    print("Replenish summary CSS updated!")
else:
    print("Target not found!")
