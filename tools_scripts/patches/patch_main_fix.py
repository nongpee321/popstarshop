with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if '{str(e)}")' in line and not line.strip().startswith('QMessageBox'):
        # It's broken across lines
        lines[i-1] = lines[i-1].rstrip('\n') + '\\n' + lines[i].lstrip()
        lines[i] = ""

with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'w', encoding='utf-8') as f:
    f.writelines(lines)
