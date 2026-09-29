import re

with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    content = f.read()

# Let's find how receipt_data is populated before print
match = re.search(r'receipt_data = \{.*?\}', content, re.DOTALL)
if match:
    print(match.group(0))
