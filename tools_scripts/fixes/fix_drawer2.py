with open(r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py', 'r', encoding='utf-8') as f:
    lines = f.readlines()

in_drawer = False
for i, line in enumerate(lines):
    if 'def open_cash_drawer' in line:
        in_drawer = True
    if in_drawer and 'cmds += INIT' in line:
        lines.insert(i+1, "    cmds += OPEN_DRAWER_1\n    cmds += OPEN_DRAWER_2\n")
        print("Fixed open_cash_drawer")
        break

with open(r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py', 'w', encoding='utf-8') as f:
    f.writelines(lines)
