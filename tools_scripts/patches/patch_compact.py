import re

path = r'D:\pop-erp-food\python-pos\ui\main_window.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Compact Network Status
content = content.replace('"🟢 Online (Syncing...)"', '"🟢 Syncing"')
content = content.replace('"🟢 Online (Synced)"', '"🟢 Online"')
content = content.replace('"🟡 Offline ON"', '"🟡 Offline"')
content = content.replace('"🟡 Offline"', '"🟡 Offline"')

# Compact Shift Button
content = content.replace('self.shift_btn = QPushButton("💰 ยังไม่เปิดกะ")', 'self.shift_btn = QPushButton("💰 เปิดกะ")')
content = content.replace('self.shift_btn.setText("💰 ยังไม่เปิดกะ")', 'self.shift_btn.setText("💰 เปิดกะ")')

# Compact Cashier Button
content = content.replace('self.cashier_btn = QPushButton("👤  ยังไม่เข้าสู่ระบบ")', 'self.cashier_btn = QPushButton("👤 เข้าสู่ระบบ")')
content = content.replace('self.cashier_btn.setText("👤  ยังไม่เข้าสู่ระบบ")', 'self.cashier_btn.setText("👤 เข้าสู่ระบบ")')

# Truncate Cashier Name
content = re.sub(
    r'self\.cashier_btn\.setText\(f"👤    \{name\}"\)',
    r'self.cashier_btn.setText(f"👤 {name[:12]}")',
    content
)
# Reduce padding
content = content.replace('padding: 5px 15px;', 'padding: 5px 10px;')

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
