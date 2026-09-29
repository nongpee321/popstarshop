with open(r'D:\pop-erp-food\python-pos\ui\payment_dialog.py', 'r', encoding='utf-8') as f:
    text = f.read()

text = text.replace('self.update_displays()        else:', 'self.update_displays()\n        else:')
with open(r'D:\pop-erp-food\python-pos\ui\payment_dialog.py', 'w', encoding='utf-8') as f:
    f.write(text)
