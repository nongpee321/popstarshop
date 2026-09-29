import os

target = 'กำลังเชื่อมต่อ'

def search_in_file(path):
    try:
        with open(path, 'r', encoding='utf-8') as f:
            content = f.read()
            if target in content:
                print(f"FOUND IN: {path}")
    except:
        pass

for root, dirs, files in os.walk(r'D:\pop-erp-food\ERPPOP-main'):
    if 'vendor' in root or 'node_modules' in root or '.git' in root or 'storage' in root:
        continue
    for file in files:
        if file.endswith('.php') or file.endswith('.blade.php') or file.endswith('.js') or file.endswith('.vue'):
            search_in_file(os.path.join(root, file))
