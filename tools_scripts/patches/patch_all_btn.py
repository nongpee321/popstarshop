with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if 'all_btn = QPushButton("ทั้งหมด")' in line:
        lines.insert(i+1, '        all_btn.setToolTip("ทั้งหมด")\n')
        break

with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'w', encoding='utf-8') as f:
    f.writelines(lines)
