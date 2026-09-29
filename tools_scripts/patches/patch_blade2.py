import os
import re

path = r'D:\pop-erp-food\ERPPOP-main\resources\views\bplus-ops\pos-workbench.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the version variable
content = re.sub(r'POS v\{\{\s*\\s*\}\}', r'POS v{{  ? ["version"] :  }}', content)

# Replace the ZIP text (just look for ZIP)
content = re.sub(r'ดาวน์โหลดไฟล์ ZIP แล้วแตกไฟล์ทั้งโฟลเดอร์ จากนั้นเปิด', r'ดาวน์โหลดไฟล์ตัวติดตั้ง (.exe) จากนั้นดับเบิลคลิกเพื่อรัน แล้วเปิด', content)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated pos-workbench.blade.php with regex!")
