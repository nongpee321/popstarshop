with open(r'D:\pop-erp-food\ERPPOP-main\resources\views\layout.blade.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for l in lines[70:85]:
    print(l.rstrip().encode('unicode_escape').decode('utf-8'))
