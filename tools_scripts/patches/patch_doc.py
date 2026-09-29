import re

path = r'D:\pop-erp-food\ERPPOP-main\resources\views\layout.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = 'html[data-color-mode="night"] .aa-panel, html[data-color-mode="night"] .tax-panel, html[data-color-mode="night"] .posd-panel, html[data-color-mode="night"] .content-card, html[data-color-mode="night"] .pos-card, html[data-color-mode="night"] .white-card, html[data-color-mode="night"] .notify-panel, html[data-color-mode="night"] .modal-content {'

new_target = 'html[data-color-mode="night"] .aa-panel, html[data-color-mode="night"] .tax-panel, html[data-color-mode="night"] .posd-panel, html[data-color-mode="night"] .content-card, html[data-color-mode="night"] .pos-card, html[data-color-mode="night"] .white-card, html[data-color-mode="night"] .notify-panel, html[data-color-mode="night"] .modal-content, html[data-color-mode="night"] .doc-filter-bar, html[data-color-mode="night"] .doc-tree-panel, html[data-color-mode="night"] .doc-book-chip {'

if target in content:
    content = content.replace(target, new_target)
else:
    print("target not found!")
    
# also fix doc-tree-link.active and doc-book-chip
css_to_add = '''
        /* Document browser fixes for night mode */
        html[data-color-mode="night"] .doc-book-chip { background: #1e293b !important; color: #f1f5f9 !important; border-color: #3b4758 !important; }
        html[data-color-mode="night"] .doc-book-chip:hover { background: #2a3441 !important; }
        html[data-color-mode="night"] .doc-tree-link.active { background: #3b4758 !important; color: #fff !important; }
        html[data-color-mode="night"] .doc-grid thead th { background: #1e293b !important; color: #f1f5f9 !important; border-bottom: 2px solid #3b4758 !important; }
'''

# insert it before "html.subnav-collapsed"
content = content.replace('html.subnav-collapsed', css_to_add + '\n        html.subnav-collapsed')

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)

print("CSS for documents browser added!")
