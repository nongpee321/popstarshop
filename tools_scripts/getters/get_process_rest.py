import re

with open(r'D:\pop-erp-food\python-pos\ui\main_window.py', 'r', encoding='utf-8') as f:
    content = f.read()

match = re.search(r'session\.commit\(\)(.*?def )', content, re.DOTALL)
if match:
    print(match.group(1))
