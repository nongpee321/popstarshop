import os
import re

path = r'D:\pop-erp-food\python-pos\ui\payment_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target_import = "from PySide6.QtCore import Qt"
new_import = "from PySide6.QtCore import Qt\nfrom PySide6.QtGui import QPixmap, QImage\nimport qrcode\nfrom ui.customer_display import generate_promptpay"

if target_import in content and "import qrcode" not in content:
    content = content.replace(target_import, new_import)

# Find right_layout.addWidget(self.keypad_widget) and add self.qr_label after it
target_add = "right_layout.addWidget(self.keypad_widget)"
new_add = """right_layout.addWidget(self.keypad_widget)
        
        self.qr_label = QLabel()
        self.qr_label.setAlignment(Qt.AlignCenter)
        self.qr_label.hide()
        right_layout.addWidget(self.qr_label)"""

if target_add in content and "self.qr_label = QLabel()" not in content:
    content = content.replace(target_add, new_add)

# Find set_method
target_method_qr = """        if method == "QR":
            self.keypad_widget.hide()
            self.input_field.hide()
            self.split_frame.hide()
            self.received_str = f"{self.total_amount:.2f}"
            self.update_displays()"""

new_method_qr = """        if method == "QR":
            self.keypad_widget.hide()
            self.input_field.hide()
            self.split_frame.hide()
            
            # Show QR
            self.qr_label.show()
            try:
                parent_window = self.parent()
                promptpay_id = parent_window.config.get('promptpay_id', '0999999999') if parent_window else '0999999999'
                payload = generate_promptpay(promptpay_id, self.total_amount)
                qr_img = qrcode.make(payload).convert("RGBA")
                qim = QImage(qr_img.tobytes("raw", "RGBA"), qr_img.size[0], qr_img.size[1], QImage.Format_RGBA8888)
                pix = QPixmap.fromImage(qim).scaled(300, 300, Qt.KeepAspectRatio, Qt.SmoothTransformation)
                self.qr_label.setPixmap(pix)
            except Exception as e:
                print("QR Error:", e)
                
            self.received_str = f"{self.total_amount:.2f}"
            self.update_displays()"""

if target_method_qr in content:
    content = content.replace(target_method_qr, new_method_qr)

target_method_else = """        elif method == "โอนเงิน (Split)":"""
# Just replace other cases to hide qr_label
target_method_split = """        elif method == "โอนเงิน/QR (Split)":""" # wait, the thai string was "แบ่งจ่าย (Split)"
# I'll just use regex to insert self.qr_label.hide() at the top of set_method before if/elif
target_set_method = """    def set_method(self, method):
        for m, btn in self.method_btns.items():
            btn.setChecked(m == method)
        self.selected_method = method
        self.title_lbl.setText(f"รับชำระเงิน: {method}")"""
# the title lbl has thai text, let's just use:
target_set_method = """    def set_method(self, method):
        for m, btn in self.method_btns.items():
            btn.setChecked(m == method)
        self.selected_method = method"""

if target_set_method in content and "self.qr_label.hide()" not in content.split("def set_method")[1]:
    content = content.replace(target_set_method, target_set_method + "\n        if hasattr(self, 'qr_label'):\n            self.qr_label.hide()")

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Patched payment_dialog.py for QR code scanner!")
