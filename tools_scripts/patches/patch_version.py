import os

path = r'D:\pop-erp-food\python-pos\ui\main_window.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = 'version_label = QLabel("v1.10.56")'
new_target = 'version_label = QLabel("v1.10.57")'

if target in content:
    content = content.replace(target, new_target)
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    print("Version updated in main_window.py!")
else:
    print("Target not found! Please check the file.")
