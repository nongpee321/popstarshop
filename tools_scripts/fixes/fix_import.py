import os
import re

path = r'D:\pop-erp-food\python-pos\ui\payment_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace('from utils.qr_generator import generate_promptpay', 'from ui.customer_display import generate_promptpay')

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
