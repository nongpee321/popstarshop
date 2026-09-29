path = r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if 'def open_cash_drawer' in line:
        for j in range(i, i+30):
            if 'ESC =' in lines[j]:
                print("FOUND:", repr(lines[j]))
        break
