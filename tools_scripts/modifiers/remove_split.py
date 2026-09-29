import os
import re

path = r'D:\pop-erp-food\python-pos\ui\payment_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Remove from method buttons
content = re.sub(r'\["เงินสด", "QR", "ผสม \(Split\)"\]', '["เงินสด", "QR"]', content)
content = re.sub(r'\["1\?,؅,\',T,,\"", "QR", ",o,, \(Split\)"\]', '["เงินสด", "QR"]', content) # Just in case

# 2. Remove split frame UI
content = re.sub(r'        # Split Payment Inputs.*?left_layout\.addWidget\(self\.split_frame\)', '', content, flags=re.DOTALL)

# 3. Remove split_frame hiding in set_method
content = re.sub(r'\s*self\.split_frame\.hide\(\)', '', content)
content = re.sub(r'\s*self\.split_frame\.show\(\)', '', content)

# 4. Remove method == "ผสม (Split)" branch in set_method
content = re.sub(r'\s*elif method == "ผสม \(Split\)":.*?(?=        else:)', '', content, flags=re.DOTALL)
# Also handle encoded branch if needed
content = re.sub(r'\s*elif method == ".*? \(Split\)":.*?(?=        else:)', '', content, flags=re.DOTALL)

# 5. Remove calculate_split method
content = re.sub(r'    def calculate_split\(self\):.*?    def update_displays\(self\):', '    def update_displays(self):', content, flags=re.DOTALL)

# 6. Clean update_displays
update_displays_target = '''    def update_displays(self):
        if self.selected_method != "ผสม (Split)":
            try:
                self.received_amount = float(self.received_str or 0)
                if self.received_amount >= self.total_amount:
                    self.change_amount = self.received_amount - self.total_amount
                else:
                    self.change_amount = 0
                self.input_field.setText(f"{self.received_amount:,.2f}")
                self.change_lbl.setText(f"เงินทอน: ฿ {self.change_amount:,.2f}")
                
                # Auto update split values for normal methods just in case
                self.cash_amount = self.received_amount if self.selected_method == "เงินสด" else 0.0
                self.transfer_amount = self.total_amount if self.selected_method == "QR" else 0.0
            except:
                pass
        else:
            pass'''
# I'll just regex replace the if self.selected_method != "ผสม (Split)": part.
content = re.sub(r'        if self\.selected_method != ".*?\(Split\)":\n(.*?)        else:\n\s*pass', r'\1', content, flags=re.DOTALL)

# 7. Clean accept_payment
# Remove:
#        if self.selected_method == "ผสม (Split)":
#            if self.received_amount < self.total_amount:
#                QMessageBox.warning(self, "แจ้งเตือน", "ยอดเงินรับผสมยังไม่ครบ!")
#                return
content = re.sub(r'        if self\.selected_method == ".*?\(Split\)":.*?return\n', '', content, flags=re.DOTALL)

# Remove:
#        # For split, adjust cash_amount if there's change (change is returned from cash)
#        if self.selected_method == "ผสม (Split)" and self.change_amount > 0:
#            self.cash_amount = max(0, self.cash_amount - self.change_amount)
content = re.sub(r'        # For split, adjust cash_amount.*?\n\s*if self\.selected_method == ".*?\(Split\)".*?\n\s*self\.cash_amount = max\(0, self\.cash_amount - self\.change_amount\)\n', '', content, flags=re.DOTALL)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Removed Split logic from payment_dialog.py")
