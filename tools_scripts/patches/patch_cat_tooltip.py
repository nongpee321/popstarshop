import re

path = r'D:\pop-erp-food\python-pos\ui\main_window.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = r'(            btn = QPushButton\(cat\.name_th\)\n            btn\.setFixedSize\(100, 40\))'
replace = r'\1\n            btn.setToolTip(cat.name_th)'

content = re.sub(target, replace, content)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
