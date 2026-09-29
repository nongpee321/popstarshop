import sys

path = r'D:\pop-erp-food\ERPPOP-main\resources\views\auth\login.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = '<br><strong style="color:var(--primary)">[ กำลังเชื่อมต่อฐานข้อมูลเครื่อง Local ]</strong>'
if target in content:
    content = content.replace(target, '')
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    print("Removed!")
else:
    print("Target not found. Let's try broader replacement.")
    # Attempt to replace just the text and the strong tag
    import re
    content = re.sub(r'<br>\s*<strong[^>]*>\[ กำลังเชื่อมต่อฐานข้อมูลเครื่อง Local \]</strong\s*>', '', content)
    content = content.replace('[ กำลังเชื่อมต่อฐานข้อมูลเครื่อง Local ]', '')
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    print("Fallback applied!")
