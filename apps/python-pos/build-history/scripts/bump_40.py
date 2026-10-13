with open(r'd:\pop-erp-food\ERPPOP-main\apps\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    c = f.read()

c = c.replace('v1.11.39', 'v1.11.40')

with open(r'd:\pop-erp-food\ERPPOP-main\apps\python-pos\ui\main_window.py', 'w', encoding='utf-8') as f:
    f.write(c)
