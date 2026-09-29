import os
for root, dirs, files in os.walk(r'D:\pop-erp-food\ERPPOP-main\resources'):
    for file in files:
        if file.endswith('.php') or file.endswith('.css'):
            path = os.path.join(root, file)
            with open(path, 'r', encoding='utf-8') as f:
                content = f.read()
                if '.status-summary' in content:
                    print(f"FOUND IN: {path}")
