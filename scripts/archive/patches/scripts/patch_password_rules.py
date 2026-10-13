import re
file_path = r"d:\pop-erp-food\ERPPOP-main\app\Http\Controllers\AuthController.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

old_rule = "'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],"
new_rule = "'password' => ['required', 'confirmed', Password::min(6)], // ลดเงื่อนไขความซับซ้อนลง"
content = content.replace(old_rule, new_rule)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated password rules in AuthController.")
