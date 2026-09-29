import os
import re

path = r'D:\pop-erp-food\python-pos\ui\payment_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Remove the initialization
content = re.sub(
    r'# Show QR Split Option\s*self\.show_split_qr_checkbox = QCheckBox.*?\s*self\.show_split_qr_checkbox\.setStyleSheet.*?\s*self\.show_split_qr_checkbox\.stateChanged\.connect.*?\s*self\.show_split_qr_checkbox\.hide\(\)\s*right_layout\.addWidget\(self\.show_split_qr_checkbox\)',
    '',
    content,
    flags=re.DOTALL
)

# Remove the block in set_method hiding/showing it
content = re.sub(r"if hasattr\(self, 'show_split_qr_checkbox'\):\s*self\.show_split_qr_checkbox\.hide\(\)\s*self\.show_split_qr_checkbox\.setChecked\(False\)", "", content)
content = re.sub(r"if hasattr\(self, 'show_split_qr_checkbox'\):\s*self\.show_split_qr_checkbox\.show\(\)", "", content)

# Remove call from calculate_split
content = re.sub(
    r"if hasattr\(self, 'show_split_qr_checkbox'\) and self\.show_split_qr_checkbox\.isChecked\(\):\s*self\.toggle_split_qr\(2\)",
    "",
    content
)

# Remove toggle_split_qr entirely
content = re.sub(
    r'    def toggle_split_qr\(self, state\):.*?def accept_payment\(self\):',
    '    def accept_payment(self):',
    content,
    flags=re.DOTALL
)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Removed show_split_qr_checkbox and toggle_split_qr")
