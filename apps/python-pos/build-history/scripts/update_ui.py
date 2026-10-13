with open(r'd:\pop-erp-food\ERPPOP-main\apps\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    c = f.read()

c = c.replace(
    'self.branch_label.setStyleSheet("font-size: 14px; font-weight: bold; color: #1e3a8a; background: #dbeafe; padding: 4px 10px; border-radius: 12px; border: 1px solid #93c5fd;")',
    'self.branch_label.setStyleSheet("font-size: 14px; font-weight: bold; color: #1e3a8a; background: #dbeafe; padding: 4px 10px; border-radius: 12px; border: 1px solid #93c5fd;")\n        self.branch_label.hide()'
)

# Show when shift is opened
c = c.replace(
    'self.shift_btn.setText("🔕 ปิดกะ (Shift Close)")',
    'self.shift_btn.setText("🔕 ปิดกะ (Shift Close)")\n            if hasattr(self, "branch_label"):\n                self.branch_label.show()'
)

# Hide when shift is closed
c = c.replace(
    'self.shift_btn.setText("🔔 เปิดกะ (Shift Open)")',
    'self.shift_btn.setText("🔔 เปิดกะ (Shift Open)")\n            if hasattr(self, "branch_label"):\n                self.branch_label.hide()'
)

# Bump version to 1.11.39
c = c.replace('v1.11.38', 'v1.11.39')

with open(r'd:\pop-erp-food\ERPPOP-main\apps\python-pos\ui\main_window.py', 'w', encoding='utf-8') as f:
    f.write(c)
