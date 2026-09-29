import os

path = r'D:\pop-erp-food\python-pos\ui\login_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace("self.selected_cashier = data.get('cashier', {})", "self.selected_cashier = data.get('cashier', {})\n                self.entered_pin = pin")
content = content.replace("self.selected_cashier = {'code': code, 'name':", "self.entered_pin = pin\n                self.selected_cashier = {'code': code, 'name':")

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated login_dialog.py to save entered_pin")

path2 = r'D:\pop-erp-food\python-pos\ui\main_window.py'
with open(path2, 'r', encoding='utf-8') as f:
    content2 = f.read()

content2 = content2.replace("self.config['cashier_name'] = name", "self.config['cashier_name'] = name\n                self.config['cashier_pin'] = getattr(dialog, 'entered_pin', '')")

with open(path2, 'w', encoding='utf-8') as f:
    f.write(content2)
print("Updated main_window.py to save cashier_pin")
