import os

path = r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = '''    ESC = b'\x1b'
    GS  = b'\x1d'
    INIT          = ESC + b'@'
    ALIGN_LEFT    = ESC + b'a\x00'
'''

replace = '''    ESC = b'\x1b'
    GS  = b'\x1d'
    INIT          = ESC + b'@'
    OPEN_DRAWER_1 = ESC + b'p\x00\x19\xfa'
    OPEN_DRAWER_2 = ESC + b'p\x01\x19\xfa'
    ALIGN_LEFT    = ESC + b'a\x00'
'''
content = content.replace(target, replace)

target2 = '''    cmds = bytearray()
    cmds += INIT
'''

replace2 = '''    cmds = bytearray()
    cmds += INIT
    cmds += OPEN_DRAWER_1
    cmds += OPEN_DRAWER_2
'''
content = content.replace(target2, replace2)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated print_escpos_receipt with drawer kick.")
