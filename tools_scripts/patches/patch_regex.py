import os
import re

path = r'D:\pop-erp-food\python-pos\ui\payment_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace UI layout creation for tax form
content = re.sub(
    r'# Tax Invoice Option.*?right_layout\.addWidget\(self\.tax_form\)',
    '''# Show QR Split Option
        self.show_split_qr_checkbox = QCheckBox("แสดง QR Code ยอดโอน (หน้าจอลูกค้า)")
        self.show_split_qr_checkbox.setStyleSheet("font-size: 18px; font-weight: bold; color: #E21B22; padding: 10px;")
        self.show_split_qr_checkbox.stateChanged.connect(self.toggle_split_qr)
        self.show_split_qr_checkbox.hide()
        right_layout.addWidget(self.show_split_qr_checkbox)''',
    content, flags=re.DOTALL
)

content = re.sub(
    r'def toggle_tax_form.*?self\.setFixedSize\(850, 600\)',
    '''def toggle_split_qr(self, state):
        if not hasattr(self.parent(), 'customer_display') or not self.parent().customer_display:
            return
            
        if state == Qt.Checked and self.selected_method == "ผสม (Split)":
            try:
                amt = float(self.inp_split_transfer.text() or 0)
                if amt > 0:
                    self.parent().customer_display.show_payment("QR", override_amount=amt)
                else:
                    self.parent().customer_display.show_payment("เงินสด")
            except:
                pass
        else:
            self.parent().customer_display.show_payment("เงินสด")''',
    content, flags=re.DOTALL
)

content = re.sub(
    r'if self\.is_full_tax:.*?QMessageBox\.warning\(self, "[^"]+", "[^"]+"\)\s*return',
    '# Removed Tax Validation',
    content, flags=re.DOTALL
)

content = re.sub(
    r'if method == "เงินสด":.*?self\.inp_split_cash\.setFocus\(\)',
    '''if method == "เงินสด":
            self.stack.setCurrentIndex(0)
            self.inp_cash.setFocus()
            if hasattr(self, 'show_split_qr_checkbox'):
                self.show_split_qr_checkbox.hide()
                self.show_split_qr_checkbox.setChecked(False)
        elif method == "QR":
            self.stack.setCurrentIndex(1)
            if hasattr(self, 'show_split_qr_checkbox'):
                self.show_split_qr_checkbox.hide()
                self.show_split_qr_checkbox.setChecked(False)
        elif method == "ผสม (Split)":
            self.stack.setCurrentIndex(2)
            self.inp_split_cash.setFocus()
            if hasattr(self, 'show_split_qr_checkbox'):
                self.show_split_qr_checkbox.show()''',
    content, flags=re.DOTALL
)

content = re.sub(
    r'self\.split_change_lbl\.setText\(f"เงินทอน: ฿ \{self\.change_amount:,\.2f\}"\)\s*except:\s*pass',
    '''self.split_change_lbl.setText(f"เงินทอน: ฿ {self.change_amount:,.2f}")
            if hasattr(self, 'show_split_qr_checkbox') and self.show_split_qr_checkbox.isChecked():
                self.toggle_split_qr(Qt.Checked)
        except:
            pass''',
    content, flags=re.DOTALL
)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Regex replace completed!")
