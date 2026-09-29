with open(r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if "expected_pin = config.get('cashier_pin', '')" in line:
        lines[i+1] = "        if not expected_pin:\n"
        lines[i+2] = "            QMessageBox.warning(self, 'Alert', 'กรุณา Log out แล้ว Log in ใหม่ 1 ครั้ง เพื่อเริ่มใช้งานระบบรหัสผ่านลิ้นชัก')\n"
        lines[i+3] = "            return\n"
        lines[i+4] = "        from PySide6.QtWidgets import QInputDialog, QLineEdit\n"
        lines[i+5] = "        pwd, ok = QInputDialog.getText(self, 'ยืนยันตัวตน', 'กรุณาใส่รหัสผ่าน Cashier:', QLineEdit.Password)\n"
        lines.insert(i+6, "        if not ok:\n            return\n        if pwd != expected_pin:\n            QMessageBox.warning(self, 'Error', 'รหัสผ่านแคชเชียร์ไม่ถูกต้อง!')\n            return\n")
        break

with open(r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py', 'w', encoding='utf-8') as f:
    f.writelines(lines)
