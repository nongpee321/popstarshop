import os
import re

path = r'D:\pop-erp-food\python-pos\ui\payment_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = '''            self.change_lbl.setText(f"เงินทอน: ฿ {self.change_amount:,.2f}")
        except:
            pass'''
replace = '''            self.change_lbl.setText(f"เงินทอน: ฿ {self.change_amount:,.2f}")
            if hasattr(self, 'show_split_qr_checkbox') and self.show_split_qr_checkbox.isChecked():
                self.toggle_split_qr(Qt.Checked)
        except:
            pass'''

content = content.replace(target, replace)
with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("calculate_split patched!")
