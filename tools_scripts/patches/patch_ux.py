import os
import re

path = r'D:\pop-erp-food\python-pos\ui\payment_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = '''    def toggle_split_qr(self, state):
        # state is 2 for checked, 0 for unchecked
        is_checked = (state == 2) or self.show_split_qr_checkbox.isChecked()
        if is_checked and self.selected_method == "ผสม (Split)":
            try:
                # Strip commas in case user typed them
                text_val = self.inp_split_transfer.text().replace(',', '')
                amt = float(text_val) if text_val else 0.0
                if amt > 0:
                    parent_window = self.parent()
                    # Update Customer Display
                    if parent_window and getattr(parent_window, "customer_display", None) and not parent_window.customer_display.isHidden():
                        parent_window.customer_display.show_payment("QR", override_amount=amt)
                        
                    # Update Cashier Display
                    promptpay_id = parent_window.config.get('promptpay_id', '0999999999') if parent_window else '0999999999'
                    from ui.customer_display import generate_promptpay
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
                    parent_window = self.parent()
                    if parent_window and getattr(parent_window, "customer_display", None) and not parent_window.customer_display.isHidden():
                        parent_window.customer_display.show_payment("เงินสด")
                    self.qr_label.hide()
            except Exception as e:
                import traceback
                traceback.print_exc()
                # Optional: show error to cashier so they know why it failed
                # from PySide6.QtWidgets import QMessageBox
                # QMessageBox.warning(self, "QR Error", str(e))
        else:
            parent_window = self.parent()
            if parent_window and getattr(parent_window, "customer_display", None) and not parent_window.customer_display.isHidden():
                parent_window.customer_display.show_payment("เงินสด")
            self.qr_label.hide()'''

replace = '''    def toggle_split_qr(self, state):
        # state is 2 for checked, 0 for unchecked
        is_checked = (state == 2) or self.show_split_qr_checkbox.isChecked()
        if is_checked and self.selected_method == "ผสม (Split)":
            try:
                # Strip commas in case user typed them
                text_val = self.inp_split_transfer.text().replace(',', '')
                amt = float(text_val) if text_val else 0.0
                
                # UX FIX: Auto-fill remaining amount if it's 0 when clicked
                if amt <= 0:
                    c_val = self.inp_split_cash.text().replace(',', '')
                    c_amt = float(c_val) if c_val else 0.0
                    remaining = max(0.0, self.total_amount - c_amt)
                    if remaining > 0:
                        amt = remaining
                        self.inp_split_transfer.blockSignals(True)
                        self.inp_split_transfer.setText(f"{amt:.2f}")
                        self.inp_split_transfer.blockSignals(False)
                        self.calculate_split()

                parent_window = self.parent()
                # Update Customer Display
                if parent_window and getattr(parent_window, "customer_display", None) and not parent_window.customer_display.isHidden():
                    parent_window.customer_display.show_payment("QR", override_amount=amt)
                    
                # Update Cashier Display
                promptpay_id = parent_window.config.get('promptpay_id', '0999999999') if parent_window else '0999999999'
                from ui.customer_display import generate_promptpay
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

            except Exception as e:
                import traceback
                traceback.print_exc()
        else:
            parent_window = self.parent()
            if parent_window and getattr(parent_window, "customer_display", None) and not parent_window.customer_display.isHidden():
                parent_window.customer_display.show_payment("เงินสด")
            self.qr_label.hide()'''

content = content.replace(target, replace)
with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated toggle_split_qr to auto-fill amount!")
