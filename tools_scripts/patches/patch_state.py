import os
import re

path = r'D:\pop-erp-food\python-pos\ui\payment_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = '''    def toggle_split_qr(self, state):
        if not hasattr(self.parent(), 'customer_display') or not self.parent().customer_display:
            return
            
        if state == Qt.Checked and self.selected_method == "ผสม (Split)":'''

replace = '''    def toggle_split_qr(self, state):
        if not hasattr(self.parent(), 'customer_display') or not self.parent().customer_display:
            return
            
        is_checked = self.show_split_qr_checkbox.isChecked()
        if is_checked and self.selected_method == "ผสม (Split)":'''

content = content.replace(target, replace)

# Also fix the call inside calculate_split just in case I hardcoded Qt.Checked
calc_target = '''self.toggle_split_qr(Qt.Checked)'''
calc_replace = '''self.toggle_split_qr(2)'''
content = content.replace(calc_target, calc_replace)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Fixed toggle_split_qr state check!")
