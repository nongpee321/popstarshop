with open(r'D:\pop-erp-food\python-pos\ui\payment_dialog.py', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i in range(220, 230):
    print(f"{i+1:03d}: {repr(lines[i])}")
