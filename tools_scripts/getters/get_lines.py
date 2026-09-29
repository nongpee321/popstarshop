with open(r'D:\pop-erp-food\ERPPOP-main\resources\views\layout.blade.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i in range(29, 65):
    if i < len(lines):
        print(lines[i].rstrip())
