path = r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if 'def open_cash_drawer' in line:
        for j in range(i, i+15):
            print(repr(lines[j]))
        break
