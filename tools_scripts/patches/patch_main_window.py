import os

path = r'D:\pop-erp-food\python-pos\ui\main_window.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = """            dialog = PaymentDialog(self, total_amount, total_items, total_qty)
            if self.customer_display and not self.customer_display.isHidden():
                self.customer_display.show_payment("QR") # Default show QR
            if dialog.exec() == QDialog.Accepted:"""

replacement = """            dialog = PaymentDialog(self, total_amount, total_items, total_qty)
            if dialog.exec() == QDialog.Accepted:"""

if target in content:
    content = content.replace(target, replacement)
else:
    print("WARNING: target not found in main_window.py")

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
