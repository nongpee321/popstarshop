import re

path = r'D:\pop-erp-food\python-pos\ui\payment_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = r'(        if method == "QR":\n            self\.keypad_widget\.hide\(\)\n            self\.input_field\.hide\(\))'
replace = r'\1\n            self.change_lbl.hide()'
content = re.sub(target, replace, content, flags=re.DOTALL)

target2 = r'(        else:\n            self\.keypad_widget\.show\(\)\n            self\.input_field\.show\(\))'
replace2 = r'\1\n            self.change_lbl.show()'
content = re.sub(target2, replace2, content, flags=re.DOTALL)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated change_lbl visibility")
