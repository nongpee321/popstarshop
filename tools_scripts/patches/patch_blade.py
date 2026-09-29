import os

path = r'D:\pop-erp-food\ERPPOP-main\resources\views\bplus-ops\pos-workbench.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

target = '<strong>วิธีติดตั้งแอป POS v{{  }}</strong>'
new_target = '<strong>วิธีติดตั้งแอป POS v{{  ? ["version"] :  }}</strong>'

if target in content:
    content = content.replace(target, new_target)
    
    # Also fix the text below it since it's no longer a ZIP file!
    old_text = 'ดาวน์โหลดไฟล์ ZIP แล้วแตกไฟล์ทั้งโฟลเดอร์ จากนั้นเปิด'
    new_text = 'ดาวน์โหลดไฟล์ .exe จากนั้นดับเบิลคลิกเพื่อติดตั้ง แล้วเปิด'
    content = content.replace(old_text, new_text)

    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    print("Updated pos-workbench.blade.php!")
else:
    print("Target not found.")
