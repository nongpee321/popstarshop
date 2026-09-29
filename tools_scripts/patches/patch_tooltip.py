import re

path = r'D:\pop-erp-food\python-pos\ui\main_window.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = r'(            prod_btn = QPushButton\(f"\{p\.name_th\}\\n\\n.*?\{p\.default_price:\.2f\}"\)\n            prod_btn\.setFixedSize\(130, 130\))'
replace = r'\1\n            prod_btn.setToolTip(p.name_th)'

content = re.sub(target, replace, content)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
