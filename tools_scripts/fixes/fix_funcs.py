import re
with open(r'D:\pop-erp-food\python-pos\ui\payment_dialog.py', 'r', encoding='utf-8') as f:
    text = f.read()

target = r"    def update_displays\(self\):.*?        self\.accept\(\)"
replace = '''    def update_displays(self):
        try:
            self.received_amount = float(self.received_str or 0)
        except ValueError:
            self.received_amount = 0.0
            
        self.input_field.setText(f"{self.received_amount:,.2f}")
        
        if self.received_amount >= self.total_amount:
            self.change_amount = self.received_amount - self.total_amount
            self.change_lbl.setStyleSheet("font-size: 20px; font-weight: bold; color: #10b981;")
        else:
            self.change_amount = 0.0
            self.change_lbl.setStyleSheet("font-size: 20px; font-weight: bold; color: #ef4444;")
            
        self.change_lbl.setText(f"เงินทอน: ฿ {self.change_amount:,.2f}")
        
        self.cash_amount = self.received_amount if self.selected_method == "เงินสด" else 0.0
        self.transfer_amount = self.total_amount if self.selected_method == "QR" else 0.0

    def accept_payment(self):
        if self.received_amount < self.total_amount:
            from PySide6.QtWidgets import QMessageBox
            QMessageBox.warning(self, "แจ้งเตือน", "ยอดเงินรับยังไม่ครบ!")
            return
            
        self.accept()'''

text = re.sub(target, replace, text, flags=re.DOTALL)

with open(r'D:\pop-erp-food\python-pos\ui\payment_dialog.py', 'w', encoding='utf-8') as f:
    f.write(text)
