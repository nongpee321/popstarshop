path = r'D:\pop-erp-food\ERPPOP-main\resources\views\wh\index.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace("data-color-mode=\"{{ ['color_mode']", "data-color-mode=\"{{ ['color_mode']")

# Also some pages don't pass ! We should use a safe coalescing that won't throw an error.
safe_str = "data-color-mode=\"{{ auth()->user()?->setting?->color_mode ?? 'light' }}\""
content = content.replace("data-color-mode=\"{{ ['color_mode'] ?? (auth()->user()?->setting?->color_mode ?? 'light') }}\"", safe_str)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print('Fixed typo safely!')
