import re

path = r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace("b\\'p\\x00\\x19\\xfa\\'", "b'p\\x00\\x19\\xfa'")
content = content.replace("b\\'p\\x01\\x19\\xfa\\'", "b'p\\x01\\x19\\xfa'")

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
