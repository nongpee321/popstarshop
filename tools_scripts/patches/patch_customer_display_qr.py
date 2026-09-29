import os

path = r'D:\pop-erp-food\python-pos\ui\customer_display.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Font reductions
content = content.replace("font-size: 28px;", "font-size: 24px;")
content = content.replace("font-size: 80px;", "font-size: 64px;")
content = content.replace("font-size: 32px;", "font-size: 24px;")

# Reduce spacing
content = content.replace("right_panel.addSpacing(30)", "right_panel.addSpacing(10)")

# Reduce QR Image size
content = content.replace("self.qr_image.setFixedSize(380, 380)", "self.qr_image.setFixedSize(280, 280)")

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("QR Layout adjusted!")
