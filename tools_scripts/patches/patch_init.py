import os
path = r'D:\pop-erp-food\python-pos\ui\payment_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if "self.received_str = \"\"" in line:
        lines.insert(i+1, "        self.set_method(self.selected_method)\n")
        break

with open(path, 'w', encoding='utf-8') as f:
    f.writelines(lines)
