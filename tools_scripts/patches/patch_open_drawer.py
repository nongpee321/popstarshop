import re
path = r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = r'(    cmds = bytearray\(\)\n    cmds \+= INIT\n    \n    try:)'
replace = r'''    cmds = bytearray()
    cmds += INIT
    cmds += OPEN_DRAWER_1
    cmds += OPEN_DRAWER_2
    
    try:'''
content = re.sub(target, replace, content)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
