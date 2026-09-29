with open(r'D:\pop-erp-food\python-pos\ui\payment_dialog.py', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i in range(150, 160):
    if lines[i].startswith('        else:'):
        # We need to unindent lines[i+1] and lines[i+2] by 4 spaces
        lines[i+1] = lines[i+1].replace('                btn.', '            btn.')
        lines[i+2] = lines[i+2].replace('                btn.', '            btn.')

with open(r'D:\pop-erp-food\python-pos\ui\payment_dialog.py', 'w', encoding='utf-8') as f:
    f.writelines(lines)
