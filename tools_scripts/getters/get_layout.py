with open(r'D:\pop-erp-food\ERPPOP-main\resources\views\layout.blade.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()
for i in range(1580, 1605):
    print(lines[i].rstrip().encode('unicode_escape').decode('utf-8'))
