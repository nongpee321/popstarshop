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

for root, dirs, files in os.walk(r'D:\pop-erp-food\python-pos'):
    if 'venv' in root or 'build' in root or '__pycache__' in root:
        continue
    for file in files:
        if file.endswith('.py'):
            search_in_file(os.path.join(root, file))
