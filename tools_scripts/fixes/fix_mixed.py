import sys

path_main = r'D:\pop-erp-food\python-pos\ui\main_window.py'
with open(path_main, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace("'ผสม (Split)': 'split'", "'ผสม (Split)': 'mixed'")
# also inside _sync_receipts I used 'split', wait no, in sync_worker.py I did if receipt.payment_method == 'split'
with open(path_main, 'w', encoding='utf-8') as f:
    f.write(content)

path_sync = r'D:\pop-erp-food\python-pos\sync_worker.py'
with open(path_sync, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace("'split'", "'mixed'")

with open(path_sync, 'w', encoding='utf-8') as f:
    f.write(content)
    
path_receipt = r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py'
with open(path_receipt, 'r', encoding='utf-8') as f:
    content = f.read()
content = content.replace("== 'split'", "== 'mixed'")
content = content.replace("pm == 'split'", "pm == 'mixed'")

with open(path_receipt, 'w', encoding='utf-8') as f:
    f.write(content)

print("Changed split to mixed")
