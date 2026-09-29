import re

path = r'D:\pop-erp-food\python-pos\ui\main_window.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target_menu = r'(        action_sync = main_menu\.addAction\(".*?\(Sync Data\)"\)\n        action_sync\.triggered\.connect\(self\.manual_sync\)\n        \n        main_menu\.addSeparator\(\))'
replace_menu = r'\1\n        action_drawer = main_menu.addAction("เปิดลิ้นชัก (Open Drawer)")\n        action_drawer.triggered.connect(self.manual_open_drawer)\n        \n        main_menu.addSeparator()'
content = re.sub(target_menu, replace_menu, content)

# Now add manual_open_drawer method to MainWindow
target_method = r'(    def open_settings\(self\):)'
replace_method = r'''    def manual_open_drawer(self):
        printer_name = self.config.get('receipt_printer', '')
        if not printer_name:
            from PySide6.QtWidgets import QMessageBox
            QMessageBox.warning(self, "แจ้งเตือน", "ยังไม่ได้ตั้งค่าเครื่องพิมพ์ในหน้าตั้งค่า")
            return
            
        expected_pin = self.config.get('cashier_pin', '')
        if not expected_pin:
            from PySide6.QtWidgets import QMessageBox
            QMessageBox.warning(self, "แจ้งเตือน", "กรุณาออกจากระบบและเข้าสู่ระบบใหม่อีกครั้งเพื่ออัปเดตรหัสผ่านลิ้นชัก")
            return
            
        from PySide6.QtWidgets import QInputDialog, QLineEdit, QMessageBox
        pwd, ok = QInputDialog.getText(self, "ยืนยันตัวตน", "กรุณาใส่รหัสผ่าน Cashier:", QLineEdit.Password)
        if not ok:
            return
        if pwd != expected_pin:
            QMessageBox.warning(self, "ข้อผิดพลาด", "รหัสผ่านไม่ถูกต้อง!")
            return
            
        try:
            from ui.receipt_dialog import open_cash_drawer
            open_cash_drawer(printer_name)
        except Exception as e:
            QMessageBox.critical(self, "Error", f"ไม่สามารถเปิดลิ้นชักได้:\n{str(e)}")

\1'''
content = re.sub(target_method, replace_method, content)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
