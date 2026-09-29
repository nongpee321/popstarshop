with open(r'D:\pop-erp-food\python-pos\ui\payment_dialog.py', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i in range(180, 205):
    if lines[i].startswith('        else:'):
        lines[i]   = "            else:\n"
        lines[i+1] = "                btn.setStyleSheet(\"background-color: white; color: #111827; font-size: 24px; font-weight: bold; border-radius: 8px; border: 1px solid #e5e7eb;\")\n"
        lines[i+2] = "                btn.clicked.connect(lambda checked, k=key: self.append_input(k))\n"
        lines[i+3] = "            keypad_layout.addWidget(btn, r, c)\n"
        break

with open(r'D:\pop-erp-food\python-pos\ui\payment_dialog.py', 'w', encoding='utf-8') as f:
    f.writelines(lines)
print("Fixed keypad indentation")
