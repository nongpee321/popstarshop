import re

path = r'D:\pop-erp-food\ERPPOP-main\resources\views\wh\index.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace hardcoded #fff and light colors with css variables in the original stylesheet
content = content.replace('background: #fff;', 'background: var(--panel);')
content = content.replace('background: #eaf6ff;', 'background: var(--blue-soft, #eaf6ff);')
content = content.replace('background: #f2fbf1;', 'background: var(--green-soft, #f2fbf1);')

# Let's add night mode definitions for those soft variables
night_mode_vars = '''
        html[data-color-mode="night"] {
            --bg: #111827;
            --panel: #1f2937;
            --panel-2: #374151;
            --line: #4b5563;
            --ink: #f3f4f6;
            --ink-soft: #9ca3af;
            --blue: #3b82f6;
            --blue-deep: #1e3a8a;
            --green: #10b981;
            --green-deep: #047857;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            --blue-soft: #1e3a8a;
            --green-soft: #064e3b;
        }
'''

# Replace the old night mode vars I added earlier
old_night_vars = re.search(r'html\[data-color-mode="night"\]\s*\{[^}]+\}\s*html\[data-color-mode="night"\] \.scanbar input \{[^}]+\}\s*html\[data-color-mode="night"\] \.wcard \{[^}]+\}', content)
if old_night_vars:
    content = content.replace(old_night_vars.group(0), night_mode_vars)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Mobile warehouse CSS cleaned!")
