import re

path = r'D:\pop-erp-food\ERPPOP-main\resources\views\products\index.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Fix background #fff to var(--erp-surface, #fff)
content = content.replace('background:#fff;', 'background:var(--erp-surface, #fff);')
content = content.replace('background: #fff;', 'background: var(--erp-surface, #fff);')

# Fix bg-white on search icon
content = content.replace('class="input-group-text bg-white"', 'class="input-group-text"')

# The table rows also seem to have a white background but white text. Let's see if there is inline CSS or anything.
# product-table-card
# Actually the table itself might be transparent or white.
content = content.replace('.product-table-card { overflow:hidden; padding:0; border:1px solid var(--product-border); border-radius:14px; box-shadow:0 5px 18px rgba(15,51,74,.055); }', 
                          '.product-table-card { overflow:hidden; padding:0; border:1px solid var(--product-border); border-radius:14px; box-shadow:0 5px 18px rgba(15,51,74,.055); background:var(--erp-surface, #fff); }')

# Scale PLU background
content = content.replace('background:linear-gradient(100deg,var(--erp-surface-2),#fff 72%)', 'background:var(--erp-surface, #fff)')

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Updated product index UI")
