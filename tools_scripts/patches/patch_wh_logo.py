import os
path = r'D:\pop-erp-food\ERPPOP-main\resources\views\wh\index.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = "logo-jet-j-red.png"
new_target = "popstar-shop-logo.png"

if target in content:
    content = content.replace(target, new_target)
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    print("Logo updated successfully in wh/index!")
else:
    print("Target not found!")
