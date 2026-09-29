import os
path = r'D:\pop-erp-food\python-pos\ui\main_window.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()
content = content.replace('v1.10.85', 'v1.10.86')
with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
