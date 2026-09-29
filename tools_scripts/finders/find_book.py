import os

for root, dirs, files in os.walk(r'D:\pop-erp-food\ERPPOP-main\resources\views'):
    for file in files:
        if file.endswith('.blade.php'):
            path = os.path.join(root, file)
            with open(path, 'r', encoding='utf-8') as f:
                content = f.read()
                if 'คู่มือ' in content or 'bi-book' in content:
                    print(f"FOUND IN: {path}")
