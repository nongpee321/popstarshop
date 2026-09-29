import os

path = r'D:\pop-erp-food\python-pos\ui\main_window.py'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Fix the broken f-string
broken_str1 = 'f"เซิร์ฟเวอร์แจ้งว่า: {err_msg}\n\nหากต้องการปิดแบบออฟไลน์ กรุณายกเลิกการเชื่อมต่อเน็ต"'
fixed_str1 = 'f"เซิร์ฟเวอร์แจ้งว่า: {err_msg}\\n\\nหากต้องการปิดแบบออฟไลน์ กรุณายกเลิกการเชื่อมต่อเน็ต"'
content = content.replace(broken_str1, fixed_str1)

broken_str2 = 'f"เซิร์ฟเวอร์แจ้งว่า: {err_msg}\n\nหากต้องการเปิดแบบออฟไลน์ กรุณายกเลิกการเชื่อมต่อเน็ต"'
fixed_str2 = 'f"เซิร์ฟเวอร์แจ้งว่า: {err_msg}\\n\\nหากต้องการเปิดแบบออฟไลน์ กรุณายกเลิกการเชื่อมต่อเน็ต"'
content = content.replace(broken_str2, fixed_str2)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Syntax fixed!")
