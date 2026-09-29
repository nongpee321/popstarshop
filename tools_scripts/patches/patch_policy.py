import re

path = r'D:\pop-erp-food\python-pos\ui\main_window.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Add size policy Minimum to prevent squishing text on these buttons
target = r'(        self\.cashier_btn\.setToolTip\(''.*?''\))'
replace = r'\1\n        from PySide6.QtWidgets import QSizePolicy\n        self.cashier_btn.setSizePolicy(QSizePolicy.Minimum, QSizePolicy.Fixed)'
content = re.sub(target, replace, content)

target2 = r'(        self\.shift_btn\.setToolTip\(''.*?''\))'
replace2 = r'\1\n        self.shift_btn.setSizePolicy(QSizePolicy.Minimum, QSizePolicy.Fixed)'
content = re.sub(target2, replace2, content)

target3 = r'(        self\.cfd_btn\.setToolTip\(''.*?''\))'
replace3 = r'\1\n        self.cfd_btn.setSizePolicy(QSizePolicy.Minimum, QSizePolicy.Fixed)'
content = re.sub(target3, replace3, content)

target4 = r'(        menu_btn = QPushButton\(".*?เมนู"\)\n        menu_btn\.setStyleSheet\(.*?\n.*?\n.*?\n.*?\))'
replace4 = r'\1\n        menu_btn.setSizePolicy(QSizePolicy.Minimum, QSizePolicy.Fixed)'
content = re.sub(target4, replace4, content)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
