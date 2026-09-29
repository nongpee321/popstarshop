import os
import re

path = r'D:\pop-erp-food\ERPPOP-main\resources\views'
matches = set()
for root, dirs, files in os.walk(path):
    for file in files:
        if file.endswith('.blade.php'):
            full = os.path.join(root, file)
            with open(full, 'r', encoding='utf-8') as f:
                content = f.read()
                # Find classes with "background: #fff"
                found = re.findall(r'\.([a-zA-Z0-9_-]+)\s*\{[^}]*background:\s*#fff', content)
                for m in found:
                    matches.add(m)
print(list(matches))
