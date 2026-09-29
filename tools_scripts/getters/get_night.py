with open(r'D:\pop-erp-food\ERPPOP-main\resources\views\layout.blade.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, l in enumerate(lines):
    if 'data-color-mode="night"' in l:
        print(f"Line {i+1}: {l.strip()}")
