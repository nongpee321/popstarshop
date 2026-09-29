import os
import re

path = r'D:\pop-erp-food\python-pos\ui\payment_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace UI layout creation for tax form
ui_target = r'''        # Tax Invoice Option
        self\.tax_checkbox = QCheckBox\("ขอใบกำกับภาษีเต็มรูปแบบ \(Full Tax Invoice\)"\)
        self\.tax_checkbox\.setStyleSheet\("font-size: 16px; font-weight: bold; color: #374151;"\)
        self\.tax_checkbox\.stateChanged\.connect\(self\.toggle_tax_form\)
        right_layout\.addWidget\(self\.tax_checkbox\)
        
        self\.tax_form = QWidget\(\)
        form_l = QFormLayout\(self\.tax_form\)
        self\.inp_tax_name = QLineEdit\(\)
        self\.inp_tax_id = QLineEdit\(\)
        self\.inp_tax_address = QLineEdit\(\)
        style = "padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;"
        self\.inp_tax_name\.setStyleSheet\(style\)
        self\.inp_tax_id\.setStyleSheet\(style\)
        self\.inp_tax_address\.setStyleSheet\(style\)
        form_l\.addRow\("ชื่อผู้เสียภาษี/บริษัท:", self\.inp_tax_name\)
        form_l\.addRow\("เลขประจำตัวผู้เสียภาษี:", self\.inp_tax_id\)
        form_l\.addRow\("ที่อยู่:", self\.inp_tax_address\)
        self\.tax_form\.hide\(\)
        right_layout\.addWidget\(self\.tax_form\)'''

ui_replace = '''        # Show QR Split Option
        self.show_split_qr_checkbox = QCheckBox("แสดง QR Code ยอดโอน (หน้าจอลูกค้า)")
        self.show_split_qr_checkbox.setStyleSheet("font-size: 18px; font-weight: bold; color: #E21B22; padding: 10px;")
        self.show_split_qr_checkbox.stateChanged.connect(self.toggle_split_qr)
        self.show_split_qr_checkbox.hide() # Hidden by default, shown only in split mode
        right_layout.addWidget(self.show_split_qr_checkbox)'''

content = re.sub(ui_target, ui_replace, content)

# Replace toggle_tax_form
method_target = r'''    def toggle_tax_form\(self, state\):
        self\.is_full_tax = state == Qt\.Checked
        if self\.is_full_tax:
            self\.tax_form\.show\(\)
            self\.setFixedSize\(850, 680\)
        else:
            self\.tax_form\.hide\(\)
            self\.setFixedSize\(850, 600\)'''

method_replace = '''    def toggle_split_qr(self, state):
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
            self.parent().customer_display.show_payment("เงินสด")'''

content = re.sub(method_target, method_replace, content)

# Replace tax validation in accept_payment
val_target = r'''        if self\.is_full_tax:
            self\.customer_name = self\.inp_tax_name\.text\(\)\.strip\(\)
            self\.customer_tax_id = self\.inp_tax_id\.text\(\)\.strip\(\)
            self\.customer_address = self\.inp_tax_address\.text\(\)\.strip\(\)
            if not self\.customer_name or not self\.customer_tax_id:
                QMessageBox\.warning\(self, "แจ้งเตือน", "กรุณากรอกชื่อและเลขประจำตัวผู้เสียภาษีให้ครบถ้วน"\)
                return'''

val_replace = '''        # Removed Tax Validation'''

content = re.sub(val_target, val_replace, content)

# Show/Hide checkbox based on method
set_method_target = r'''        if method == "เงินสด":
            self\.stack\.setCurrentIndex\(0\)
            self\.inp_cash\.setFocus\(\)
        elif method == "QR":
            self\.stack\.setCurrentIndex\(1\)
        elif method == "ผสม \(Split\)":
            self\.stack\.setCurrentIndex\(2\)
            self\.inp_split_cash\.setFocus\(\)'''

set_method_replace = '''        if method == "เงินสด":
            self.stack.setCurrentIndex(0)
            self.inp_cash.setFocus()
            self.show_split_qr_checkbox.hide()
            self.show_split_qr_checkbox.setChecked(False)
        elif method == "QR":
            self.stack.setCurrentIndex(1)
            self.show_split_qr_checkbox.hide()
            self.show_split_qr_checkbox.setChecked(False)
        elif method == "ผสม (Split)":
            self.stack.setCurrentIndex(2)
            self.inp_split_cash.setFocus()
            self.show_split_qr_checkbox.show()'''

content = re.sub(set_method_target, set_method_replace, content)

# Update split calc to trigger QR update
calc_target = r'''            self\.split_change_lbl\.setText\(f"เงินทอน: ฿ \{self\.change_amount:,\.2f\}"\)
        except:
            pass'''

calc_replace = '''            self.split_change_lbl.setText(f"เงินทอน: ฿ {self.change_amount:,.2f}")
            if hasattr(self, 'show_split_qr_checkbox') and self.show_split_qr_checkbox.isChecked():
                self.toggle_split_qr(Qt.Checked)
        except:
            pass'''

content = re.sub(calc_target, calc_replace, content)


with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("payment_dialog updated!")
