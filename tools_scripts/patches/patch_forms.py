import re

path = r'D:\pop-erp-food\ERPPOP-main\resources\views\layout.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# I will add universal input fields to night mode:
new_css = '''
        html[data-color-mode="night"] .form-control:not(:disabled):not([readonly]),
        html[data-color-mode="night"] .form-select:not(:disabled) {
            background-color: #1e293b !important;
            color: #f1f5f9 !important;
            border-color: #3b4758 !important;
        }
        
        html[data-color-mode="night"] .form-control:focus,
        html[data-color-mode="night"] .form-select:focus {
            background-color: #2a3441 !important;
            color: #fff !important;
            border-color: var(--erp-primary) !important;
        }
'''

target = '/* Fix Disabled form-controls in night mode */'
if target in content and '.form-control:not(:disabled)' not in content:
    content = content.replace(target, new_css + '\n          ' + target)
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    print("Form controls CSS updated!")
else:
    print("Already updated or target not found.")

