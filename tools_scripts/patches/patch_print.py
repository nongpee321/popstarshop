import re

path = r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = r'(    CUT_PAPER     = GS  \+ b\'V\\x41\\x03\')'
replace = r'\1\n    OPEN_DRAWER_1 = ESC + b\'p\\x00\\x19\\xfa\'\n    OPEN_DRAWER_2 = ESC + b\'p\\x01\\x19\\xfa\''

content = re.sub(target, replace, content)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Fixed OPEN_DRAWER variables in print_escpos_receipt")
