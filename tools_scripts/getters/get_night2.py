import re

path = r'D:\pop-erp-food\ERPPOP-main\resources\views\layout.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

matches = re.finditer(r'html\[data-color-mode="night"\].*?\}', content, re.DOTALL | re.MULTILINE)
for m in matches:
    print(m.group(0))
