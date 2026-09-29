import re

path = r'D:\pop-erp-food\ERPPOP-main\resources\views\layout.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = 'html[data-color-mode="night"] .aa-panel, html[data-color-mode="night"] .tax-panel, html[data-color-mode="night"] .posd-panel, html[data-color-mode="night"] .content-card, html[data-color-mode="night"] .pos-card, html[data-color-mode="night"] .white-card, html[data-color-mode="night"] .notify-panel, html[data-color-mode="night"] .modal-content, html[data-color-mode="night"] .doc-filter-bar, html[data-color-mode="night"] .doc-tree-panel, html[data-color-mode="night"] .doc-book-chip {'

new_target = 'html[data-color-mode="night"] .card, html[data-color-mode="night"] .dropdown-menu, html[data-color-mode="night"] .bg-white, html[data-color-mode="night"] [class*="-panel"], html[data-color-mode="night"] [class*="-card"], html[data-color-mode="night"] [class*="-box"], html[data-color-mode="night"] [class*="-sheet"], html[data-color-mode="night"] .modal-content, html[data-color-mode="night"] .doc-filter-bar, html[data-color-mode="night"] .doc-book-chip {'

# Let's replace the whole card block to make it cleaner
old_card_rule_start = 'html[data-color-mode="night"] .card, html[data-color-mode="night"] .dropdown-menu'
if old_card_rule_start in content:
    # We will just replace that huge line
    regex = r'html\[data-color-mode="night"\] \.card,.*\{ background-color: #2a3441 !important; border-color: #3b4758 !important; color: #f1f5f9 !important; \}'
    content = re.sub(regex, new_target + ' background-color: #2a3441 !important; border-color: #3b4758 !important; color: #f1f5f9 !important; }', content)
    
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    print("Wildcard CSS updated successfully!")
else:
    print("Old card rule not found!")

