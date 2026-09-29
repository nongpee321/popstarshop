with open(r'D:\pop-erp-food\python-pos\ui\payment_dialog.py', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if "self.clear_cart_requested = True" in line and "else:" in lines[i-1]:
        # Fix else:
        lines[i-1] = "            else:\n"
        lines[i]   = "                self.clear_cart_requested = True\n"
        lines[i+1] = "                self.reject()\n"

with open(r'D:\pop-erp-food\python-pos\ui\payment_dialog.py', 'w', encoding='utf-8') as f:
    f.writelines(lines)
