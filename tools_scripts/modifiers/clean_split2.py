with open(r'D:\pop-erp-food\python-pos\ui\payment_dialog.py', 'r', encoding='utf-8') as f:
    lines = f.readlines()

out = []
skip = 0
for line in lines:
    if "For split, adjust cash_amount" in line:
        skip = 2
        continue
    if skip > 0:
        skip -= 1
        continue
    out.append(line)

with open(r'D:\pop-erp-food\python-pos\ui\payment_dialog.py', 'w', encoding='utf-8') as f:
    f.writelines(out)
