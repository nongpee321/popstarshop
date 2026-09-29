with open(r'D:\pop-erp-food\python-pos\ui\payment_dialog.py', 'r', encoding='utf-8') as f:
    lines = f.readlines()

new_lines = []
for line in lines:
    if "self.cash_amount -= self.change_amount" in line:
        continue
    if "# Edge case: change is more than cash given" in line:
        continue
    if "self.cash_amount = 0" in line:
        continue
    # Let's just fix unindent manually in update_displays
    if line.startswith("            try:") or line.startswith("            self.received_amount =") or line.startswith("            except ValueError:") or line.startswith("            self.input_field.setText") or line.startswith("            if self.received_amount >=") or line.startswith("            self.change_amount =") or line.startswith("            self.change_lbl.") or line.startswith("            else:"):
        # it has 12 spaces, replace with 8 spaces
        line = line[4:]
    new_lines.append(line)

with open(r'D:\pop-erp-food\python-pos\ui\payment_dialog.py', 'w', encoding='utf-8') as f:
    f.writelines(new_lines)
