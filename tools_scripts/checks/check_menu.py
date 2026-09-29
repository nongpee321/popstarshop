import re

with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    content = f.read()

match = re.search(r'action_settings = main_menu\.addAction\("([^"]+)"\)', content)
with open('menu_out.txt', 'w', encoding='utf-8') as f:
    if match:
        f.write(f"FOUND: {match.group(1)}")
    else:
        f.write("NOT FOUND")
