import os
import re

path = r'D:\pop-erp-food\python-pos\ui\payment_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = '''    def toggle_split_qr(self, state):
        if not hasattr(self.parent(), 'customer_display') or not self.parent().customer_display:
            return
            
        is_checked = self.show_split_qr_checkbox.isChecked()
        if is_checked and self.selected_method == "ผสม (Split)":
            try:
                amt = float(self.inp_split_transfer.text() or 0)
                if amt > 0:
                    self.parent().customer_display.show_payment("QR", override_amount=amt)
                else:
                    self.parent().customer_display.show_payment("เงินสด")
            except:
                pass
        else:
            self.parent().customer_display.show_payment("เงินสด")'''

replace = '''    def toggle_split_qr(self, state):
        is_checked = self.show_split_qr_checkbox.isChecked()
        if is_checked and self.selected_method == "ผสม (Split)":
            try:
                amt = float(self.inp_split_transfer.text() or 0)
                if amt > 0:
                    # Update Customer Display
                    if hasattr(self.parent(), 'customer_display') and self.parent().customer_display:
                        self.parent().customer_display.show_payment("QR", override_amount=amt)
                        
                    # Update Cashier Display
                    parent_window = self.parent()
                    promptpay_id = parent_window.config.get('promptpay_id', '0999999999') if parent_window else '0999999999'
                    from utils.qr_generator import generate_promptpay
                    import io, qrcode
                    from PySide6.QtGui import QPixmap
                    from PySide6.QtCore import Qt
                    payload = generate_promptpay(promptpay_id, amt)
                    qr_img = qrcode.make(payload)
                    buf = io.BytesIO()
                    qr_img.save(buf, format="PNG")
                    pix = QPixmap()
                    pix.loadFromData(buf.getvalue())
                    pix = pix.scaled(300, 300, Qt.KeepAspectRatio, Qt.SmoothTransformation)
                    self.qr_label.setPixmap(pix)
                    self.qr_label.show()
                else:
                    if hasattr(self.parent(), 'customer_display') and self.parent().customer_display:
                        self.parent().customer_display.show_payment("เงินสด")
                    self.qr_label.hide()
            except Exception as e:
                print(e)
        else:
            if hasattr(self.parent(), 'customer_display') and self.parent().customer_display:
                self.parent().customer_display.show_payment("เงินสด")
            self.qr_label.hide()'''

content = content.replace(target, replace)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated toggle_split_qr to show QR on both screens!")
