with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if 'name_item = QTableWidgetItem(name_str)' in line:
        lines.insert(i+1, "            name_item.setToolTip(name_str)\n")
        break

with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'w', encoding='utf-8') as f:
    f.writelines(lines)
