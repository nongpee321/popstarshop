import os
path = r'D:\pop-erp-food\ERPPOP-main\resources\views'
for root, dirs, files in os.walk(path):
    for file in files:
        if file.endswith('.blade.php'):
            full = os.path.join(root, file)
            with open(full, 'r', encoding='utf-8') as f:
                if 'รายการควรเติม' in f.read():
                    print(full)
