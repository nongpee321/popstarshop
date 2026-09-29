with open(r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if "QMessageBox.critical(self, \"Error\", f\"ไม่สามารถเปิดลิ้นชักได้:" in line:
        if not line.rstrip().endswith('")'):
            # Join with the next line
            lines[i] = line.rstrip() + '\\n' + lines[i+1].lstrip()
            lines[i+1] = ""

with open(r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py', 'w', encoding='utf-8') as f:
    f.writelines(lines)
