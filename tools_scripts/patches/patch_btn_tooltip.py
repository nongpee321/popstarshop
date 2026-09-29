with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if 'self.fs_btn = QPushButton' in line:
        lines.insert(i+1, "        self.fs_btn.setToolTip('ย่อจอ / เต็มจอ')\n")
    elif 'self.cashier_btn = QPushButton' in line:
        lines.insert(i+1, "        self.cashier_btn.setToolTip('แคชเชียร์')\n")
    elif 'self.shift_btn = QPushButton' in line:
        lines.insert(i+1, "        self.shift_btn.setToolTip('จัดการกะ (Shift)')\n")
    elif 'self.logout_btn = QPushButton' in line:
        lines.insert(i+1, "        self.logout_btn.setToolTip('ออกจากระบบ')\n")
    elif 'self.cfd_btn = QPushButton' in line:
        lines.insert(i+1, "        self.cfd_btn.setToolTip('จอฝั่งลูกค้า')\n")
    elif 'cancel_bill_btn = QPushButton' in line:
        lines.insert(i+1, "        cancel_bill_btn.setToolTip('ยกเลิกบิลปัจจุบัน')\n")
    elif 'pay_btn = QPushButton' in line:
        lines.insert(i+1, "        pay_btn.setToolTip('ชำระเงิน')\n")

with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'w', encoding='utf-8') as f:
    f.writelines(lines)
