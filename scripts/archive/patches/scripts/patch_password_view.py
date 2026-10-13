import re
file_path = r"d:\pop-erp-food\ERPPOP-main\resources\views\auth\change-password.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

old_text = "รหัสผ่านต้องยาวอย่างน้อย 8 ตัว มีตัวอักษรพิมพ์เล็ก พิมพ์ใหญ่ และตัวเลข"
new_text = "รหัสผ่านต้องยาวอย่างน้อย 6 ตัว (สามารถใช้ตัวเลขล้วนได้ เช่น 123456)"
content = content.replace(old_text, new_text)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated password rules text in view.")
