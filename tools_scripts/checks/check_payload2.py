with open(r'D:\pop-erp-food\python-pos\sync_worker.py', 'r', encoding='utf-8') as f:
    lines = f.readlines()

start = -1
for i, l in enumerate(lines):
    if 'def _sync_receipts' in l:
        start = i
        break

if start != -1:
    end = start + 80
    for l in lines[start:end]:
        print(l.rstrip().encode('unicode_escape').decode('utf-8'))
