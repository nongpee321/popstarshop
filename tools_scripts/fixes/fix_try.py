with open(r'D:\pop-erp-food\python-pos\ui\payment_dialog.py', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i in range(225, 250):
    if lines[i].startswith('        try:'):
        lines[i] = lines[i].replace('        try:', '            try:')
    if lines[i].startswith('            self.received_str'):
        pass

with open(r'D:\pop-erp-food\python-pos\ui\payment_dialog.py', 'w', encoding='utf-8') as f:
    f.writelines(lines)
