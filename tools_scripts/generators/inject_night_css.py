import sys

path = r'D:\pop-erp-food\ERPPOP-main\resources\views\layout.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

new_css = '''html[data-color-mode="night"] .table input, html[data-color-mode="night"] .table select, html[data-color-mode="night"] .table textarea { background-color: #1e293b !important; color: #f1f5f9 !important; border-color: #3b4758 !important; }

        /* Fix Disabled form-controls in night mode */
        html[data-color-mode="night"] .form-control:disabled,
        html[data-color-mode="night"] .form-select:disabled,
        html[data-color-mode="night"] .form-control[readonly] { background-color: #111827 !important; color: #94a3b8 !important; border-color: #3b4758 !important; }
        
        /* Fix btn-light (4M Button) in night mode */
        html[data-color-mode="night"] .btn-light { background-color: #1e293b !important; color: #f1f5f9 !important; border-color: #3b4758 !important; }
        html[data-color-mode="night"] .btn-light:hover { background-color: #2a3441 !important; color: #fff !important; }'''

content = content.replace('html[data-color-mode="night"] .table input, html[data-color-mode="night"] .table select, html[data-color-mode="night"] .table textarea { background-color: #1e293b !important; color: #f1f5f9 !important; border-color: #3b4758 !important; }', new_css)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Injected dark mode CSS fixes.")
