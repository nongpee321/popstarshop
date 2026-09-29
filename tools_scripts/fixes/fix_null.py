import os

path = r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# First, fix the corrupted bytes
import re
content = re.sub(r"OPEN_DRAWER_1 = ESC \+ b'p.*?fa'", "OPEN_DRAWER_1 = ESC + b'p\\\\x00\\\\x19\\\\xfa'", content)
content = re.sub(r"OPEN_DRAWER_2 = ESC \+ b'p.*?fa'", "OPEN_DRAWER_2 = ESC + b'p\\\\x01\\\\x19\\\\xfa'", content)

# But wait, the file has actual null bytes now, so reading it as utf-8 might have preserved them.
# Let's just do a string replacement of the literal null bytes.
