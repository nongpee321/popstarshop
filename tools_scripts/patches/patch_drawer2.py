import re
path = r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = r'''        expected_pin = config.get\('cashier_pin', ''\)
        if expected_pin:
            from PySide6.QtWidgets import QInputDialog, QLineEdit
            pwd, ok = QInputDialog.getText\(self, "ยืนยันตัวตน", "กรุณาใส่รหัสผ่าน Cashier:", QLineEdit.Password\)
            if not ok:
                return
            if pwd != expected_pin:
                QMessageBox.warning\(self, "ข้อผิดพลาด", "รหัสผ่านไม่ถูกต้อง!"\)
                return'''

replace = '''        expected_pin = config.get('cashier_pin', '')
        if not expected_pin:
            QMessageBox.warning(self, "แจ้งเตือน", "กรุณาออกจากระบบและเข้าสู่ระบบใหม่อีกครั้งเพื่ออัปเดตรหัสผ่านลิ้นชัก")
            return
            
        from PySide6.QtWidgets import QInputDialog, QLineEdit
        pwd, ok = QInputDialog.getText(self, "ยืนยันตัวตน", "กรุณาใส่รหัสผ่าน Cashier:", QLineEdit.Password)
        if not ok:
            return
        if pwd != expected_pin:
            QMessageBox.warning(self, "ข้อผิดพลาด", "รหัสผ่านไม่ถูกต้อง!")
            return'''

content = re.sub(target, replace, content)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
