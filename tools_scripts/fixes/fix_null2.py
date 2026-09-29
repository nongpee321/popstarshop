with open(r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py', 'rb') as f:
    data = f.read()
    
# Replace the bad bytes
# The bad bytes are b"ESC + b'p\x00\x19\xfa'" where the literal null was written.
# Wait, I can just replace the whole open_cash_drawer function and the OPEN_DRAWER lines.

import re
data_str = data.decode('utf-8', errors='ignore')

# Fix OPEN_DRAWER_1 and 2
data_str = re.sub(r"OPEN_DRAWER_1 = ESC \+ b'p[\x00-\xff]*?'", "OPEN_DRAWER_1 = ESC + b'p\\\\x00\\\\x19\\\\xfa'", data_str)
data_str = re.sub(r"OPEN_DRAWER_2 = ESC \+ b'p[\x00-\xff]*?'", "OPEN_DRAWER_2 = ESC + b'p\\\\x01\\\\x19\\\\xfa'", data_str)

with open(r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py', 'w', encoding='utf-8') as f:
    f.write(data_str)

print('done')
