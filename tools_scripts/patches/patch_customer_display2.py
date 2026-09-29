import re

path = r'D:\pop-erp-food\python-pos\ui\customer_display.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# I want to ensure the table header is smaller
content = re.sub(r'setHorizontalHeaderLabels\(\[.*\]\)', 'setHorizontalHeaderLabels(["สินค้า", "จำนวน", "ราคา", "รวม"])', content)
content = re.sub(r'QLabel\("ยินดีต้อนรับ \(Welcome\)"\)', 'QLabel("ยินดีต้อนรับ (Welcome)")', content)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
