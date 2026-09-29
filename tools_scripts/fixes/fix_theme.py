import re

with open(r'D:\pop-erp-food\ERPPOP-main\resources\views\layout.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_css = 'html[data-theme=\"popstar\"] .fa-rail { background: linear-gradient(180deg, #c62828 0%, #ffffff 100%); }'
new_css = 'html[data-theme=\"popstar\"] .fa-rail { background: linear-gradient(180deg, #c62828 0%, #8e0000 100%); }'

content = content.replace(old_css, new_css)

with open(r'D:\pop-erp-food\ERPPOP-main\resources\views\layout.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print('Done!')
