with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    lines = f.readlines()

start = -1
for i, l in enumerate(lines):
    if 'def process_payment' in l:
        start = i
        break

if start != -1:
    end = start + 100
    for l in lines[start:end]:
        print(l.rstrip().encode('unicode_escape').decode('utf-8'))
