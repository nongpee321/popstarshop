import os
import re

path = r'D:\pop-erp-food\ERPPOP-main\resources\views\pos\index.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

pattern = r'<a href="\{\{ route\(\'dashboard\'\) \}\}"[^>]*>\s*<i class="bi bi-grid"></i> ERP\s*</a>\s*<a href="\{\{ route\(\'pos\.compare\'\) \}\}"[^>]*>\s*<i class="bi bi-layout-split"></i> [^\n]*\s*</a>'
new_content = re.sub(pattern, '', content, flags=re.DOTALL)

with open(path, 'w', encoding='utf-8') as f:
    f.write(new_content)
print("Removed buttons")
