import os
import re

path = r'D:\pop-erp-food\python-pos\ui\customer_display.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Add override_amount to show_payment
target = """    def show_payment(self, method="QR"):
        if method == "QR":
            try:
                pixmap = self.generate_qr_image(self.current_total)"""

replacement = """    def show_payment(self, method="QR", override_amount=None):
        if method == "QR":
            try:
                amt = override_amount if override_amount is not None else self.current_total
                pixmap = self.generate_qr_image(amt)"""

content = content.replace(target, replacement)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("customer_display updated!")
