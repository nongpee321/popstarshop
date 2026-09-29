import os

path = r'D:\pop-erp-food\python-pos\ui\payment_dialog.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = '''    def calculate_split(self):
        try:
            c = float(self.inp_split_cash.text() or 0)
            t = float(self.inp_split_transfer.text() or 0)'''

replace = '''    def calculate_split(self):
        try:
            c_text = self.inp_split_cash.text().replace(',', '')
            t_text = self.inp_split_transfer.text().replace(',', '')
            c = float(c_text) if c_text else 0.0
            t = float(t_text) if t_text else 0.0'''

content = content.replace(target, replace)
with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("calculate_split updated!")
