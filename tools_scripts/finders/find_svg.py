import os
path = r'D:\pop-erp-food\ERPPOP-main'
for root, dirs, files in os.walk(path):
    if 'vendor' in root or 'node_modules' in root or '.git' in root or 'storage' in root:
        continue
    for file in files:
        if file.endswith(('.php', '.html', '.js')):
            full = os.path.join(root, file)
            try:
                with open(full, 'r', encoding='utf-8') as f:
                    if 'logo-jet-erp-mark.svg' in f.read():
                        print(full)
            except:
                pass
