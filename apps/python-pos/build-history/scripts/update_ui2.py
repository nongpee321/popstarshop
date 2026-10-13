import re

with open(r'd:\pop-erp-food\ERPPOP-main\apps\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    c = f.read()

# Fix the duplicate hide
c = c.replace('self.branch_label.hide()\n        self.branch_label.setStyleSheet', 'self.branch_label.setStyleSheet')

# Show when shift is opened (where the button is changed to Shift Close)
c = re.sub(
    r'(self\.shift_btn\.setText\(".*?ปิดกะ \(Shift Close\)"\))',
    r'\1\n            if hasattr(self, "branch_label"): self.branch_label.show()',
    c
)

# Hide when shift is closed (where the button is changed to Shift Open)
c = re.sub(
    r'(self\.shift_btn\.setText\(".*?เปิดกะ \(Shift Open\)"\))',
    r'\1\n            if hasattr(self, "branch_label"): self.branch_label.hide()',
    c
)

with open(r'd:\pop-erp-food\ERPPOP-main\apps\python-pos\ui\main_window.py', 'w', encoding='utf-8') as f:
    f.write(c)
