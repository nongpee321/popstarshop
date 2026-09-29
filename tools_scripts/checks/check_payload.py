import re
with open(r'D:\pop-erp-food\python-pos\sync_worker.py', 'r', encoding='utf-8') as f:
    content = f.read()

match = re.search(r'payload = \{.*?\}', content, re.DOTALL)
if match:
    print(match.group(0))
