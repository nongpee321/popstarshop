import os
import re

path = r'D:\pop-erp-food\python-pos\ui\receipt_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = '''        top_btns.addWidget(print_btn)
        top_btns.addWidget(settings_btn)'''

replace = '''        # Drawer Button
        drawer_btn = QPushButton("🗄️ เปิดลิ้นชัก")
        drawer_btn.setStyleSheet("QPushButton { background-color: #f59e0b; color: white; border: none; border-radius: 8px; padding: 10px; font-weight: bold; font-size: 14px; } QPushButton:hover { background-color: #d97706; }")
        drawer_btn.clicked.connect(self.manual_open_drawer)

        top_btns.addWidget(print_btn)
        top_btns.addWidget(drawer_btn)
        top_btns.addWidget(settings_btn)'''

content = content.replace(target, replace)

method_target = '''    def print_receipt(self):'''
method_replace = '''    def manual_open_drawer(self):
        parent_window = self.parent()
        config = getattr(parent_window, 'config', {}) if parent_window else {}
        printer_name = config.get('receipt_printer', '')
        if not printer_name:
            QMessageBox.warning(self, "แจ้งเตือน", "ยังไม่ได้ตั้งค่าเครื่องพิมพ์ในหน้าตั้งค่า")
            return
        try:
            open_cash_drawer(printer_name)
        except Exception as e:
            QMessageBox.critical(self, "Error", f"ไม่สามารถเปิดลิ้นชักได้:\n{str(e)}")

    def print_receipt(self):'''

content = content.replace(method_target, method_replace)

init_target = '''        self.update_receipt_ui(config)'''
init_replace = '''        self.update_receipt_ui(config)
        
        # Auto-open drawer if cash or split
        pm = self.receipt_data.get('payment_method', '')
        if pm in ['cash', 'split', 'เงินสด', 'ผสม (Split)']:
            printer_name = config.get('receipt_printer', '')
            if printer_name:
                try:
                    open_cash_drawer(printer_name)
                except:
                    pass
'''
content = content.replace(init_target, init_replace)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated UI with drawer button and auto-open.")
