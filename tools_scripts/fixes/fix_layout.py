import re

path = r'D:\pop-erp-food\ERPPOP-main\resources\views\layout.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace .content-card background
content = re.sub(r'(\.content-card\s*\{[^}]*?)background:\s*#fff;', r'\1background: var(--erp-surface, #fff);', content)

# Check if there are other card backgrounds
content = re.sub(r'(\.card\s*\{[^}]*?)background:\s*#fff;', r'\1background: var(--erp-surface, #fff);', content)
content = re.sub(r'(\.panel-card\s*\{[^}]*?)background:\s*#fff;', r'\1background: var(--erp-surface, #fff);', content)
content = re.sub(r'(\.set-card\s*\{[^}]*?)background:\s*#fff;', r'\1background: var(--erp-surface, #fff);', content)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Fixed layout cards")
