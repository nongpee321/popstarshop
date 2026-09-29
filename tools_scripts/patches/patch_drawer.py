import re

path = r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Remove drawer button from top_btns
target_drawer = r'        # Drawer Button\n.*?drawer_btn = QPushButton.*?\n.*?drawer_btn\.setStyleSheet.*?\n.*?drawer_btn\.clicked\.connect.*?\n'
content = re.sub(target_drawer, '', content, flags=re.DOTALL)
content = re.sub(r'\s*top_btns\.addWidget\(drawer_btn\)', '', content)

# 2. Remove OPEN_DRAWER from print_escpos_receipt
target_cmds = r'\s*cmds \+= OPEN_DRAWER_1\n\s*cmds \+= OPEN_DRAWER_2'
content = re.sub(target_cmds, '', content)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
