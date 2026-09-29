with open(r'D:\pop-erp-food\python-pos\ui\payment_dialog.py', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if line.strip() == "else:":
        # Let's check context around it
        prev = lines[i-1].strip() if i > 0 else ""
        next_l = lines[i+1].strip() if i < len(lines)-1 else ""
        if "change_lbl.setText" in prev and "if self.received_amount < self.total_amount:" in next_l:
            lines[i] = ""

with open(r'D:\pop-erp-food\python-pos\ui\payment_dialog.py', 'w', encoding='utf-8') as f:
    f.writelines(lines)
