import os
import re

path = r'D:\pop-erp-food\python-pos\ui\payment_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Fix the method buttons list
content = re.sub(r'\[.*?\]', '["เงินสด", "QR"]', content, count=1)

# Clean update_displays
content = re.sub(r'\s*if self\.selected_method != ".*?\(Split\)":', '', content)
content = re.sub(r'\s*else:\n\s*pass', '', content)
content = re.sub(r'\s*# Auto update split values for normal methods just in case', '', content)

# Clean accept_payment
content = re.sub(r'\s*# For split, adjust cash_amount if there\'s change.*?self\.cash_amount = max\(0, self\.cash_amount - self\.change_amount\)', '', content, flags=re.DOTALL)
content = re.sub(r'\s*if self\.selected_method == ".*?\(Split\)" and self\.change_amount > 0:\n\s*self\.cash_amount = max\(0, self\.cash_amount - self\.change_amount\)', '', content, flags=re.DOTALL)


with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Cleaned up Split references")
