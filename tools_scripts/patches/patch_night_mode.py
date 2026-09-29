import re

path = r'D:\pop-erp-food\ERPPOP-main\resources\views\layout.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = 'html[data-color-mode="night"] .card, html[data-color-mode="night"] .dropdown-menu, html[data-color-mode="night"] .bg-white, html[data-color-mode="night"] .info-box, html[data-color-mode="night"] .panel-card, html[data-color-mode="night"] .metric-card, html[data-color-mode="night"] .dashboard-filter, html[data-color-mode="night"] .executive-hero {'

new_target = 'html[data-color-mode="night"] .card, html[data-color-mode="night"] .dropdown-menu, html[data-color-mode="night"] .bg-white, html[data-color-mode="night"] .info-box, html[data-color-mode="night"] .panel-card, html[data-color-mode="night"] .metric-card, html[data-color-mode="night"] .dashboard-filter, html[data-color-mode="night"] .executive-hero, html[data-color-mode="night"] .aa-panel, html[data-color-mode="night"] .tax-panel, html[data-color-mode="night"] .posd-panel, html[data-color-mode="night"] .content-card, html[data-color-mode="night"] .pos-card, html[data-color-mode="night"] .white-card, html[data-color-mode="night"] .notify-panel, html[data-color-mode="night"] .modal-content {'

if target in content:
    content = content.replace(target, new_target)
    
    # Also add label rule next to text-muted
    target2 = 'html[data-color-mode="night"] .text-dark, html[data-color-mode="night"] .text-muted, html[data-color-mode="night"] .text-black, html[data-color-mode="night"] p, html[data-color-mode="night"] a {'
    new_target2 = 'html[data-color-mode="night"] label, html[data-color-mode="night"] .text-dark, html[data-color-mode="night"] .text-muted, html[data-color-mode="night"] .text-black, html[data-color-mode="night"] p, html[data-color-mode="night"] a {'
    if target2 in content:
        content = content.replace(target2, new_target2)
    else:
        print("target2 not found!")
        
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    print("CSS updated successfully.")
else:
    print("Target 1 not found!")

