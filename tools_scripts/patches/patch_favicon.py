import os

path = r'D:\pop-erp-food\ERPPOP-main\resources\views\layout.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = "'images/logo-jet-erp-mark.svg'"
new_target = "'images/logo-jet-j-red.png'"

if target in content:
    content = content.replace(target, new_target)
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    print("Favicon updated successfully!")
else:
    print("Favicon path not found!")

