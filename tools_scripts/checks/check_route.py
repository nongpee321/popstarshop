import os

for root, dirs, files in os.walk(r'D:\pop-erp-food\ERPPOP-main'):
    if 'vendor' in root or 'node_modules' in root or '.git' in root or 'storage' in root:
        continue
    for file in files:
        if file.endswith('.php'):
            try:
                with open(os.path.join(root, file), 'r', encoding='utf-8') as f:
                    content = f.read()
                    if 'shift/close' in content:
                        print(f"FOUND IN: {os.path.join(root, file)}")
            except:
                pass
