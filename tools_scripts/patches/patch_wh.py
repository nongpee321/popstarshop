import os

path = r'D:\pop-erp-food\ERPPOP-main\resources\views\wh\index.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Add data-color-mode to html
if '<html lang="th">' in content:
    content = content.replace('<html lang="th">', '<html lang="th" data-color-mode="{{ [\'color_mode\'] ?? (auth()->user()?->setting?->color_mode ?? \'light\') }}">')

# Add dark mode variables
dark_vars = '''
        html[data-color-mode="night"] {
            --bg: #111827;
            --panel: #1f2937;
            --panel-2: #374151;
            --line: #4b5563;
            --ink: #f3f4f6;
            --ink-soft: #9ca3af;
            --blue: #1d4ed8;
            --blue-deep: #1e3a8a;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }
        html[data-color-mode="night"] .scanbar input { background: #374151; color: #fff; border-color: #4b5563; }
        html[data-color-mode="night"] .wcard { background: var(--panel); border-color: var(--line); color: var(--ink); }
'''

if 'html, body { margin: 0; height: 100%; }' in content and 'data-color-mode="night"' not in content:
    content = content.replace('html, body { margin: 0; height: 100%; }', dark_vars + '\n        html, body { margin: 0; height: 100%; }')

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("wh/index.blade.php updated!")
