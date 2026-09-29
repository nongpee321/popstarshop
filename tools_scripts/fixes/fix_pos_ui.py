import re

path = r'D:\pop-erp-food\ERPPOP-main\resources\views\bplus-ops\pos-workbench.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Fix pos-install-card
content = content.replace('border: 1px solid #bbf7d0;', 'border: 1px solid var(--erp-border);')
content = content.replace('background: var(--erp-success-soft);', 'background: var(--erp-surface);')

# Fix text colors inside pos-install-copy
content = content.replace('color: #166534;', 'color: var(--erp-text);')
content = content.replace('color: #4b6353;', 'color: var(--erp-muted);')

# Fix pos-note (the yellow one)
content = content.replace('border: 1px solid #fde68a;', 'border: 1px solid var(--erp-border);')
content = content.replace('background: var(--erp-warning-soft);', 'background: var(--erp-surface);')
content = content.replace('color: var(--erp-warning-ink);', 'color: var(--erp-text);')

# Fix pos-light-btn
content = content.replace('background: #fff;', 'background: var(--erp-surface);')
content = content.replace('color: var(--erp-primary-dark);', 'color: var(--erp-text);')

# Fix pos-card (filter card)
content = content.replace('background: #fff;', 'background: var(--erp-surface);')

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Updated pos-workbench.blade.php colors")
