import ast

with open(r'D:\pop-erp-food\python-pos\ui\payment_dialog.py', 'r', encoding='utf-8') as f:
    source = f.read()

try:
    ast.parse(source)
    print("Syntax OK")
except Exception as e:
    print(f"Error: {e}")
